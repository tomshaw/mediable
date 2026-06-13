<?php

namespace TomShaw\Mediable\Traits;

use Illuminate\Support\Facades\Cache;

trait WithCache
{
    public function storeAttachmentId(int $id): void
    {
        Cache::put('attachment_id', $id, now()->addHours(24));
    }

    public function hasStoreAttachmentId(): bool
    {
        return Cache::has('attachment_id');
    }

    public function getStoreAttachmentId(): ?int
    {
        $id = Cache::get('attachment_id');

        return is_int($id) ? $id : null;
    }

    public function deleteStoreAttachmentId(): void
    {
        Cache::forget('attachment_id');
    }
}
