<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\{FileUploadConfiguration, TemporaryUploadedFile};
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Models\Attachment;

beforeEach(function () {
    $this->artisan('migrate');

    $this->disk = Storage::fake('public');

    Storage::fake(FileUploadConfiguration::disk());
});

function temporaryUpload(UploadedFile $file): TemporaryUploadedFile
{
    $name = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded($file);

    FileUploadConfiguration::storage()->putFileAs('/'.FileUploadConfiguration::path(), $file, $name);

    return TemporaryUploadedFile::createFromLivewire('/'.$name);
}

// Test that an image upload stores the original alongside its WebP and AVIF derivatives
it('creates webp and avif derivatives for image uploads', function () {
    config()->set('mediable.create_webp', true);
    config()->set('mediable.create_avif', true);

    Eloquent::create([temporaryUpload(UploadedFile::fake()->image('photo.jpg', 120, 80))]);

    expect(Attachment::count())->toBe(3);

    $webp = Attachment::where('file_type', 'image/webp')->firstOrFail();
    $avif = Attachment::where('file_type', 'image/avif')->firstOrFail();

    $this->disk->assertExists('uploads/'.$webp->file_name);
    $this->disk->assertExists('uploads/'.$avif->file_name);

    expect($webp->file_name)->toEndWith('.webp')
        ->and($avif->file_name)->toEndWith('.avif')
        ->and($webp->file_size)->toBe($this->disk->size('uploads/'.$webp->file_name))
        ->and($avif->file_size)->toBe($this->disk->size('uploads/'.$avif->file_name));

    expect(getimagesizefromstring($this->disk->get('uploads/'.$webp->file_name))[2])->toBe(IMAGETYPE_WEBP)
        ->and(getimagesizefromstring($this->disk->get('uploads/'.$avif->file_name))[2])->toBe(IMAGETYPE_AVIF);
});

// Test that derivative creation is skipped when both conversions are disabled
it('skips derivatives when conversions are disabled', function () {
    config()->set('mediable.create_webp', false);
    config()->set('mediable.create_avif', false);

    Eloquent::create([temporaryUpload(UploadedFile::fake()->image('photo.jpg', 120, 80))]);

    expect(Attachment::count())->toBe(1)
        ->and(Attachment::first()->file_type)->toBe('image/jpeg');
});

// Test that non image uploads are stored without any conversion attempt
it('does not create derivatives for non image uploads', function () {
    Eloquent::create([temporaryUpload(UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'))]);

    expect(Attachment::count())->toBe(1)
        ->and(Attachment::first()->file_type)->toBe('application/pdf');
});

// Test that the derivative points back at the original upload it was generated from
it('links derivatives back to the original upload', function () {
    config()->set('mediable.create_webp', true);
    config()->set('mediable.create_avif', false);

    Eloquent::create([temporaryUpload(UploadedFile::fake()->image('photo.jpg', 120, 80))]);

    $original = Attachment::where('file_type', 'image/jpeg')->firstOrFail();
    $webp = Attachment::where('file_type', 'image/webp')->firstOrFail();

    expect($webp->file_dir)->toBe($original->file_dir);
});
