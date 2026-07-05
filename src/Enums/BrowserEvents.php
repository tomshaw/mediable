<?php

namespace TomShaw\Mediable\Enums;

enum BrowserEvents: string
{
    // Public browser API
    case DEFAULT = 'mediable::browser.default';
    case INSERT = 'mediable::browser.insert';
    case OPEN = 'mediable::browser.open';
    case CLOSE = 'mediable::browser.close';
    case ALERT = 'mediable::browser.alert';

    // JavaScript bridge (handled in the component's @script block)
    case SCROLL = 'mediable::browser.scroll';
    case CONFIRM = 'mediable::browser.confirm';
    case AUDIO_START = 'mediable::audio.start';
    case AUDIO_PAUSE = 'mediable::audio.pause';
    case DELETE_SELECTED = 'mediable::delete.selected';

    // Uploads child component
    case UPLOADS_COMPLETED = 'mediable::uploads.completed';
    case UPLOADS_RESET = 'mediable::uploads.reset';

    // Image editor (form) child component
    case EDITOR_ATTACHMENT_UPDATED = 'mediable::editor.attachment-updated';
    case FORM_EDITOR_SAVED = 'mediable::form.editor-saved';
}
