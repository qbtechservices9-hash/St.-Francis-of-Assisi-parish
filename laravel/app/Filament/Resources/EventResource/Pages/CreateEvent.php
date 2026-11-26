<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Event;
use App\Models\User;
use App\Support\ApprovalNotifications;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($this->isMinistryOnlyAdmin($user)) {
            $allowedIds = $user->ministries()->pluck('ministries.id')->map(fn ($id) => (int) $id)->all();

            if (!empty($allowedIds)) {
                $selected = (int) ($data['ministry_id'] ?? 0);
                $data['ministry_id'] = in_array($selected, $allowedIds, true) ? $selected : $allowedIds[0];
            }

            $data['approval_status'] = Event::STATUS_PENDING;
            $data['submitted_by'] = $user->id;
            $data['approved_by'] = null;
            $data['approved_at'] = null;
            $data['is_published'] = false;

            return $data;
        }

        if ($user?->hasAnyRole(['super_admin', 'content_admin'])) {
            if ((bool) ($data['is_published'] ?? false)) {
                $data['approval_status'] = Event::STATUS_APPROVED;
                $data['approved_by'] = $user->id;
                $data['approved_at'] = now();
            } else {
                $data['approval_status'] = Event::STATUS_DRAFT;
                $data['approved_by'] = null;
                $data['approved_at'] = null;
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        Cache::forget('home_upcoming_events');

        $user = auth()->user();

        if ($this->isMinistryOnlyAdmin($user)) {
            ApprovalNotifications::notifyReviewers(
                contentType: 'Event',
                title: (string) ($this->record->title ?? 'Untitled'),
                editUrl: EventResource::getUrl('edit', ['record' => $this->record]),
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
