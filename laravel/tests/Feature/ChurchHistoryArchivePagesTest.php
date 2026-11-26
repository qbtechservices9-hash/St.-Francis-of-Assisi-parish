<?php

namespace Tests\Feature;

use App\Models\Friar;
use App\Models\PastoralCouncilChairperson;
use App\Models\Society;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChurchHistoryArchivePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_links_to_the_new_history_archives(): void
    {
        $response = $this->get(route('about'));

        $response->assertOk();
        $response->assertSee('Church History');
        $response->assertSee(route('history.societies'), false);
        $response->assertSee(route('history.friars'), false);
        $response->assertSee(route('history.pastoral-council'), false);
    }

    public function test_societies_page_only_shows_published_societies(): void
    {
        Society::query()->create([
            'name' => 'Catholic Youth Organisation',
            'brief_history' => 'The youth society has supported prayer, formation, and parish service for many years.',
            'cover_image' => null,
            'gallery' => [],
            'order' => 1,
            'is_published' => true,
        ]);

        Society::query()->create([
            'name' => 'Hidden Society',
            'brief_history' => 'This should not be visible.',
            'cover_image' => null,
            'gallery' => [],
            'order' => 2,
            'is_published' => false,
        ]);

        $response = $this->get(route('history.societies'));

        $response->assertOk();
        $response->assertSee('Catholic Youth Organisation');
        $response->assertSee('The youth society has supported prayer, formation, and parish service for many years.');
        $response->assertDontSee('Hidden Society');
    }

    public function test_friars_and_chairpersons_pages_render_their_timelines(): void
    {
        Friar::query()->create([
            'name' => 'Fr. Michael Dery',
            'photo' => null,
            'served_from_year' => 1994,
            'served_to_year' => 2002,
            'collaborators' => ['Br. Joseph Mensah', 'Fr. Peter Ofori'],
            'notes' => 'He oversaw parish expansion and community outreach.',
            'order' => 0,
            'is_published' => true,
        ]);

        PastoralCouncilChairperson::query()->create([
            'name' => 'Mrs. Agnes Asare',
            'photo' => null,
            'served_from_year' => 2014,
            'served_to_year' => 2018,
            'notes' => 'Guided the council during the parish hall renovation.',
            'order' => 0,
            'is_published' => true,
        ]);

        $friarsResponse = $this->get(route('history.friars'));
        $chairpersonsResponse = $this->get(route('history.pastoral-council'));

        $friarsResponse->assertOk();
        $friarsResponse->assertSee('Fr. Michael Dery');
        $friarsResponse->assertSee('1994 - 2002');
        $friarsResponse->assertSee('Br. Joseph Mensah');

        $chairpersonsResponse->assertOk();
        $chairpersonsResponse->assertSee('Mrs. Agnes Asare');
        $chairpersonsResponse->assertSee('2014 - 2018');
        $chairpersonsResponse->assertSee('Guided the council during the parish hall renovation.');
    }
}
