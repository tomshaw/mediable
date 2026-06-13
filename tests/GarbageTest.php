<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use TomShaw\Mediable\Eloquent\Eloquent;
use TomShaw\Mediable\Models\Attachment;

beforeEach(function () {
    $this->artisan('migrate');

    $this->disk = Storage::fake('public');
});

function createAttachment(string $fileDir, bool $hidden): Attachment
{
    return Attachment::create([
        'file_name' => basename($fileDir),
        'file_original_name' => basename($fileDir),
        'file_type' => 'image/jpeg',
        'file_size' => 100,
        'file_dir' => $fileDir,
        'file_url' => $fileDir,
        'title' => basename($fileDir),
        'hidden' => $hidden,
    ]);
}

// Test that garbage() removes hidden attachments and their files from the configured disk
it('deletes hidden attachments and their files', function () {
    $this->disk->put('uploads/hidden.jpg', UploadedFile::fake()->image('hidden.jpg')->getContent());

    $hidden = createAttachment('uploads/hidden.jpg', hidden: true);

    Eloquent::garbage();

    $this->disk->assertMissing('uploads/hidden.jpg');
    $this->assertDatabaseMissing('attachments', ['id' => $hidden->id]);
});

// Test that garbage() leaves visible attachments and their files untouched
it('keeps visible attachments and their files', function () {
    $this->disk->put('uploads/visible.jpg', UploadedFile::fake()->image('visible.jpg')->getContent());

    $visible = createAttachment('uploads/visible.jpg', hidden: false);

    Eloquent::garbage();

    $this->disk->assertExists('uploads/visible.jpg');
    $this->assertDatabaseHas('attachments', ['id' => $visible->id]);
});

// Test that garbage() removes the database record even when the file is already missing
it('deletes a hidden record when its file is absent', function () {
    $hidden = createAttachment('uploads/orphan.jpg', hidden: true);

    Eloquent::garbage();

    $this->assertDatabaseMissing('attachments', ['id' => $hidden->id]);
});
