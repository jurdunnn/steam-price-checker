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

        return Game::search($search)->take($limit)->get();
    }

    public function get(Request $request, string $id)
    {
        $options = $request->input('options');

        $options = json_decode($options, true);

        $game = Game::where('id', $id)->with('image')
            ->with('modifiers')
            ->with('metas')
            ->first();

        // If an option is false, add where clause to remove it.
        // We do not want to add the were clause for true, as these filters
        // are for filtering out, not limiting to.

        $options = array_filter($options, fn ($option) => $option == false);

        foreach ($options as $option => $value) {
            if ($game->metas->type == $option) {
                return [
                    'errors' => 'Filtered Out',
                ];
            }
        }

        return $game;
    }
}
