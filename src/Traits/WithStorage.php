<?php

namespace TomShaw\Mediable\Traits;

use Illuminate\Support\Facades\Storage;

/** @phpstan-ignore trait.unused */
trait WithStorage
{
    public function path(string $path): string
    {
        return Storage::path($path);
    }

    public function exists(string $path): bool
    {
        return Storage::exists($path);
    }

    public function delete(string $path): bool
    {
        return Storage::delete($path);
    }

    public function copy(string $from, string $to): bool
    {
        return Storage::copy($from, $to);
    }

    public function move(string $from, string $to): bool
    {
        return Storage::move($from, $to);
    }

    public function size(string $path): int
    {
        return Storage::size($path);
    }

    public function url(string $path): string
    {
        return Storage::url($path);
    }

    public function mimeType(string $path): string
    {
        return Storage::mimeType($path);
    }

    public function lastModified(string $path): int
    {
        return Storage::lastModified($path);
    }

    public function files(string $directory): array
    {
        return Storage::files($directory);
    }

    public function allFiles(string $directory): array
    {
        return Storage::allFiles($directory);
    }

    public function directories(string $directory): array
    {
        return Storage::directories($directory);
    }

    public function allDirectories(string $directory): array
    {
        return Storage::allDirectories($directory);
    }

    public function makeDirectory(string $directory): bool
    {
        return Storage::makeDirectory($directory);
    }

    public function deleteDirectory(string $directory): bool
    {
        return Storage::deleteDirectory($directory);
    }

    public function put(string $path, $contents, ?string $visibility = null): bool
    {
        return Storage::put($path, $contents, $visibility);
    }

    public function putFile(string $path, $file, ?string $visibility = null): string
    {
        return Storage::putFile($path, $file, $visibility);
    }

    public function putFileAs(string $path, $file, string $name, ?string $visibility = null): string
    {
        return Storage::putFileAs($path, $file, $name, $visibility);
    }

    public function putString(string $path, string $contents, ?string $visibility = null): bool
    {
        return Storage::putString($path, $contents, $visibility);
    }
}
