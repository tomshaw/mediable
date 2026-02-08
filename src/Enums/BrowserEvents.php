<?php

namespace TomShaw\Mediable\Enums;

enum BrowserEvents: string
{
    // Browser
    case DEFAULT = 'mediable::browser.default';
    case INSERT = 'mediable::browser.insert';
    case OPEN = 'mediable::browser.open';
    case CLOSE = 'mediable::browser.close';
    case ALERT = 'mediable::browser.alert';
    case EXPAND = 'mediable::browser.expand';
    case SCROLL = 'mediable::browser.scroll';
    case CONFIRM = 'mediable::browser.confirm';

    // Server
    case SERVER_LIMITS = 'mediable::server.limits';

    // Toolbar
    case TOOLBAR_ENABLE_THUMB_MODE = 'mediable::toolbar.enable-thumb-mode';
    case TOOLBAR_ENABLE_EDITOR_MODE = 'mediable::toolbar.enable-editor-mode';
    case TOOLBAR_ENABLE_UPLOAD_MODE = 'mediable::toolbar.enable-upload-mode';
    case TOOLBAR_TOGGLE_SIDEBAR = 'mediable::toolbar.toggle-sidebar';
    case TOOLBAR_TOGGLE_META_INFO = 'mediable::toolbar.toggle-meta-info';
    case TOOLBAR_DELETE_ATTACHMENT = 'mediable::toolbar.delete-attachment';
    case TOOLBAR_ORDER_DIR_CHANGED = 'mediable::toolbar.order-dir-changed';
    case TOOLBAR_ORDER_BY_CHANGED = 'mediable::toolbar.order-by-changed';
    case TOOLBAR_COLUMN_WIDTH_CHANGED = 'mediable::toolbar.column-width-changed';
    case TOOLBAR_MIME_TYPE_CHANGED = 'mediable::toolbar.mime-type-changed';
    case TOOLBAR_CLOSE_IMAGE_EDITOR = 'mediable::toolbar.close-image-editor';

    // Uploads
    case UPLOADS_COMPLETED = 'mediable::uploads.completed';
    case UPLOADS_RESET = 'mediable::uploads.reset';
    case UPLOADS_FILES_CHANGED = 'mediable::uploads.files-changed';

    // Attachment (singular - one item)
    case ATTACHMENT_ACTIVE_CHANGED = 'mediable::attachment.active-changed';
    case ATTACHMENT_ACTIVE_CLEARED = 'mediable::attachment.active-cleared';

    // Attachments (plural - collection operations)
    case ATTACHMENTS_REMOVE_ITEM = 'mediable::attachments.remove-item';
    case ATTACHMENTS_CLEAR_SELECTED = 'mediable::attachments.clear-selected';
    case ATTACHMENTS_RESET_AUDIO = 'mediable::attachments.reset-audio';
    case ATTACHMENTS_SELECTION_CHANGED = 'mediable::attachments.selection-changed';
    case ATTACHMENTS_TOGGLE_ITEM = 'mediable::attachments.toggle-item';

    // Audio
    case AUDIO_START = 'mediable::audio.start';
    case AUDIO_PAUSE = 'mediable::audio.pause';

    // Panel
    case PANEL_INSERT_MEDIA = 'mediable::panel.insert-media';
    case PANEL_REGENERATE_UNIQUE_ID = 'mediable::panel.regenerate-unique-id';
    case PANEL_UNIQUE_ID_UPDATED = 'mediable::panel.unique-id-updated';

    // Form
    case FORM_EDITOR_SAVED = 'mediable::form.editor-saved';
    case FORM_REQUEST_ACTIVE_ID = 'mediable::form.request-active-id';
    case FORM_RECEIVE_ACTIVE_ID = 'mediable::form.receive-active-id';

    // Editor
    case EDITOR_ATTACHMENT_UPDATED = 'mediable::editor.attachment-updated';

    // Delete
    case DELETE_SELECTED = 'mediable::delete.selected';

    // Upload List
    case UPLOADS_LIST_REMOVE_FILE = 'mediable::uploads-list.remove-file';
    case UPLOADS_LIST_SUBMIT_FILES = 'mediable::uploads-list.submit-files';
    case UPLOADS_LIST_CLEAR_FILES = 'mediable::uploads-list.clear-files';
    case UPLOADS_LIST_DATA = 'mediable::uploads-list.data';
    case UPLOADS_LIST_RESET = 'mediable::uploads-list.reset';
}
