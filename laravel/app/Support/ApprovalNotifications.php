<?php

namespace App\Support;

use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\ContentApprovalNotification;
use Illuminate\Support\Collection;

class ApprovalNotifications
{
    public static function notifyReviewers(
        string $contentType,
        string $title,
        string $editUrl,
        ?int $ministryId = null,
        ?int $excludeUserId = null
    ): void {
        $allowGlobalContentAdminReviewer = self::allowGlobalContentAdminReviewer();

        $reviewers = User::query()
            ->where(function ($query) {
                $query
                    ->whereHas('roles', fn ($roles) => $roles->where('name', 'super_admin'))
                    ->orWhereHas('roles', fn ($roles) => $roles->where('name', 'content_admin'));
            })
            ->when($ministryId, function ($query) use ($ministryId, $allowGlobalContentAdminReviewer) {
                $query->where(function ($scope) use ($ministryId, $allowGlobalContentAdminReviewer) {
                    $scope
                        ->whereHas('roles', fn ($roles) => $roles->where('name', 'super_admin'))
                        ->orWhere(function ($contentAdmins) use ($ministryId, $allowGlobalContentAdminReviewer) {
                            $contentAdmins
                                ->whereHas('roles', fn ($roles) => $roles->where('name', 'content_admin'))
                                ->where(function ($ministries) use ($ministryId, $allowGlobalContentAdminReviewer) {
                                    if ($allowGlobalContentAdminReviewer) {
                                        $ministries
                                            ->whereDoesntHave('ministries')
                                            ->orWhereHas('ministries', fn ($assigned) => $assigned->whereKey($ministryId));

                                        return;
                                    }

                                    $ministries->whereHas('ministries', fn ($assigned) => $assigned->whereKey($ministryId));
                                });
                        });
                });
            })
            ->when($excludeUserId, fn ($query) => $query->whereKeyNot($excludeUserId))
            ->distinct()
            ->get();

        if ($reviewers->isEmpty()) {
            return;
        }

        $message = "{$contentType} \"{$title}\" was submitted for review.";

        self::dispatch(
            $reviewers,
            new ContentApprovalNotification(
                title: "New {$contentType} pending approval",
                message: $message,
                url: $editUrl,
                level: 'warning',
            ),
        );
    }

    public static function notifySubmitterDecision(
        ?User $submitter,
        string $contentType,
        string $title,
        string $decision,
        string $editUrl,
        ?string $notes = null,
        ?int $excludeUserId = null
    ): void {
        if (! $submitter) {
            return;
        }

        if ($excludeUserId && $submitter->id === $excludeUserId) {
            return;
        }

        $decisionText = strtolower($decision);
        $message = "Your {$contentType} \"{$title}\" was {$decisionText}.";

        if (! empty($notes)) {
            $message .= " Note: {$notes}";
        }

        self::dispatch(
            collect([$submitter]),
            new ContentApprovalNotification(
                title: "{$contentType} {$decision}",
                message: $message,
                url: $editUrl,
                level: $decisionText === 'approved' ? 'success' : 'danger',
            ),
        );
    }

    private static function dispatch(Collection $users, ContentApprovalNotification $notification): void
    {
        foreach ($users as $user) {
            try {
                $user->notify($notification);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }
    }

    private static function allowGlobalContentAdminReviewer(): bool
    {
        $raw = SiteSetting::getValue('approval_global_content_admin_reviewer');

        if ($raw !== null) {
            return filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
        }

        return (bool) config('approval.allow_global_content_admin_reviewer', true);
    }
}
