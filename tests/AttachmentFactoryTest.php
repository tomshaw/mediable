<?php

use TomShaw\Mediable\Database\Factories\AttachmentFactory;
use TomShaw\Mediable\Models\Attachment;

beforeEach(function () {
    $this->artisan('migrate');
});

// Test that the model resolves the package factory via newFactory()
it('resolves the package factory', function () {
    expect(Attachment::factory())->toBeInstanceOf(AttachmentFactory::class);
});

// Test that the factory persists a valid attachment with the expected casts
it('creates a persisted attachment with correct casts', function () {
    $attachment = Attachment::factory()->create();

    $this->assertDatabaseHas('attachments', ['id' => $attachment->id]);

    expect($attachment->file_size)->toBeInt();
    expect($attachment->sort_order)->toBeInt();
    expect($attachment->hidden)->toBeBool();
    expect($attachment->file_type)->toBe('image/jpeg');
});

// Test that the factory can generate multiple distinct records
it('creates multiple attachments', function () {
    Attachment::factory()->count(3)->create();

    expect(Attachment::count())->toBe(3);
});
