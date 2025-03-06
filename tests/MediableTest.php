<?php

use Livewire\Livewire;
use TomShaw\Mediable\Components\MediaBrowser;
use TomShaw\Mediable\Enums\BrowserEvents;
use TomShaw\Mediable\Models\Attachment;

beforeEach(function () {
    $this->artisan('migrate');

    $this->theme = config('mediable.theme');

    Attachment::create([
        'file_name' => 'temp_file_name.jpg',
        'file_original_name' => 'temp_file_name.jpg',
        'file_type' => 'image/jpeg',
        'file_size' => 100,
        'file_dir' => 'temp_file_name.jpg',
        'file_url' => 'temp_file_name.jpg',
        'title' => 'temp_file_name.jpg',
        'caption' => 'temp_file_name.jpg',
        'description' => 'temp_file_name.jpg',
        'sort_order' => 1,
        'styles' => 'temp_file_name.jpg',
    ]);

    $this->component = Livewire::test(MediaBrowser::class);
});

// Test that the component renders successfully without throwing any exceptions
it('can render a mediable component', function () {
    $this->component->assertSuccessful();
});

// Test that the component renders a file with the name 'temp_file_name.jpg'
it('can render a mediable file type', function () {
    $this->component->assertSee('temp_file_name.jpg');
});

// Test that the attachment was created successfully
it('creates an attachment', function () {
    $attachment = Attachment::first();
    expect($attachment->file_name)->toBe('temp_file_name.jpg');
});

// Test that the component has the correct instance
it('has the correct component instance', function () {
    expect(get_class($this->component->instance()))->toBe(MediaBrowser::class);
});

// Test that the component has the correct view
it('has the correct view', function () {
    Livewire::test(MediaBrowser::class)->assertViewIs('mediable::'.$this->theme.'.media-browser');
});

// Test that the component has the correct initial properties
it('has the correct initial properties', function () {
    $state = $this->component->get('modal');
    expect($state->show)->toBe(false);
    expect($state->elementId)->toBe('');
});

// Test that the 'deleteAttachment' method removes the attachment from the database
it('can delete an attachment', function () {
    $this->component->call('deleteAttachment', 1);
    $this->assertDatabaseMissing('attachments', ['id' => 1]);
});

// Test that the 'insertMedia' method dispatches the 'DEFAULT' event
it('dispatched the correct event', function () {
    $this->component->call('insertMedia')->assertDispatched(BrowserEvents::DEFAULT->value);
});

// Test that the 'set' method correctly updates the 'theme' property of the component
it('sets the correct data', function () {
    $this->component->set('theme', 'tailwind');
    expect($this->component->get('theme'))->toBe('tailwind');
});
