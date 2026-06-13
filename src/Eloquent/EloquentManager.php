<?php

namespace TomShaw\Mediable\Eloquent;

use Exception;
use GdImage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\{Builder, Collection};
use Illuminate\Support\Facades\{Config, DB, Storage};
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use TomShaw\Mediable\Concerns\AttachmentState;
use TomShaw\Mediable\Exceptions\MediaBrowserException;
use TomShaw\Mediable\Models\Attachment;

class EloquentManager
{
    /**
     * @param  Builder<Attachment>  $query
     */
    public function __construct(
        public Builder $query,
    ) {}

    public function load(int $id): ?Attachment
    {
        return Attachment::find($id);
    }

    public function query(string $orderBy, string $orderDir, ?string $mimeType = null): void
    {
        if ($mimeType) {
            $this->query = Attachment::where('hidden', '=', false)->where('file_type', '=', $mimeType)->orderBy($orderBy, $orderDir);
        } else {
            $this->query = Attachment::where('hidden', '=', false)->orderBy($orderBy, $orderDir);
        }
    }

    /**
     * @param  array<int, TemporaryUploadedFile|null>  $files
     */
    public function create(array $files): void
    {
        $diskConfig = $this->getAndValidateDisk(Config::string('mediable.disk'));

        $disk = $diskConfig['disk'];

        $folder = Config::string('mediable.folder');

        foreach ($files as $file) {
            if (is_null($file)) {
                continue;
            }

            $fileName = $this->prepareFileName($file->getClientOriginalName());

            $storagePath = $file->storePubliclyAs(path: $folder, name: $fileName, options: $disk);

            if ($storagePath === false) {
                continue;
            }

            $fullPath = Storage::disk($disk)->path($storagePath);

            $data = $this->createDataArray($file, $storagePath, $fileName);

            try {
                Attachment::create($data);
            } catch (Exception $e) {
                throw new MediaBrowserException($e->getMessage());
            }

            if (str_starts_with($file->getMimeType(), 'image/')) {

                $contents = file_get_contents($fullPath);

                if ($contents === false) {
                    continue;
                }

                $image = imagecreatefromstring($contents);

                if ($image === false) {
                    continue;
                }

                if (config('mediable.create_webp')) {

                    try {
                        $path = $this->createImageResource($image, $storagePath, $disk, 'image/webp', Config::integer('mediable.webp_quality'));
                    } catch (Exception $e) {
                        continue;
                    }

                    $create = $this->createDataArray($file, $path, $fileName);

                    $create['file_type'] = 'image/webp';

                    if (Storage::disk($disk)->exists($path)) {
                        $create['file_dir'] = $storagePath;
                        $create['title'] = pathinfo($path, PATHINFO_FILENAME);
                        $create['file_name'] = pathinfo($path, PATHINFO_BASENAME);
                        $create['file_original_name'] = pathinfo($path, PATHINFO_BASENAME);
                        $create['file_size'] = Storage::disk($disk)->size($path);
                    }

                    Attachment::create($create);
                }

                if (config('mediable.create_avif')) {

                    try {
                        $path = $this->createImageResource($image, $storagePath, $disk, 'image/avif', Config::integer('mediable.avif_quality'));
                    } catch (Exception $e) {
                        continue;
                    }

                    $create = $this->createDataArray($file, $path, $fileName);

                    $create['file_type'] = 'image/avif';

                    if (Storage::disk($disk)->exists($path)) {
                        $create['file_dir'] = $storagePath;
                        $create['title'] = pathinfo($path, PATHINFO_FILENAME);
                        $create['file_name'] = pathinfo($path, PATHINFO_BASENAME);
                        $create['file_original_name'] = pathinfo($path, PATHINFO_BASENAME);
                        $create['file_size'] = Storage::disk($disk)->size($path);
                    }

                    Attachment::create($create);
                }

                imagedestroy($image);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function createDataArray(TemporaryUploadedFile $file, string $storagePath, string $fileName): array
    {
        return [
            'file_name' => $fileName,
            'file_original_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_dir' => $storagePath,
            'file_url' => asset('storage/'.$storagePath),
            'title' => pathinfo($fileName, PATHINFO_FILENAME),
        ];
    }

    private function createImageResource(GdImage $image, string $stored, string $disk, string $type = 'image/webp', int $quality = -1): string
    {
        $extension = ($type === 'image/webp') ? 'webp' : 'avif';

        $directory = Config::string('mediable.folder');
        $baseName = pathinfo($stored, PATHINFO_FILENAME);
        $path = $directory.'/'.$baseName.'.'.$extension;

        ob_start();
        if ($type === 'image/webp') {
            imagewebp($image, null, $quality);
        } else {
            imageavif($image, null, $quality);
        }
        $content = ob_get_clean() ?: '';

        Storage::disk($disk)->put($path, $content);

        return $path;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data = []): void
    {
        try {
            Attachment::where('id', $id)->update($data);
        } catch (Exception $e) {
            throw new MediaBrowserException($e->getMessage());
        }
    }

    public function enable(int $id): void
    {
        try {
            Attachment::where('id', $id)->update([
                'hidden' => false,
            ]);
        } catch (Exception $e) {
            throw new MediaBrowserException($e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        try {
            Attachment::findOrFail($id)->delete();
        } catch (Exception $e) {
            throw new MediaBrowserException($e->getMessage());
        }
    }

    public function garbage(): void
    {
        $disk = $this->getAndValidateDisk(Config::string('mediable.disk'))['disk'];

        try {
            Attachment::where('hidden', true)->chunkById(100, function ($attachments) use ($disk): void {
                foreach ($attachments as $attachment) {
                    $fileDir = $attachment->file_dir;

                    if ($fileDir && Storage::disk($disk)->exists($fileDir)) {
                        Storage::disk($disk)->delete($fileDir);
                    }

                    $attachment->delete();
                }
            });
        } catch (Exception $e) {
            throw new MediaBrowserException($e->getMessage());
        }
    }

    public function copyImageFromTo(string $source, string $destination): string
    {
        $diskConfig = $this->getAndValidateDisk(Config::string('mediable.disk'));

        $disk = $diskConfig['disk'];

        if (! Storage::disk($disk)->exists($source)) {
            throw new MediaBrowserException('Source file not found.');
        }

        if (Storage::disk($disk)->exists($destination)) {
            throw new MediaBrowserException('Destination file already exists.');
        }

        try {
            Storage::disk($disk)->copy($source, $destination);
        } catch (Exception $e) {
            throw new MediaBrowserException($e->getMessage());
        }

        return $destination;
    }

    public function saveImageToDatabase(AttachmentState $attachment, string $destination): Attachment
    {
        $diskConfig = $this->getAndValidateDisk(Config::string('mediable.disk'));

        $disk = $diskConfig['disk'];

        $filePath = Storage::disk($disk)->path($destination);

        $file = $this->fileObject($filePath);

        $create = [
            'file_name' => $file->getFilename(),
            'file_original_name' => $file->getFilename(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_dir' => $destination,
            'file_url' => asset('storage/'.$destination),
            'title' => $attachment->title,
            'caption' => $attachment->caption,
            'description' => $attachment->description,
            'hidden' => true,
        ];

        try {
            return Attachment::create($create);
        } catch (Exception $e) {
            throw new MediaBrowserException($e->getMessage());
        }
    }

    public function getFilePath(string $filename): string
    {
        $diskConfig = $this->getAndValidateDisk(Config::string('mediable.disk'));

        $disk = $diskConfig['disk'];

        return Storage::disk($disk)->path($filename);
    }

    public function fileObject(string $path, bool $checkPath = true): File
    {
        try {
            $file = new File($path, $checkPath);
        } catch (Exception $e) {
            throw new MediaBrowserException($e->getMessage());
        }

        return $file;
    }

    public function randomizeName(string $source): string
    {
        $extension = pathinfo($source, PATHINFO_EXTENSION);

        $destinationFilename = Str::random(12).'.'.$extension;

        $directory = pathinfo($source, PATHINFO_DIRNAME);

        return $directory.DIRECTORY_SEPARATOR.$destinationFilename;
    }

    public function prepareFileName(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $name = pathinfo($fileName, PATHINFO_FILENAME);

        $slug = Str::slug($name);

        return $slug.'-'.time().'.'.$extension;
    }

    /**
     * @param  list<string>  $searchColumns
     */
    public function search(string $searchTerm, array $searchColumns): void
    {
        if ($searchTerm) {
            $this->query->where(function (Builder $query) use ($searchTerm, $searchColumns): void {
                foreach ($searchColumns as $index => $column) {
                    if ($index === 0) {
                        $query->where($column, 'like', "%{$searchTerm}%");
                    } else {
                        $query->orWhere($column, 'like', "%{$searchTerm}%");
                    }
                }
            });
        }
    }

    /**
     * @return list<string>
     */
    public function uniqueMimes(): array
    {
        $types = Attachment::query()->distinct()->orderBy('file_type')->pluck('file_type')->all();

        return array_values(array_filter($types, 'is_string'));
    }

    /**
     * @return LengthAwarePaginator<int, Attachment>
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->query->paginate($perPage);
    }

    /**
     * @return array{disk: string, driver: mixed}
     */
    public function getAndValidateDisk(string $name): array
    {
        $disks = Config::array('filesystems.disks');

        if (! array_key_exists($name, $disks)) {
            throw new MediaBrowserException('Storage disk not found.');
        }

        return ['disk' => $name, 'driver' => $disks[$name]];
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function getMimeTypeStats(): Collection
    {
        return Attachment::select('file_type', DB::raw('count(*) as total'), DB::raw('sum(file_size) as total_size'))
            ->groupBy('file_type')
            ->get();
    }

    public function getMimeTypeTotals(): ?Attachment
    {
        return Attachment::select(DB::raw('count(*) as total'), DB::raw('sum(file_size) as total_size'))->first();
    }
}
