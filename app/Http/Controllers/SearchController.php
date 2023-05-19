<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request, string $search)
    {
        Game::flushEventListeners();

        $limit = json_decode($request->input('limit'), true);

        $games = Game::search($search);

        if ($limit) {
            $games->take($limit);
        }

        return $games->get();
    }

    public function get(Request $request, string $id)
    {
        $options = $request->input('options');

        $options = json_decode($options, true);

        $game = Game::where('id', $id)
            ->with('images')
            ->with('modifiers')
            ->with('metas')
            ->first();

        // Do not pass game to view if it does not have all required data
        if ($game->doesNotHaveRequiredData()) {
            return [
                'errors' => 'Missing Data',
            ];
        }

        // Do not pass game to view if game matches a filter.
        $options = array_filter($options, fn ($option) => $option == false);

        foreach ($options as $option => $value) {
            if ($game->metas->type == $option) {
                return [
                    'errors' => 'Filtered Out',
                ];
            }

            if ($option == 'free' && $game->metas->free == 1) {
                return [
                    'errors' => 'Filtered Out',
                ];
            }

            if ($option == 'unreleased' && $game->metas->unreleased == 1) {
                return [
                    'errors' => 'Filtered Out',
                ];
            }
        }

        return $game;
    }
}
