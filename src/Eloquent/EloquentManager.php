<?php

namespace TomShaw\Mediable\Eloquent;

use Exception;
use GdImage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use TomShaw\Mediable\Concerns\AttachmentState;
use TomShaw\Mediable\Exceptions\MediaBrowserException;
use TomShaw\Mediable\Models\Attachment;

class EloquentManager
{
    public function __construct(
        public Builder $query,
    ) {
    }

    public function load(int $id)
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

    public function create(array $files): void
    {
        $diskConfig = $this->getAndValidateDisk(config('mediable.disk'));

        $disk = $diskConfig['disk'];

        $driver = $diskConfig['driver'];

        foreach ($files as $file) {
            if (is_null($file)) {
                continue;
            }

            $store = $file->store($disk);

            $create = $this->createDataArray($file, $store, $driver);

            try {
                Attachment::create($create);
            } catch (Exception $e) {
                throw new MediaBrowserException($e->getMessage());
            }

            if (str_starts_with($file->getMimeType(), 'image/')) {

                try {
                    $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
                } catch (Exception $e) {
                    continue;
                }

                if (config('mediable.create_webp')) {

                    try {
                        $path = $this->createImageResource($image, $store, $disk, 'image/webp', config('mediable.webp_quality'));
                    } catch (Exception $e) {
                        continue;
                    }

                    $create = $this->createDataArray($file, $path, $driver);

                    $create['file_type'] = 'image/webp';

                    if (Storage::exists($path)) {
                        $create['file_size'] = Storage::size($path);
                    }

                    Attachment::create($create);
                }

                if (config('mediable.create_avif')) {

                    try {
                        $path = $this->createImageResource($image, $store, $disk, 'image/avif', config('mediable.avif_quality'));
                    } catch (Exception $e) {
                        continue;
                    }

                    $create = $this->createDataArray($file, $path, $driver);

                    $create['file_type'] = 'image/avif';

                    if (Storage::exists($path)) {
                        $create['file_size'] = Storage::size($path);
                    }

                    Attachment::create($create);
                }

                imagedestroy($image);
            }
        }
    }

    private function createDataArray(TemporaryUploadedFile $file, string $store, array $driver): array
    {
        return [
            'file_name' => $file->getFilename(),
            'file_original_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_dir' => $store,
            'file_url' => $driver['url'].'/'.basename($store),
            'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
        ];
    }

    private function createImageResource(GdImage $image, string $stored, string $disk, string $type = 'image/webp', int $quality = -1)
    {
        $extension = ($type === 'image/webp') ? 'webp' : 'avif';

        $path = pathinfo($stored, PATHINFO_FILENAME).'.'.$extension;

        ob_start();
        if ($type === 'image/webp') {
            imagewebp($image, null, $quality);
        } else {
            imageavif($image, null, $quality);
        }
        $content = ob_get_clean();

        Storage::disk($disk)->put($path, $content);

        $path = dirname($stored).'/'.pathinfo($stored, PATHINFO_FILENAME).'.'.$extension;

        return $path;
    }

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
            Attachment::find($id)->delete();
        } catch (Exception $e) {
            throw new MediaBrowserException($e->getMessage());
        }
    }

    public function garbage(): void
    {
        try {
            $attachments = Attachment::where('hidden', true)->get();

            foreach ($attachments as $attachment) {
                $fileDir = $attachment->file_dir;

                if (Storage::exists($fileDir)) {
                    Storage::delete($fileDir);
                }

                $attachment->delete();
            }
        } catch (Exception $e) {
            throw new MediaBrowserException($e->getMessage());
        }
    }

    public function copyImageFromTo(string $source, string $destination): string
    {
        if (! Storage::exists($source)) {
            throw new MediaBrowserException('Source file not found.');
        }

        if (Storage::exists($destination)) {
            throw new MediaBrowserException('Destination file already exists.');
        }

        try {
            Storage::copy($source, $destination);
        } catch (Exception $e) {
            throw new MediaBrowserException($e->getMessage());
        }

        return $destination;
    }

    public function saveImageToDatabase(AttachmentState $attachment, string $destination): Attachment
    {
        $diskConfig = $this->getAndValidateDisk(config('mediable.disk'));

        $driver = $diskConfig['driver'];

        $filepath = Storage::path($destination);

        $file = $this->fileObject($filepath);

        $create = [
            'file_name' => $file->getFilename(),
            'file_original_name' => $file->getFilename(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_dir' => $destination,
            'file_url' => $driver['url'].'/'.basename($destination),
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
        return Storage::path($filename);
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

    public function search(string $searchTerm, array $searchColumns): void
    {
        if ($searchTerm) {

            foreach ($searchColumns as $index => $column) {
                if ($index === 0) {
                    $this->query->where($column, 'like', "%{$searchTerm}%");
                } else {
                    $this->query->orWhere($column, 'like', "%{$searchTerm}%");
                }
            }
        }
    }

    public function uniqueMimes(): array
    {
        return Attachment::query()->distinct()->pluck('file_type')->sort()->values()->toArray();
    }

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->query->paginate($perPage);
    }

    public function getAndValidateDisk(string $disk): array
    {
        $disks = config('filesystems.disks');

        if (! array_key_exists($disk, $disks)) {
            throw new MediaBrowserException('Storage disk not found.');
        }

        return ['disk' => $disk, 'driver' => $disks[$disk]];
    }

    public function getMimeTypeStats()
    {
        return Attachment::select('file_type', DB::raw('count(*) as total'), DB::raw('sum(file_size) as total_size'))
            ->groupBy('file_type')
            ->get();
    }

    public function getMimeTypeTotals()
    {
        return Attachment::select(DB::raw('count(*) as total'), DB::raw('sum(file_size) as total_size'))->first();
    }
}
