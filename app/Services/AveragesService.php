<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AveragesService
{
    public function calculateAverage(int $type, string $table, $column)
    {
        $values = DB::table($table)
            ->get($column)
            ->toArray();

        $sum = 0;
        $count = count($values);

        foreach ($values as $value) {
            $sum += $value->$column;
        }

        if ($count === 0) {
            return;
        }

        $average = round($sum / $count, 2);

        $averages = DB::table('averages')->where('type', $type);

        if ($averages->exists()) {
            $averages->update([
                'value' => $average
            ]);

            return;
        }

        $averages->insert([
            'type' => $type,
            'value' => $average
        ]);
    }
}
