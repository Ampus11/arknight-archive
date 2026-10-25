<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Operator;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['q', 'class', 'rarity', 'nation', 'sort']);

        $query = Operator::query()->filter($filters);

        match ($filters['sort'] ?? 'rarity') {
            'name' => $query->orderBy('name'),
            'class' => $query->orderBy('class')->orderByDesc('rarity')->orderBy('name'),
            default => $query->orderByDesc('rarity')->orderBy('name'),
        };

        $operators = $query->paginate(36)->withQueryString();

        $doctor = session('doctor_name');
        $favoriteIds = $doctor
            ? Favorite::where('doctor_name', $doctor)->pluck('operator_id')
            : collect();

        $classCounts = Operator::query()
            ->selectRaw('class, count(*) as aggregate')
            ->groupBy('class')
            ->pluck('aggregate', 'class');

        return view('home', [
            'operators' => $operators,
            'filters' => $filters,
            'favoriteIds' => $favoriteIds,
            'classCounts' => $classCounts,
            'nations' => Operator::query()
                ->whereNotNull('nation')
                ->distinct()
                ->orderBy('nation')
                ->pluck('nation'),
            'stats' => [
                'total' => Operator::query()->count(),
                'sixStar' => Operator::query()->where('rarity', 6)->count(),
                'classes' => Operator::query()->distinct()->count('class'),
                'nations' => Operator::query()->distinct()->count('nation'),
            ],
        ]);
    }
}
