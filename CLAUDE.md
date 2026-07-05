# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is **tomshaw/mediable**, a Laravel Livewire media manager package. It provides a modal-based file browser for uploading, managing, and selecting media attachments. The package supports images, videos, audio, and documents with automatic WebP/AVIF conversion for images.

**Requirements:** PHP 8.5+, Laravel 13, Livewire 4

## Essential Commands

```bash
# Run tests
composer run test

# Run tests with coverage
composer run test-coverage

# Format code with Pint (uses grouped imports)
composer run format

# Run PHPStan static analysis
composer run analyse

# Generate PHPStan baseline
composer run baseline
```

## Architecture

### Package Structure

- `src/Components/MediaBrowser.php` - Main Livewire component; owns ALL browser state (selection, active item, ordering, sidebar draft fields) and exposes it to views via `#[Computed]` properties (`paginator`, `activeAttachment`, `selectedAttachments`, `editorAttachment`, `uniqueMimeTypes`)
- `src/Providers/MediableServiceProvider.php` - Registers views, migrations, Blade directives, and publishable resources
- `src/Models/Attachment.php` - Eloquent model for stored file metadata; uses `#[UseFactory]` and `#[Scope]` attributes (`visible()`/`hidden()` scopes)

### State Management (src/Concerns/)

Uses PHP classes implementing Livewire's `Wireable` interface for typed, serializable state:
- `AttachmentState` - Immutable attachment snapshot (`private(set)` properties)
- `ModalState` - Modal visibility and target element ID (publicly writable: JS entangles `modal.show`)
- `PanelState` - Current view mode (thumb, preview, editor, upload); `private(set)`
- `AlertState` - Alert messages (publicly writable: JS entangles `alert.show` for the auto-dismiss timer)
- `ShowState` - Panel visibility flags; `private(set)` with `toggleSidebar()`/`toggleMetaInfo()` clone-with helpers

### Facades

- `Eloquent` - Database operations via `EloquentManager` (CRUD, search, pagination, file storage)
- `GraphicDraw` - GD-based image manipulation via `GraphicDrawManager`

### Traits (src/Traits/)

Mixed into MediaBrowser: `WithFileSize`, `WithExtension`, `WithCache`, `WithMimeTypes`, `WithReporting`, `WithColumnWidths`. Mixed into the `form` view component: `WithGraphicDraw` (image edit actions; calls `$this->refreshWorkingCopy()` after each save), `WithFonts`. Mixed into the `uploads` view component: `ServerLimits`, `WithFileSize`, `WithMimeTypes`. `WithStorage` is currently unused.

### Events (src/Enums/BrowserEvents.php)

The event surface is intentionally small. Public API: `OPEN`, `CLOSE`, `ALERT`, `INSERT`, `DEFAULT`. JS bridge (handled in the parent view's `@script` block): `SCROLL`, `CONFIRM`, `AUDIO_START`, `AUDIO_PAUSE`, `DELETE_SELECTED`. Child-to-parent: `UPLOADS_COMPLETED`, `UPLOADS_RESET`, `EDITOR_ATTACHMENT_UPDATED`, `FORM_EDITOR_SAVED`. All other UI interaction goes through direct `wire:click`/`wire:model` bindings on MediaBrowser — do NOT reintroduce broadcast events for sibling communication.

### Views

The main view is `resources/views/livewire/media-browser.blade.php`. Browser chrome (header, toolbar, alert, attachments grid, preview, editor pane, meta, sidebar, strip, footer) are plain Blade partials in `resources/views/includes/` rendered in the parent component's context. Only three Livewire view components remain in `resources/views/livewire/mediable/components/`: `uploads` (file-upload lifecycle), `form` (image editor; receives `activeId` as a mount prop, keyed `form-editor`), and `stats` (optional, unmounted by default). Image URLs are cache-busted with `?v=` derived from `updated_at` (plus an edit-version counter in editor mode) — there is no `uniqueId` mechanism.

## Testing

Tests use Pest with Orchestra Testbench for Laravel package testing:
- `tests/Pest.php` - Configures the test suite to use `TestCase`
- `tests/Support/TestCase.php` - Base test case extending Orchestra's TestCase
- `tests/MediableTest.php` - Component tests using `Livewire::test()`

Tests run against an in-memory SQLite database (configured in `phpunit.xml.dist`).

## Configuration

The package config (`resources/config/config.php`) controls:
- `theme` - View theme (default: 'tailwind')
- `validation` - File type/size validation rules
- `disk` - Storage disk (env: `MEDIABLE_DISK_DRIVER`)
- `folder` - Upload folder (env: `MEDIABLE_DISK_FOLDER`)
- `create_webp` / `create_avif` - Auto-generate WebP/AVIF versions
- `webp_quality` / `avif_quality` - Conversion quality (0-100)

## Code Style

- Uses Laravel Pint with grouped imports enabled (`pint.json`)
- PHP 8.3+ features (constructor promotion, named arguments, enums)
- Type hints throughout codebase
