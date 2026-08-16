<?php

namespace TomShaw\Mediable\Image;

use Closure;
use Illuminate\Image\Image as ProcessedImage;
use Illuminate\Support\Facades\{Config, Image, Storage};
use Illuminate\Support\Str;
use Throwable;
use TomShaw\Mediable\Exceptions\MediaBrowserException;
use TomShaw\Mediable\Image\Transformations\{Brightness, Colorize, Contrast, Gamma, Invert, Pixelate, Text};

/**
 * Applies editor transformations to stored images through Laravel's image manipulation API.
 *
 * Every operation reads and writes through the configured filesystem disk, so paths passed
 * here are disk relative (the value stored on an attachment's `file_dir` column).
 */
class ImageEditorManager
{
    /** @return array<string, string> */
    public function flipModes(): array
    {
        return [
            'horizontal' => 'Horizontal',
            'vertical' => 'Vertical',
            'both' => 'Both',
        ];
    }

    /** @return array<string, string> */
    public function fitModes(): array
    {
        return [
            'scale' => 'Scale (proportional)',
            'resize' => 'Resize (exact)',
            'cover' => 'Cover (crop to fill)',
            'contain' => 'Contain (pad to fit)',
        ];
    }

    /** @return array<string, string> */
    public function filterModes(): array
    {
        return [
            'grayscale' => 'Grayscale',
            'invert' => 'Invert',
            'brightness' => 'Brightness',
            'contrast' => 'Contrast',
            'colorize' => 'Colorize',
            'gamma' => 'Gamma',
            'blur' => 'Blur',
            'sharpen' => 'Sharpen',
            'pixelate' => 'Pixelate',
        ];
    }

    /** @return array<string, string> */
    public function formats(): array
    {
        return [
            'webp' => 'WebP',
            'avif' => 'AVIF',
            'jpg' => 'JPEG',
            'png' => 'PNG',
            'gif' => 'GIF',
        ];
    }

    public function exists(string $path): bool
    {
        return $path !== '' && Storage::disk($this->disk())->exists($path);
    }

    /**
     * @return array{width: int, height: int}|null
     */
    public function dimensions(string $path): ?array
    {
        if (! $this->exists($path)) {
            return null;
        }

        try {
            [$width, $height] = Image::fromStorage($path, $this->disk())->dimensions();
        } catch (Throwable) {
            return null;
        }

        return ['width' => $width, 'height' => $height];
    }

    public function flip(string $path, string $mode): bool
    {
        return $this->save($path, fn (ProcessedImage $image): ProcessedImage => match ($mode) {
            'horizontal' => $image->flipHorizontally(),
            'vertical' => $image->flipVertically(),
            'both' => $image->flipHorizontally()->flipVertically(),
            default => throw new MediaBrowserException("Unsupported flip mode [{$mode}]."),
        });
    }

    public function fit(string $path, string $mode, int $width, int $height, ?string $background = null): bool
    {
        $targetWidth = $width > 0 ? $width : null;
        $targetHeight = $height > 0 ? $height : null;

        if ($mode === 'cover' || $mode === 'contain') {
            if ($targetWidth === null || $targetHeight === null) {
                return false;
            }

            return $this->save($path, fn (ProcessedImage $image): ProcessedImage => $mode === 'cover'
                ? $image->cover($targetWidth, $targetHeight)
                : $image->contain($targetWidth, $targetHeight, $background));
        }

        if ($targetWidth === null && $targetHeight === null) {
            return false;
        }

        return $this->save($path, fn (ProcessedImage $image): ProcessedImage => match ($mode) {
            'scale' => $image->scale($targetWidth, $targetHeight),
            'resize' => $image->resize($targetWidth, $targetHeight),
            default => throw new MediaBrowserException("Unsupported fit mode [{$mode}]."),
        });
    }

    /**
     * @param  array<string, int|float>  $options
     */
    public function filter(string $path, string $mode, array $options = []): bool
    {
        return $this->save($path, fn (ProcessedImage $image): ProcessedImage => match ($mode) {
            'grayscale' => $image->grayscale(),
            'invert' => $image->transform(new Invert),
            'brightness' => $image->transform(new Brightness($this->level($options, 'level'))),
            'contrast' => $image->transform(new Contrast($this->level($options, 'level'))),
            'colorize' => $image->transform(new Colorize(
                $this->level($options, 'red'),
                $this->level($options, 'green'),
                $this->level($options, 'blue'),
            )),
            'gamma' => $image->transform(new Gamma(max(0.1, min(10.0, (float) ($options['gamma'] ?? 1.0))))),
            'blur' => $image->blur($this->amount($options, 'amount', 5)),
            'sharpen' => $image->sharpen($this->amount($options, 'amount', 10)),
            'pixelate' => $image->transform(new Pixelate(max(1, (int) ($options['size'] ?? 10)))),
            default => throw new MediaBrowserException("Unsupported filter mode [{$mode}]."),
        });
    }

    public function rotate(string $path, float $angle, ?string $background = null): bool
    {
        return $this->save($path, fn (ProcessedImage $image): ProcessedImage => $image->rotate($angle, $background));
    }

    public function crop(string $path, int $width, int $height, int $x = 0, int $y = 0): bool
    {
        if ($width < 1 || $height < 1) {
            return false;
        }

        return $this->save($path, fn (ProcessedImage $image): ProcessedImage => $image->crop($width, $height, $x, $y));
    }

    public function orient(string $path): bool
    {
        return $this->save($path, fn (ProcessedImage $image): ProcessedImage => $image->orient());
    }

    public function text(string $path, string $text, string $font, float $size, string $color, float $angle = 0.0): bool
    {
        if (trim($text) === '' || $size <= 0 || ! is_readable($font)) {
            return false;
        }

        return $this->save($path, fn (ProcessedImage $image): ProcessedImage => $image->transform(
            new Text($text, $font, $size, $color, $angle)
        ));
    }

    /**
     * Re-encode the image into the given format, returning the disk path it was written to.
     */
    public function convert(string $path, string $format, ?int $quality = null): string|false
    {
        if (! array_key_exists($format, $this->formats())) {
            return false;
        }

        $destination = $this->destination($path, $format);

        try {
            return Image::fromStorage($path, $this->disk())
                ->toFormat($format)
                ->quality($this->quality($quality))
                ->storePubliclyAs($this->directory($destination), basename($destination), $this->disk());
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Apply the given transformations and write the result back over the source file.
     *
     * @param  Closure(ProcessedImage): ProcessedImage  $callback
     */
    protected function save(string $path, Closure $callback): bool
    {
        if (! $this->exists($path)) {
            return false;
        }

        try {
            $image = $callback(Image::fromStorage($path, $this->disk()));

            return $image
                ->quality($this->quality())
                ->storePubliclyAs($this->directory($path), basename($path), $this->disk()) !== false;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Resolve the path a converted image should be written to.
     *
     * Conversions that keep the current format overwrite in place; everything else swaps the
     * extension, falling back to a random name when that would collide with an existing file.
     */
    protected function destination(string $path, string $format): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === $format || ($format === 'jpg' && $extension === 'jpeg')) {
            return $path;
        }

        $directory = $this->directory($path);

        $destination = trim($directory.'/'.pathinfo($path, PATHINFO_FILENAME).'.'.$format, '/');

        if ($this->exists($destination)) {
            $destination = trim($directory.'/'.Str::random(12).'.'.$format, '/');
        }

        return $destination;
    }

    protected function directory(string $path): string
    {
        $directory = pathinfo($path, PATHINFO_DIRNAME);

        return in_array($directory, ['.', '', DIRECTORY_SEPARATOR], true) ? '' : $directory;
    }

    protected function disk(): string
    {
        return Config::string('mediable.disk');
    }

    /**
     * @return int<1, 100>
     */
    protected function quality(?int $quality = null): int
    {
        return max(1, min(100, $quality ?? Config::integer('mediable.editor_quality')));
    }

    /**
     * @param  array<string, int|float>  $options
     * @return int<-100, 100>
     */
    protected function level(array $options, string $key): int
    {
        return max(-100, min(100, (int) ($options[$key] ?? 0)));
    }

    /**
     * @param  array<string, int|float>  $options
     * @return int<0, 100>
     */
    protected function amount(array $options, string $key, int $default): int
    {
        return max(0, min(100, (int) ($options[$key] ?? $default)));
    }
}
