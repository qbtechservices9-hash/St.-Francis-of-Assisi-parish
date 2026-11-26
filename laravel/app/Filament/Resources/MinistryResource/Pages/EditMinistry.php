<?php

namespace App\Filament\Resources\MinistryResource\Pages;


use Illuminate\Support\Facades\Cache;
use App\Filament\Resources\MinistryResource;
use App\Models\Ministry;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditMinistry extends EditRecord
{
    protected static string $resource = MinistryResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = $this->generateSlug($data['slug'] ?? null, $data['name'] ?? null);
        $data['media'] = $this->normalizeMedia($data['media'] ?? []);

        return $data;
    }

    private function normalizeMedia(array $media): array
    {
        return collect($media)
            ->map(function ($item) {
                if (!is_array($item)) {
                    return null;
                }

                $url = $item['url'] ?? null;

                if (!empty($item['image_upload'])) {
                    $url = $item['image_upload'];
                }

                if (!empty($item['video_upload'])) {
                    $url = $item['video_upload'];
                }

                $type = $item['type'] ?? null;

                return [
                    'type' => $type,
                    'url' => $url,
                    'caption' => $item['caption'] ?? null,
                ];
            })
            ->filter(fn ($item) => is_array($item) && !empty($item['url']))
            ->values()
            ->all();
    }

    private function generateSlug(?string $slug, ?string $name): ?string
    {
        $base = $slug ? Str::slug($slug) : Str::slug((string) $name);
        if ($base === '') {
            return $slug;
        }

        $candidate = $base;
        $counter = 2;

        while (Ministry::query()
            ->where('slug', $candidate)
            ->where('id', '!=', $this->record->id)
            ->exists()) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }
    protected function afterSave(): void
    {
        Cache::forget('ministries_active');
    }
}
