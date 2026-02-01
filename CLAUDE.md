# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is **tomshaw/mediable**, a Laravel Livewire media manager package. It provides a modal-based file browser for uploading, managing, and selecting media attachments. The package supports images, videos, audio, and documents with automatic WebP/AVIF conversion for images.

**Requirements:** PHP 8.3+, Laravel 12, Livewire 4

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

- `src/Components/MediaBrowser.php` - Main Livewire component with all media browser functionality
- `src/Providers/MediableServiceProvider.php` - Registers views, migrations, Blade directives, and publishable resources
- `src/Models/Attachment.php` - Eloquent model for stored file metadata

### State Management (src/Concerns/)

Uses PHP classes implementing Livewire's `Wireable` interface for typed, serializable state:
- `AttachmentState` - Current attachment selection/editing state
- `ModalState` - Modal visibility and target element ID
- `PanelState` - Current view mode (thumb, preview, editor, upload)
- `AlertState` - Alert messages
- `ShowState` - Sidebar/meta panel visibility

### Facades

- `Eloquent` - Database operations via `EloquentManager` (CRUD, search, pagination, file storage)
- `GraphicDraw` - GD-based image manipulation via `GraphicDrawManager`

### Traits (src/Traits/)

Functionality mixed into MediaBrowser: `WithFileSize`, `WithExtension`, `WithStorage`, `WithCache`, `WithMimeTypes`, `WithGraphicDraw`, `WithFonts`, `ServerLimits`, `WithReporting`, `WithColumnWidths`

### Events

The component uses Livewire events for communication:
- `mediable.open` - Opens the modal (optionally with target element ID)
- `mediable.close` - Closes the modal
- `mediable.on` - Dispatched when files are selected (for handling in parent components)

### Views

Views are in `resources/views/tailwind/` with a component-based structure. The main view is `media-browser.blade.php` with includes in the `includes/` subdirectory.

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
