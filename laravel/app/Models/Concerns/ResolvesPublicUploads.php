<?php

namespace App\Models\Concerns;

use App\Support\MediaStorage;

trait ResolvesPublicUploads
{
    protected function resolvePublicUrl(?string $value): ?string
    {
        return MediaStorage::url($value);
    }

    protected function resolvePublicUrls(?array $items): array
    {
        return collect($items ?? [])
            ->filter(fn ($item) => is_string($item) && $item !== '')
            ->map(fn (string $item) => $this->resolvePublicUrl($item))
            ->filter()
            ->values()
            ->all();
    }
}
