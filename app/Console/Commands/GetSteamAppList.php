<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;

class GetSteamAppList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:app-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get steam app list using steam api, store publicly.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = config('app.steam_key');

        $file = 'public/steam_apps.json';

        $url = "http://api.steampowered.com/ISteamApps/GetAppList/v0002/?key=$key&format=json";

        $reponse = file_get_contents($url);

        $games = json_decode($reponse, true)['applist']['apps'];

        foreach ($games as $game) {
            $this->info("Creating {$game['name']}");

            if (!Game::where('steam_app_id', $game['appid'])->exists()) {
                Game::create([
                    'steam_app_id' => $game['appid'],
                    'title' => $game['name'],
                ]);
            }
        }

        file_put_contents($file, $reponse);
    }
}
