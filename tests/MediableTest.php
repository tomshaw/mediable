<?php

use Livewire\Livewire;
use TomShaw\Mediable\Components\MediaBrowser;
use TomShaw\Mediable\Models\Attachment;

beforeEach(function () {
    $this->artisan('migrate');

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
        'sortorder' => 1,
        'styles' => 'temp_file_name.jpg',
    ]);

    $this->component = Livewire::test(MediaBrowser::class);
});

it('can render a mediable component', function () {
    $this->component->assertSuccessful();
});

it('can render a mediable file type', function () {
    $this->component->assertSee('temp_file_name.jpg');
});
