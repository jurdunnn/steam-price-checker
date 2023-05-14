<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(string $search)
    {
        Game::flushEventListeners();

        return Game::select(DB::raw('id, title, CASE WHEN title = "' . $search . '" THEN 0 ELSE 1 END as priority'))
            ->where('title', 'LIKE', '%' . $search . '%')
            ->orderByRaw('priority ASC')
            ->limit(5)
            ->get();
    }

    public function get(Request $request, string $id)
    {
        $options = $request->input('options');

        $options = json_decode($options, true);

        $game = Game::where('id', $id)->with('image')
            ->with('modifiers')
            ->first();

        // If an option is false, add where clause to remove it.
        // We do not want to add the were clause for true, as these filters
        // are for filtering out, not limiting to.

        $options = array_filter($options, fn ($option) => $option == false);

        foreach ($options as $option => $value) {
            if ($game->metas->$option == 1) {
                return [
                    'errors' => 'Filtered Out',
                ];
            }
        }

        return $game;
    }
}
