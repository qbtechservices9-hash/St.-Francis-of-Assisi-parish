<?php

namespace App\Filament\Resources\GalleryItemResource\Pages;

use App\Filament\Resources\GalleryItemResource;
use App\Models\GalleryItem;
use App\Models\User;
use App\Support\ApprovalNotifications;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateGalleryItem extends CreateRecord
{
    protected static string $resource = GalleryItemResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        if ($this->isMinistryOnlyAdmin($user)) {
            $allowedIds = $user->ministries()->pluck('ministries.id')->map(fn ($id) => (int) $id)->all();

            if (!empty($allowedIds)) {
                $selected = (int) ($data['ministry_id'] ?? 0);
                $data['ministry_id'] = in_array($selected, $allowedIds, true) ? $selected : $allowedIds[0];
            }
        }

        $data = GalleryItemResource::prepareFormData($data, null, true);

        if ($this->isMinistryOnlyAdmin($user)) {
            $data['approval_status'] = GalleryItem::STATUS_PENDING;
            $data['submitted_by'] = $user->id;
            $data['approved_by'] = null;
            $data['approved_at'] = null;
            $data['is_published'] = false;
        } elseif ($user?->hasAnyRole(['super_admin', 'content_admin'])) {
            if ((bool) ($data['is_published'] ?? false)) {
                $data['approval_status'] = GalleryItem::STATUS_APPROVED;
                $data['approved_by'] = $user->id;
                $data['approved_at'] = now();
            } else {
                $data['approval_status'] = GalleryItem::STATUS_DRAFT;
                $data['approved_by'] = null;
                $data['approved_at'] = null;
            }
        }

        $imageUploads = $data['image_uploads'] ?? [];
        $videoUploads = $data['video_uploads'] ?? [];
        $thumbnail = $data['thumbnail'] ?? null;

        $base = $data;
        unset($base['image_uploads'], $base['video_uploads'], $base['thumbnail_upload']);

        $records = [];

        if (($data['type'] ?? null) === 'image' && is_array($imageUploads) && !empty($imageUploads)) {
            foreach ($imageUploads as $path) {
                $records[] = GalleryItem::query()->create(array_merge($base, [
                    'url' => $path,
                ]));
            }
        }

        if (($data['type'] ?? null) === 'video' && is_array($videoUploads) && !empty($videoUploads)) {
            foreach ($videoUploads as $path) {
                $records[] = GalleryItem::query()->create(array_merge($base, [
                    'url' => $path,
                    'thumbnail' => $thumbnail,
                ]));
            }
        }

        if (!empty($data['url']) && empty($records)) {
            $records[] = GalleryItem::query()->create($base);
        } elseif (!empty($data['url']) && !empty($records)) {
            $records[] = GalleryItem::query()->create(array_merge($base, [
                'url' => $data['url'],
                'thumbnail' => $thumbnail,
            ]));
        }

        if (empty($records)) {
            $records[] = GalleryItem::query()->create($base);
        }

        return $records[0];
    }

    protected function afterCreate(): void
    {
        $user = auth()->user();

        if ($this->isMinistryOnlyAdmin($user)) {
            ApprovalNotifications::notifyReviewers(
                contentType: 'Gallery Item',
                title: (string) ($this->record->title ?? 'Untitled'),
                editUrl: GalleryItemResource::getUrl('edit', ['record' => $this->record]),
                ministryId: $this->record->ministry_id ? (int) $this->record->ministry_id : null,
                excludeUserId: $user?->id,
            );
        }
    }

    private function isMinistryOnlyAdmin(?User $user): bool
    {
        return (bool) $user
            && $user->hasRole('ministry_admin')
            && !$user->hasAnyRole(['super_admin', 'content_admin']);
    }
}
