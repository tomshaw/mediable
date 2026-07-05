<?php

use Livewire\Livewire;
use TomShaw\Mediable\Components\MediaBrowser;
use TomShaw\Mediable\Enums\BrowserEvents;
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
    Livewire::test(MediaBrowser::class)->assertViewIs('mediable::livewire.media-browser');
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
    $this->component->call('insertMedia', [1])->assertDispatched(BrowserEvents::DEFAULT->value);
});

// Test that the 'set' method correctly updates component properties
it('sets the correct data', function () {
    $this->component->set('fullScreen', true);
    expect($this->component->get('fullScreen'))->toBe(true);
});

// Test that BrowserEvents enum values use the correct naming convention
it('has correctly prefixed event values', function () {
    $cases = BrowserEvents::cases();

    foreach ($cases as $case) {
        expect($case->value)->toStartWith('mediable::');
    }
});

// Test that toggling an attachment selects it and makes it active
it('toggles attachment selection', function () {
    $this->component->call('toggleAttachment', 1);

    expect($this->component->get('selectedIds'))->toBe([1]);
    expect($this->component->get('activeId'))->toBe(1);
    expect($this->component->get('title'))->toBe('temp_file_name.jpg');

    $this->component->call('toggleAttachment', 1);

    expect($this->component->get('selectedIds'))->toBe([]);
});

// Test that selection-dependent regions render inside the shared island and actions are scoped to it
it('scopes selection actions to the selection island', function () {
    $this->component->assertSeeHtml('wire:island="selection"');

    $this->component->call('toggleAttachment', 1);

    $this->component->assertSeeHtml('wire:island="selection"');

    expect($this->component->get('selectedIds'))->toBe([1]);
});

// Test that setting an active attachment always enables preview mode, even when already active
it('activates preview mode through setActiveAttachment', function () {
    $this->component->call('setActiveAttachment', 1);

    expect($this->component->get('activeId'))->toBe(1);
    expect($this->component->get('panel')->isPreviewMode())->toBeTrue();

    $this->component->call('setActiveAttachment', 1);

    expect($this->component->get('activeId'))->toBe(1);
    expect($this->component->get('panel')->isPreviewMode())->toBeTrue();
});

// Test that deleting the active attachment prunes the selection
it('prunes selection when deleting the active attachment', function () {
    $this->component->call('toggleAttachment', 1);
    $this->component->call('deleteAttachment', 1);

    expect($this->component->get('selectedIds'))->toBe([]);
    expect($this->component->get('activeId'))->toBeNull();
    $this->assertDatabaseMissing('attachments', ['id' => 1]);
});

// Test that confirmDelete dispatches the CONFIRM browser event with the selection payload
it('dispatches a confirm event before deleting selected', function () {
    $this->component->call('toggleAttachment', 1);
    $this->component->call('confirmDelete')
        ->assertDispatched(BrowserEvents::CONFIRM->value);
});

// Test that the DELETE_SELECTED event deletes attachments and clears the selection
it('deletes selected attachments through the delete event', function () {
    $this->component->call('toggleAttachment', 1);
    $this->component->dispatch(BrowserEvents::DELETE_SELECTED->value, selectedIds: [1]);

    expect($this->component->get('selectedIds'))->toBe([]);
    $this->assertDatabaseMissing('attachments', ['id' => 1]);
});

// Test that updateAttachment validates and persists sidebar draft fields
it('updates the active attachment from the sidebar draft', function () {
    $this->component->call('toggleAttachment', 1);
    $this->component->set('title', 'Updated Title');
    $this->component->call('updateAttachment');

    expect(Attachment::find(1)->title)->toBe('Updated Title');
    expect($this->component->get('alert')->type)->toBe('success');
});

// Test that updateAttachment surfaces validation failures as error alerts
it('rejects invalid sidebar drafts with an error alert', function () {
    $this->component->call('toggleAttachment', 1);
    $this->component->set('title', '');
    $this->component->call('updateAttachment');

    expect($this->component->get('alert')->type)->toBe('error');
    expect(Attachment::find(1)->title)->toBe('temp_file_name.jpg');
});

// Test that toggleOrderDir flips between ascending and descending
it('toggles the order direction', function () {
    expect($this->component->get('orderDir'))->toBe('DESC');

    $this->component->call('toggleOrderDir');
    expect($this->component->get('orderDir'))->toBe('ASC');

    $this->component->call('toggleOrderDir');
    expect($this->component->get('orderDir'))->toBe('DESC');
});

// Test that toggling the sidebar preserves all other ShowState flags
it('preserves show state flags when toggling the sidebar', function () {
    $this->component->call('toggleSidebar');

    $state = $this->component->get('show');
    expect($state->isShowSidebar())->toBeFalse();
    expect($state->isShowSearch())->toBeTrue();
    expect($state->isShowMetaInfo())->toBeTrue();

    $this->component->call('toggleSidebar');
    expect($this->component->get('show')->isShowSidebar())->toBeTrue();
});

// Test that insertMedia falls back to the current selection when called without arguments
it('inserts the current selection by default', function () {
    $this->component->call('toggleAttachment', 1);
    $this->component->call('insertMedia')->assertDispatched(BrowserEvents::DEFAULT->value);
});

// Test that preview mode renders the image lightbox overlay for enlarging images
it('renders the lightbox overlay in preview mode for images', function () {
    $this->component->call('setActiveAttachment', 1);

    $this->component->assertSeeHtml('x-show="lightbox"');
    $this->component->assertSeeHtml('cursor-zoom-in');
});
