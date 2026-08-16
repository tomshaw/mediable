<?php

use Illuminate\Support\Facades\Storage;
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Image\ImageEditor;
use TomShaw\Mediable\Models\Attachment;

beforeEach(function () {
    Storage::fake('public');

    config()->set('mediable.disk', 'public');

    $this->path = 'uploads/editor-test.png';

    Storage::disk('public')->put($this->path, fixtureImage());
});

// Test that dimensions are read through the configured disk
it('reads the dimensions of a stored image', function () {
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 120, 'height' => 60]);
});

// Test that a missing file yields no dimensions rather than an error
it('returns no dimensions for a missing file', function () {
    expect(ImageEditor::dimensions('uploads/does-not-exist.png'))->toBeNull();
});

// Test that flipping horizontally moves the right hand half of the image to the left
it('flips an image horizontally', function () {
    expect(ImageEditor::flip($this->path, 'horizontal'))->toBeTrue();
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 120, 'height' => 60]);

    expect(ImageEditor::crop($this->path, 50, 50, 0, 0))->toBeTrue();
    expect(dominantColor($this->path))->toBe('#2828c8');
});

// Test that an unknown flip mode fails instead of writing the file
it('rejects an unknown flip mode', function () {
    expect(ImageEditor::flip($this->path, 'diagonal'))->toBeFalse();
});

// Test that scaling keeps the aspect ratio of the source image
it('scales an image proportionally', function () {
    expect(ImageEditor::fit($this->path, 'scale', 60, 0))->toBeTrue();
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 60, 'height' => 30]);
});

// Test that resizing applies both dimensions exactly, distorting the image
it('resizes an image to exact dimensions', function () {
    expect(ImageEditor::fit($this->path, 'resize', 40, 90))->toBeTrue();
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 40, 'height' => 90]);
});

// Test that cover fills the requested box by cropping the overflow
it('covers the requested dimensions', function () {
    expect(ImageEditor::fit($this->path, 'cover', 50, 50))->toBeTrue();
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 50, 'height' => 50]);
});

// Test that contain pads the image out to the requested box
it('contains the image within the requested dimensions', function () {
    expect(ImageEditor::fit($this->path, 'contain', 100, 100, '#ffffff'))->toBeTrue();
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 100, 'height' => 100]);
});

// Test that cover and contain require both dimensions
it('rejects cover without both dimensions', function () {
    expect(ImageEditor::fit($this->path, 'cover', 50, 0))->toBeFalse();
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 120, 'height' => 60]);
});

// Test that an unknown fit mode fails instead of writing the file
it('rejects an unknown fit mode', function () {
    expect(ImageEditor::fit($this->path, 'squish', 50, 50))->toBeFalse();
});

// Test that cropping writes back the requested rectangle
it('crops an image', function () {
    expect(ImageEditor::crop($this->path, 30, 20, 5, 5))->toBeTrue();
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 30, 'height' => 20]);
});

// Test that a zero sized crop is rejected
it('rejects an empty crop', function () {
    expect(ImageEditor::crop($this->path, 0, 20))->toBeFalse();
});

// Test that a quarter turn swaps the width and height
it('rotates an image', function () {
    expect(ImageEditor::rotate($this->path, 90.0, '#000000'))->toBeTrue();
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 60, 'height' => 120]);
});

// Test that auto orienting an image without EXIF data leaves it intact
it('orients an image', function () {
    expect(ImageEditor::orient($this->path))->toBeTrue();
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 120, 'height' => 60]);
});

// Test that the native grayscale filter drains the color channels
it('applies the grayscale filter', function () {
    Storage::disk('public')->put($this->path, solidImage(40, 40, 200, 40, 40));

    expect(ImageEditor::filter($this->path, 'grayscale'))->toBeTrue();

    $color = dominantColor($this->path);

    expect(substr($color, 1, 2))->toBe(substr($color, 3, 2))
        ->and(substr($color, 3, 2))->toBe(substr($color, 5, 2));
});

// Test that the custom invert transformation is registered and applied
it('applies the invert filter through a custom transformation', function () {
    Storage::disk('public')->put($this->path, solidImage(40, 40, 200, 40, 40));

    expect(ImageEditor::filter($this->path, 'invert'))->toBeTrue();
    expect(dominantColor($this->path))->toBe('#37d7d7');
});

// Test that the custom brightness transformation lightens every channel
it('applies the brightness filter through a custom transformation', function () {
    Storage::disk('public')->put($this->path, solidImage(40, 40, 100, 100, 100));

    expect(ImageEditor::filter($this->path, 'brightness', ['level' => 20]))->toBeTrue();
    expect(dominantColor($this->path))->toBe('#979797');
});

// Test that the custom colorize transformation shifts a single channel
it('applies the colorize filter through a custom transformation', function () {
    Storage::disk('public')->put($this->path, solidImage(40, 40, 100, 100, 100));

    expect(ImageEditor::filter($this->path, 'colorize', ['red' => 50, 'green' => 0, 'blue' => 0]))->toBeTrue();
    expect(dominantColor($this->path))->toBe('#e36464');
});

// Test that every remaining filter mode round trips without corrupting the file
it('applies the remaining filter modes', function (string $mode, array $options) {
    expect(ImageEditor::filter($this->path, $mode, $options))->toBeTrue();
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 120, 'height' => 60]);
})->with([
    ['contrast', ['level' => 25]],
    ['gamma', ['gamma' => 2.2]],
    ['blur', ['amount' => 5]],
    ['sharpen', ['amount' => 15]],
    ['pixelate', ['size' => 8]],
]);

// Test that an unknown filter mode fails instead of writing the file
it('rejects an unknown filter mode', function () {
    expect(ImageEditor::filter($this->path, 'emboss'))->toBeFalse();
});

// Test that text is drawn onto the image
it('draws text onto an image', function () {
    Storage::disk('public')->put($this->path, solidImage(200, 100, 255, 255, 255));

    expect(ImageEditor::text($this->path, 'Mediable', font(), 42.0, '#000000'))->toBeTrue();
    expect(dominantColor($this->path))->not->toBe('#ffffff');
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 200, 'height' => 100]);
});

// Test that unusable text input is rejected before touching the file
it('rejects unusable text input', function () {
    expect(ImageEditor::text($this->path, '   ', font(), 42.0, '#000000'))->toBeFalse();
    expect(ImageEditor::text($this->path, 'Mediable', font(), 0.0, '#000000'))->toBeFalse();
    expect(ImageEditor::text($this->path, 'Mediable', '/missing/font.ttf', 42.0, '#000000'))->toBeFalse();
});

// Test that converting writes a new file alongside the original with the new extension
it('converts an image to another format', function () {
    $destination = ImageEditor::convert($this->path, 'webp', 80);

    expect($destination)->toBe('uploads/editor-test.webp');
    expect(Storage::disk('public')->exists($destination))->toBeTrue();
    expect(Storage::disk('public')->mimeType($destination))->toBe('image/webp');
});

// Test that converting to the current format re-encodes the file in place
it('converts in place when the format is unchanged', function () {
    expect(ImageEditor::convert($this->path, 'png'))->toBe($this->path);
    expect(ImageEditor::dimensions($this->path))->toBe(['width' => 120, 'height' => 60]);
});

// Test that an existing file with the target name is never overwritten
it('avoids overwriting an existing file when converting', function () {
    Storage::disk('public')->put('uploads/editor-test.webp', 'reserved');

    $destination = ImageEditor::convert($this->path, 'webp');

    expect($destination)->not->toBe('uploads/editor-test.webp');
    expect(Storage::disk('public')->get('uploads/editor-test.webp'))->toBe('reserved');
    expect(Storage::disk('public')->mimeType($destination))->toBe('image/webp');
});

// Test that an unsupported target format is rejected
it('rejects an unsupported conversion format', function () {
    expect(ImageEditor::convert($this->path, 'tiff'))->toBeFalse();
});

// Test that operations on a missing file fail rather than throwing
it('fails quietly when the source file is missing', function () {
    expect(ImageEditor::flip('uploads/missing.png', 'vertical'))->toBeFalse();
    expect(ImageEditor::convert('uploads/missing.png', 'webp'))->toBeFalse();
});

// Test that repointing an attachment swaps the file and discards the one it replaced
it('repoints an attachment at a converted file', function () {
    $this->artisan('migrate');

    $attachment = Attachment::create([
        'file_name' => 'editor-test.png',
        'file_original_name' => 'editor-test.png',
        'file_type' => 'image/png',
        'file_size' => Storage::disk('public')->size($this->path),
        'file_dir' => $this->path,
        'file_url' => 'editor-test.png',
        'title' => 'editor-test',
    ]);

    $destination = ImageEditor::convert($this->path, 'webp', 80);

    Eloquent::replaceAttachmentFile($attachment->id, $this->path, $destination);

    $attachment->refresh();

    expect($attachment->file_dir)->toBe($destination);
    expect($attachment->file_name)->toBe('editor-test.webp');
    expect($attachment->file_type)->toBe('image/webp');
    expect($attachment->file_size)->toBe(Storage::disk('public')->size($destination));
    expect(Storage::disk('public')->exists($this->path))->toBeFalse();
});
