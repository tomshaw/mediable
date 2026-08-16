<?php

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use TomShaw\Mediable\Enums\BrowserEvents;
use TomShaw\Mediable\Image\ImageEditor;
use TomShaw\Mediable\Models\Attachment;

beforeEach(function () {
    $this->artisan('migrate');

    Storage::fake('public');

    config()->set('mediable.disk', 'public');

    Storage::disk('public')->put('uploads/source.png', fixtureImage());

    $this->attachment = Attachment::create([
        'file_name' => 'source.png',
        'file_original_name' => 'source.png',
        'file_type' => 'image/png',
        'file_size' => Storage::disk('public')->size('uploads/source.png'),
        'file_dir' => 'uploads/source.png',
        'file_url' => 'uploads/source.png',
        'title' => 'source',
    ]);

    $this->editor = Livewire::test('mediable::form', ['activeId' => $this->attachment->id]);
});

/**
 * The disk path of the hidden working copy the editor operates on.
 */
function workingCopy(Testable $editor): string
{
    return $editor->get('attachment')->getFileDir();
}

// Test that mounting copies the original into a hidden working copy
it('opens on a working copy of the original', function () {
    $path = workingCopy($this->editor);

    expect($path)->not->toBe('uploads/source.png');
    expect(Storage::disk('public')->exists($path))->toBeTrue();
    expect($this->editor->get('primaryId'))->toBe($this->attachment->id);
    expect(Attachment::find($this->attachment->id)->file_dir)->toBe('uploads/source.png');
});

// Test that the dimension driven panels are seeded from the working copy
it('seeds the fit and crop panels with the current dimensions', function () {
    expect($this->editor->get('fitWidth'))->toBe(120);
    expect($this->editor->get('fitHeight'))->toBe(60);
    expect($this->editor->get('cropWidth'))->toBe(120);
    expect($this->editor->get('cropHeight'))->toBe(60);
});

// Test that scale mode keeps the height in step with the width
it('links the scale dimensions to the aspect ratio', function () {
    $this->editor->set('fitWidth', 60);

    expect($this->editor->get('fitHeight'))->toBe(30);
});

// Test that exact resize mode leaves the paired dimension alone
it('leaves the paired dimension alone outside scale mode', function () {
    $this->editor->set('fitMode', 'resize')->set('fitWidth', 60);

    expect($this->editor->get('fitHeight'))->toBe(60);
});

// Test that applying a fit writes the working copy and records the edit
it('applies a fit to the working copy', function () {
    $this->editor->set('fitMode', 'resize')->set('fitWidth', 40)->set('fitHeight', 90)->call('fitImage');

    expect(ImageEditor::dimensions(workingCopy($this->editor)))->toBe(['width' => 40, 'height' => 90]);
    expect($this->editor->get('editHistory'))->toHaveCount(1);
    expect($this->editor->get('editVersion'))->toBe(1);
});

// Test that cropping the working copy leaves the original untouched
it('crops the working copy without touching the original', function () {
    $this->editor->set('cropWidth', 30)->set('cropHeight', 20)->call('cropImage');

    expect(ImageEditor::dimensions(workingCopy($this->editor)))->toBe(['width' => 30, 'height' => 20]);
    expect(ImageEditor::dimensions('uploads/source.png'))->toBe(['width' => 120, 'height' => 60]);
});

// Test that rotating the working copy swaps its dimensions
it('rotates the working copy', function () {
    $this->editor->set('rotateAngle', 90)->call('rotateImage');

    expect(ImageEditor::dimensions(workingCopy($this->editor)))->toBe(['width' => 60, 'height' => 120]);
});

// Test that a filter runs through the editor and bumps the cache busting version
it('applies a filter to the working copy', function () {
    $this->editor->set('filterMode', 'grayscale')->call('filterImage');

    expect($this->editor->get('editVersion'))->toBe(1);
    expect($this->editor->get('editHistory'))->toHaveCount(1);
});

// Test that a failed operation reports an error instead of recording an edit
it('reports a failed operation without recording it', function () {
    $this->editor->set('flipMode', 'diagonal')->call('flipImage')
        ->assertDispatched(BrowserEvents::ALERT->value);

    expect($this->editor->get('editHistory'))->toBeEmpty();
});

// Test that converting re-encodes the working copy and repoints its record
it('converts the working copy and repoints its attachment', function () {
    $previous = workingCopy($this->editor);

    $this->editor->set('convertFormat', 'webp')->set('convertQuality', 80)->call('convertImage');

    $path = workingCopy($this->editor);

    expect($path)->toEndWith('.webp');
    expect(Storage::disk('public')->exists($previous))->toBeFalse();
    expect($this->editor->get('attachment')->getFileType())->toBe('image/webp');
    expect(Attachment::find($this->editor->get('attachment')->getId())->file_dir)->toBe($path);
});

// Test that saving reveals the working copy and clears the editor state
it('reveals the working copy when the changes are saved', function () {
    $this->editor->set('filterMode', 'grayscale')->call('filterImage');

    $id = $this->editor->get('attachment')->getId();

    $this->editor->call('saveEditorChanges')->assertDispatched(BrowserEvents::FORM_EDITOR_SAVED->value);

    expect(Attachment::find($id)->hidden)->toBeFalse();
    expect($this->editor->get('editHistory'))->toBeEmpty();
    expect($this->editor->get('primaryId'))->toBeNull();
    expect($this->editor->get('filterMode'))->toBeNull();
});

// Test that undoing starts a fresh working copy from the original
it('restarts from the original when the changes are undone', function () {
    $this->editor->set('cropWidth', 30)->set('cropHeight', 20)->call('cropImage');

    $this->editor->call('undoEditorChanges');

    expect(ImageEditor::dimensions(workingCopy($this->editor)))->toBe(['width' => 120, 'height' => 60]);
    expect($this->editor->get('editHistory'))->toBeEmpty();
});
