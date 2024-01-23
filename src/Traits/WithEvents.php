<?php

namespace TomShaw\Mediable\Traits;

use TomShaw\Mediable\Enums\BrowserEvents;

trait WithEvents
{
    /**
     * Dispatch an alert event to the browser.
     *
     * This method dispatches an event of type 'alert' to the browser, with a specified
     * type and message. The type parameter can be used to specify the type of the alert
     * (like 'error', 'warning', 'info', etc.), and the message parameter can be used to
     * specify the text of the alert message.
     *
     * @param string $type The type of the alert.
     * @param string $message The alert message.
     * @return void
     */
    public function dispatchAlert(string $type, string $message): void
    {
        $this->dispatch(BrowserEvents::ALERT->value, type: $type, message: $message);
    }

    /**
     * Dispatch a browser event with the given type and options.
     *
     * This method dispatches a browser event of the specified type, with the specified options.
     * The options are passed as an array, and are spread into the dispatch method.
     *
     * @param BrowserEvents $type The type of the event to dispatch.
     * @param array $options An array of options for the event.
     * @return void
     */
    public function dispatchEvent(BrowserEvents $type, array $options = []): void
    {
        $this->dispatch($type, ...$options);
    }
}
