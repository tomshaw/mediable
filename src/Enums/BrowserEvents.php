<?php

namespace TomShaw\Mediable\Enums;

enum BrowserEvents: string
{
    case DEFAULT = 'mediable:on';
    case INSERT = 'mediable:insert';
    case OPEN = 'mediable:open';
    case CLOSE = 'mediable:close';
    case ALERT = 'mediable:alert';
}
