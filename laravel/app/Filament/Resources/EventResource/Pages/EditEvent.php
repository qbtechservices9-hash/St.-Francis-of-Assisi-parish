<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Event;
use App\Models\User;
use App\Support\ApprovalNotifications;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected bool $shouldNotifyReviewers = false;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();

        if ($this->isMinistryOnlyAdmin($user)) {
            $this->shouldNotifyReviewers = ($this->record->approval_status ?? null) !== Event::STATUS_PENDING;

            $allowedIds = $user->ministries()->pluck('ministries.id')->map(fn ($id) => (int) $id)->all();
            $selected = (int) ($data['ministry_id'] ?? 0);
            $fallback = (int) ($this->record->ministry_id ?? 0);

            if (!in_array($selected, $allowedIds, true)) {
                $data['ministry_id'] = in_array($fallback, $allowedIds, true) ? $fallback : ($allowedIds[0] ?? null);
            }

            $data['approval_status'] = Event::STATUS_PENDING;
            $data['submitted_by'] = $user->id;
            $data['approved_by'] = null;
            $data['approved_at'] = null;
            $data['is_published'] = false;

            return $data;
        }

        $this->shouldNotifyReviewers = false;

        if ($user?->hasAnyRole(['super_admin', 'content_admin'])) {
            if ((bool) ($data['is_published'] ?? false)) {
                $data['approval_status'] = Event::STATUS_APPROVED;
                $data['approved_by'] = $user->id;
                $data['approved_at'] = now();
            } elseif (($this->record->approval_status ?? null) === Event::STATUS_APPROVED) {
                $data['approval_status'] = Event::STATUS_DRAFT;
                $data['approved_by'] = null;
                $data['approved_at'] = null;
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        Cache::forget('home_upcoming_events');

        $user = auth()->user();

        if ($this->isMinistryOnlyAdmin($user) && $this->shouldNotifyReviewers) {
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
