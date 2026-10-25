<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Operator;
use App\Services\LevelingCalculator;
use App\Services\PlannerService;
use Illuminate\Http\Request;

class PlannerController extends Controller
{
    public function show(Request $request, PlannerService $planner, LevelingCalculator $leveling)
    {
        $operators = Operator::query()
            ->orderByDesc('rarity')
            ->orderBy('name')
            ->get();

        $selected = null;
        $result = null;

        $owned = array_map('intval', (array) $request->input('owned', []));
        $ownedLmd = max(0, (int) $request->input('owned_lmd', 0));

        $form = [
            'operator' => $request->input('operator'),
            'from_elite' => max(0, (int) $request->input('from_elite', 0)),
            'from_level' => max(1, (int) $request->input('from_level', 1)),
            'to_elite' => max(0, (int) $request->input('to_elite', 0)),
            'to_level' => max(1, (int) $request->input('to_level', 1)),
        ];

        if ($slug = $request->input('operator')) {
            $selected = $operators->firstWhere('slug', $slug);

            if ($selected !== null) {
                $rarity = $selected->rarity;
                $lastElite = $leveling->eliteStages($rarity) - 1;

                $fromElite = max(0, (int) $request->input('from_elite', 0));
                $fromLevel = max(1, (int) $request->input('from_level', 1));
                $toElite = max(0, (int) $request->input('to_elite', $lastElite));
                $toLevel = max(1, (int) $request->input(
                    'to_level',
                    $leveling->maxLevel($rarity, $toElite) ?? 1,
                ));

                $form = [
                    'operator' => $slug,
                    'from_elite' => $fromElite,
                    'from_level' => $fromLevel,
                    'to_elite' => $toElite,
                    'to_level' => $toLevel,
                ];

                $result = $planner->calculate(
                    $selected,
                    $fromElite,
                    $fromLevel,
                    $toElite,
                    $toLevel,
                    $owned,
                    $ownedLmd,
                );
            }
        }

        return view('planner.show', [
            'operators' => $operators,
            'selected' => $selected,
            'result' => $result,
            'owned' => $owned,
            'ownedLmd' => $ownedLmd,
            'form' => $form,
            'materials' => Material::query()
                ->orderBy('tier')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
