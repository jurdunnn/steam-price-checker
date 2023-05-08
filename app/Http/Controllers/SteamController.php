<?php

namespace App\Http\Controllers;

use App\Services\SteamService;

class SteamController extends Controller
{
    public function get($appid)
    {
        $steam = new SteamService;

        return $steam->get($appid);
    }

    public function search($query)
    {
        $steam = new SteamService;

        return $steam->search($query);
    }
}
