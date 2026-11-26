<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GalleryItem;
use App\Models\Ministry;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\ContentApprovalNotification;
use App\Support\ApprovalNotifications;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_and_gallery_published_scopes_only_return_approved_and_published_records(): void
    {
        $ministry = $this->createMinistry('Choir');

        $approvedEvent = Event::query()->create([
            'title' => 'Approved Event',
            'description' => 'Approved event description',
            'date' => now()->addWeek()->toDateString(),
            'time' => '09:00:00',
            'location' => 'Main Chapel',
            'category' => 'Mass',
            'recurring' => null,
            'is_published' => true,
            'ministry_id' => $ministry->id,
            'approval_status' => Event::STATUS_APPROVED,
        ]);

        Event::query()->create([
            'title' => 'Pending Event',
            'description' => 'Pending event description',
            'date' => now()->addWeek()->toDateString(),
            'time' => '10:00:00',
            'location' => 'Hall',
            'category' => 'Prayer',
            'recurring' => null,
            'is_published' => true,
            'ministry_id' => $ministry->id,
            'approval_status' => Event::STATUS_PENDING,
        ]);

        Event::query()->create([
            'title' => 'Approved Hidden Event',
            'description' => 'Approved hidden event description',
            'date' => now()->addWeek()->toDateString(),
            'time' => '11:00:00',
            'location' => 'Annex',
            'category' => 'Fellowship',
            'recurring' => null,
            'is_published' => false,
            'ministry_id' => $ministry->id,
            'approval_status' => Event::STATUS_APPROVED,
        ]);

        $approvedGallery = GalleryItem::query()->create([
            'title' => 'Approved Gallery Item',
            'description' => 'Approved gallery item',
            'type' => 'image',
            'url' => 'https://example.com/a.jpg',
            'thumbnail' => null,
            'category' => 'worship',
            'date' => now()->toDateString(),
            'order' => 0,
            'is_published' => true,
            'ministry_id' => $ministry->id,
            'approval_status' => GalleryItem::STATUS_APPROVED,
        ]);

        GalleryItem::query()->create([
            'title' => 'Pending Gallery Item',
            'description' => 'Pending gallery item',
            'type' => 'image',
            'url' => 'https://example.com/b.jpg',
            'thumbnail' => null,
            'category' => 'events',
            'date' => now()->toDateString(),
            'order' => 1,
            'is_published' => true,
            'ministry_id' => $ministry->id,
            'approval_status' => GalleryItem::STATUS_PENDING,
        ]);

        $this->assertSame(
            [$approvedEvent->id],
            Event::published()->pluck('id')->all(),
        );

        $this->assertSame(
            [$approvedGallery->id],
            GalleryItem::published()->pluck('id')->all(),
        );
    }

    public function test_notify_reviewers_in_strict_mode_only_notifies_assigned_content_admins_and_super_admin(): void
    {
        $this->seedApprovalRoles();
        Notification::fake();

        SiteSetting::query()->create([
            'key' => 'approval_global_content_admin_reviewer',
            'value' => 'false',
            'group' => 'site',
        ]);

        $ministryA = $this->createMinistry('Youth');
        $ministryB = $this->createMinistry('Women');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $contentForA = User::factory()->create();
        $contentForA->assignRole('content_admin');
        $contentForA->ministries()->sync([$ministryA->id]);

        $contentForB = User::factory()->create();
        $contentForB->assignRole('content_admin');
        $contentForB->ministries()->sync([$ministryB->id]);

        $globalContent = User::factory()->create();
        $globalContent->assignRole('content_admin');

        ApprovalNotifications::notifyReviewers(
            contentType: 'Event',
            title: 'Youth Prayer Night',
            editUrl: '/admin/events/1/edit',
            ministryId: $ministryA->id,
            excludeUserId: null,
        );

        Notification::assertSentTo($superAdmin, ContentApprovalNotification::class);
        Notification::assertSentTo($contentForA, ContentApprovalNotification::class);
        Notification::assertNotSentTo($contentForB, ContentApprovalNotification::class);
        Notification::assertNotSentTo($globalContent, ContentApprovalNotification::class);
    }

    public function test_notify_reviewers_in_global_mode_notifies_unassigned_content_admins(): void
    {
        $this->seedApprovalRoles();
        Notification::fake();

        SiteSetting::query()->create([
            'key' => 'approval_global_content_admin_reviewer',
            'value' => 'true',
            'group' => 'site',
        ]);

        $ministry = $this->createMinistry('Media');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $globalContent = User::factory()->create();
        $globalContent->assignRole('content_admin');

        ApprovalNotifications::notifyReviewers(
            contentType: 'Gallery Item',
            title: 'Sunday Highlights',
            editUrl: '/admin/gallery-items/1/edit',
            ministryId: $ministry->id,
            excludeUserId: null,
        );

        Notification::assertSentTo($superAdmin, ContentApprovalNotification::class);
        Notification::assertSentTo($globalContent, ContentApprovalNotification::class);
    }

    private function seedApprovalRoles(): void
    {
        Role::findOrCreate('super_admin');
        Role::findOrCreate('content_admin');
        Role::findOrCreate('ministry_admin');
    }

    private function createMinistry(string $name): Ministry
    {
        return Ministry::query()->create([
            'name' => $name,
            'slug' => str($name)->slug(),
            'description' => "{$name} ministry",
            'summary' => "{$name} summary",
            'details' => "{$name} details",
            'contact_email' => strtolower(str_replace(' ', '', $name)) . '@example.com',
            'contact_phone' => '+233000000000',
            'meeting_time' => 'Saturday 4:00 PM',
            'image' => null,
            'media' => [],
            'icon' => 'users',
            'order' => 0,
            'is_active' => true,
        ]);
    }
}
