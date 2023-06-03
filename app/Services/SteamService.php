<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class SteamService
{
    /**
     * Retrieve game information from Steam based on the provided App ID.
     *
     * @param int $appid The Steam App ID of the game.
     * @return array|null The game information as an array, or null if the information is not available.
     */
    public function getGameInfoFromSteam($appid): ?array
    {
        $reponse = Http::get("https://store.steampowered.com/api/appdetails?appids=$appid");

        return Arr::flatten(json_decode($reponse, true), 1)[1] ?? null;
    }

    /**
     * Retrieve reviews for a Steam game.
     *
     * @param int $appid The Steam App ID of the game.
     * @param array $options An array of optional parameters for filtering the reviews.
     *                       Available options: filter<string>, language<string>,
     *                       day_range<string>, cursor<string>, review_type<string>,
     *                       purchase_type<string>, num_per_page<string>.
     * @return array|null The decoded JSON response containing the reviews as an array, or null on failure.
     */
    public function getReviews($appid, array $options = []): ?array
    {
        $queryString = http_build_query($options, '', '&');

        $url = "https://store.steampowered.com/appreviews/$appid?json=1";

        $url = $queryString ? "$url&$queryString" : $url;

        return json_decode(Http::get($url), true);
    }

    /**
     * Retrieves news for a specific app from the Steam API.
     *
     * @param int $appid The ID of the app for which to retrieve news.
     * @param array $options Additional options for the news request (optional).
     * @return array|null An array containing the news information or null if the request fails.
     */
    public function getNews($appid, array $options = []): ?array
    {
        $queryString = http_build_query($options, '', '&');

        $url = "https://api.steampowered.com/ISteamNews/GetNewsForApp/v2?appid=$appid";

        $url = $queryString ? "$url&$queryString" : $url;

        return json_decode(Http::get($url), true);
    }
}
