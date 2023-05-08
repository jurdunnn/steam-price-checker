<?php

namespace Tests\Feature;

use App\Services\SteamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SteamServiceTest extends TestCase
{
    public function test_steam_search_returns_app_ids(): void
    {
        $steam = new SteamService;

        $reponse = $steam->search('Hello Neighbor');

        $this->assertTrue(is_array($reponse));
    }

    public function test_steam_search_has_correct_app_id(): void
    {
        $appid = '521890';

        $searchString = 'Hello Neighbor';

        $steam = new SteamService;

        $reponse = $steam->search($searchString);

        $this->assertTrue(in_array($appid, $reponse));
    }
}
