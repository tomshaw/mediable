<?php

namespace TomShaw\Mediable\Eloquent;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use TomShaw\Mediable\Exceptions\MediaBrowserException;
use TomShaw\Mediable\Models\Attachment;

class EloquentManager
{
    public function __construct(
        public Builder $query,
    ) {
    }

    public function query(string $orderBy, string $orderDir, string $mimeType = null): void
    {
        if ($mimeType) {
            $this->query = Attachment::where('file_type', '=', $mimeType)->orderBy($orderBy, $orderDir);
        } else {
            $this->query = Attachment::orderBy($orderBy, $orderDir);
        }
    }

    public function create(array $files): void
    {
        $disk = config('mediable.disk');
        $disks = config('filesystems.disks');

        if (! array_key_exists($disk, $disks)) {
            throw new MediaBrowserException('Storage disk not found.');
        }

        $driver = $disks[$disk];

        foreach ($files as $file) {
            if (is_null($file)) {
                continue;
            }

            $stored = $file->store($disk);

            $create = [];
            $create['file_name'] = $file->getFilename();
            $create['file_original_name'] = $file->getClientOriginalName();
            $create['file_type'] = $file->getMimeType();
            $create['file_size'] = $file->getSize();
            $create['file_dir'] = $stored;
            $create['file_url'] = $driver['url'].'/'.basename($stored);
            $create['title'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            try {
                Attachment::create($create);
            } catch (MediaBrowserException $e) {
                throw new MediaBrowserException($e->getMessage());
            }
        }
    }

    public function update(int $id, string $title, string $caption, string $description): void
    {
        try {
            Attachment::where('id', $id)->update([
                'title' => $title,
                'caption' => $caption,
                'description' => $description,
            ]);
        } catch (MediaBrowserException $e) {
            throw new MediaBrowserException($e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        try {
            Attachment::find($id)->delete();
        } catch (MediaBrowserException $e) {
            throw new MediaBrowserException($e->getMessage());
        }
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
}
