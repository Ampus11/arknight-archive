<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Operator;
use App\Models\PromotionCost;
use App\Services\LevelingCalculator;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function show(Request $request, Operator $operator, LevelingCalculator $leveling)
    {
        $promotions = [];

        foreach ([1, 2] as $promotion) {
            if (! $leveling->isPromotable($operator->rarity, $promotion)) {
                continue;
            }

            $rows = PromotionCost::with('material')
                ->forOperator($operator->rarity, [$promotion], $operator->class)
                ->get()
                ->groupBy(fn (PromotionCost $row): int => $row->material_id)
                ->map(function ($group) {
                    /** @var PromotionCost $first */
                    $first = $group->first();

                    return [
                        'name' => $first->material->name,
                        'emoji' => $first->material->emoji,
                        'color' => $first->material->color,
                        'tier' => $first->material->tier,
                        'qty' => (int) $group->sum('qty'),
                    ];
                })
                ->sortBy([['tier', 'asc'], ['name', 'asc']])
                ->values()
                ->all();

            $promotions[$promotion] = [
                'lmd' => $leveling->promotionLmd($operator->rarity, $promotion),
                'materials' => $rows,
                'cap' => $leveling->maxLevel($operator->rarity, $promotion),
                'max_level' => $leveling->maxLevel($operator->rarity, $promotion - 1),
            ];
        }

        $doctor = session('doctor_name');

        return view('operators.show', [
            'operator' => $operator,
            'comments' => $operator->comments,
            'isFavorite' => (bool) ($doctor && Favorite::where('doctor_name', $doctor)
                ->where('operator_id', $operator->id)
                ->exists()),
            'promotions' => $promotions,
            'eliteStages' => $leveling->eliteStages($operator->rarity),
        ]);
    }
}
