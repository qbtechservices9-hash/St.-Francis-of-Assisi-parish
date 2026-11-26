<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\GalleryItem;
use App\Models\Ministry;
use App\Models\Friar;
use App\Models\PastoralCouncilChairperson;
use App\Models\Society;
use App\Support\MediaStorage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MediaUrlAccessorTest extends TestCase
{
    #[Test]
    public function it_resolves_event_image_urls_for_public_pages(): void
    {
        $event = new Event(['image' => 'events/poster.jpg']);
        $eventFromStoragePrefix = new Event(['image' => 'storage/events/poster.jpg']);
        $eventRemote = new Event(['image' => 'https://example.com/image.jpg']);

        $this->assertSame(MediaStorage::url('events/poster.jpg'), $event->image_url);
        $this->assertSame(MediaStorage::url('storage/events/poster.jpg'), $eventFromStoragePrefix->image_url);
        $this->assertSame('https://example.com/image.jpg', $eventRemote->image_url);
    }

    #[Test]
    public function it_resolves_gallery_media_and_thumbnail_urls_for_public_pages(): void
    {
        $item = new GalleryItem([
            'url' => 'gallery/media.jpg',
            'thumbnail' => 'storage/gallery/thumb.jpg',
        ]);

        $this->assertSame(MediaStorage::url('gallery/media.jpg'), $item->media_url);
        $this->assertSame(MediaStorage::url('storage/gallery/thumb.jpg'), $item->thumbnail_url);
    }

    #[Test]
    public function it_resolves_ministry_image_urls_for_public_pages(): void
    {
        $ministry = new Ministry(['image' => 'ministries/logo.png']);
        $this->assertSame(MediaStorage::url('ministries/logo.png'), $ministry->image_url);
    }

    #[Test]
    public function it_resolves_society_images_for_public_pages(): void
    {
        $society = new Society([
            'cover_image' => 'societies/covers/history.jpg',
            'gallery' => ['societies/gallery/one.jpg', 'storage/societies/gallery/two.jpg'],
        ]);

        $this->assertSame(MediaStorage::url('societies/covers/history.jpg'), $society->cover_image_url);
        $this->assertSame(MediaStorage::url('storage/societies/gallery/two.jpg'), $society->gallery_image_urls[1]);
    }

    #[Test]
    public function it_resolves_friar_and_council_chairperson_photo_urls_for_public_pages(): void
    {
        $friar = new Friar(['photo' => 'friars/leader.jpg']);
        $chairperson = new PastoralCouncilChairperson(['photo' => 'storage/pastoral-council/chair.jpg']);

        $this->assertSame(MediaStorage::url('friars/leader.jpg'), $friar->photo_url);
        $this->assertSame(MediaStorage::url('storage/pastoral-council/chair.jpg'), $chairperson->photo_url);
    }
}
