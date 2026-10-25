<?php

namespace App\Services;

use App\Models\Operator;
use App\Models\PromotionCost;

class PlannerService
{
    public function __construct(private LevelingCalculator $leveling) {}

    /**
     * Build the upgrade plan for an operator between two elite/level states.
     *
     * @param  array<int|string, int>  $owned  Material id => amount the doctor already owns.
     * @param  int  $ownedLmd  LMD the doctor already owns.
     * @return array{
     *     ok: bool,
     *     error: ?string,
     *     exp: int,
     *     lmd_total: int,
     *     lmd_leveling: int,
     *     lmd_promotion: int,
     *     lmd_owned: int,
     *     lmd_missing: int,
     *     promotions: list<int>,
     *     materials: list<array{id: int, name: string, emoji: string, color: string, tier: int, need: int, have: int, missing: int}>,
     *     records: list<array{name: string, emoji: string, exp: int, count: int}>,
     * }
     */
    public function calculate(Operator $operator, int $fromElite, int $fromLevel, int $toElite, int $toLevel, array $owned = [], int $ownedLmd = 0): array
    {
        $error = $this->validate($operator, $fromElite, $fromLevel, $toElite, $toLevel);

        if ($error !== null) {
            return $this->emptyResult($error, $ownedLmd);
        }

        $rarity = $operator->rarity;
        $exp = 0;
        $lmdLeveling = 0;
        $lmdPromotion = 0;
        $promotions = [];

        for ($elite = $fromElite; $elite <= $toElite; $elite++) {
            $cap = (int) $this->leveling->maxLevel($rarity, $elite);
            $start = $elite === $fromElite ? $fromLevel : 1;
            $end = $elite === $toElite ? $toLevel : $cap;

            $exp += $this->leveling->expBetween($rarity, $elite, $start, $end);
            $lmdLeveling += $this->leveling->lmdBetween($rarity, $elite, $start, $end);

            if ($elite < $toElite) {
                $promotion = $elite + 1;
                $promotions[] = $promotion;
                $lmdPromotion += $this->leveling->promotionLmd($rarity, $promotion);
            }
        }

        $materials = $this->materialRequirements($operator, $promotions, $owned);

        $records = collect(config('arknights.battle_records'))
            ->map(fn (array $record): array => [
                'name' => $record['name'],
                'emoji' => $record['emoji'],
                'exp' => $record['exp'],
                'count' => $exp > 0 ? (int) ceil($exp / $record['exp']) : 0,
            ])
            ->values()
            ->all();

        $lmdTotal = $lmdLeveling + $lmdPromotion;

        return [
            'ok' => true,
            'error' => null,
            'exp' => $exp,
            'lmd_total' => $lmdTotal,
            'lmd_leveling' => $lmdLeveling,
            'lmd_promotion' => $lmdPromotion,
            'lmd_owned' => $ownedLmd,
            'lmd_missing' => max(0, $lmdTotal - $ownedLmd),
            'promotions' => $promotions,
            'materials' => $materials,
            'records' => $records,
        ];
    }

    private function validate(Operator $operator, int $fromElite, int $fromLevel, int $toElite, int $toLevel): ?string
    {
        $rarity = $operator->rarity;

        if ($toElite < $fromElite || ($toElite === $fromElite && $toLevel < $fromLevel)) {
            return 'Target tidak boleh lebih rendah dari posisi saat ini.';
        }

        if ($toElite === $fromElite && $toLevel === $fromLevel) {
            return 'Pilih target yang berbeda dari posisi saat ini.';
        }

        $fromCap = $this->leveling->maxLevel($rarity, $fromElite);
        $toCap = $this->leveling->maxLevel($rarity, $toElite);

        if ($fromCap === null) {
            return "Operator {$rarity}★ tidak bisa berada di Elite {$fromElite}.";
        }

        if ($toCap === null) {
            return "Operator {$rarity}★ tidak bisa mencapai Elite {$toElite}.";
        }

        if ($fromLevel < 1 || $fromLevel > $fromCap) {
            return "Level awal harus antara 1 dan {$fromCap} untuk Elite {$fromElite}.";
        }

        if ($toLevel < 1 || $toLevel > $toCap) {
            return "Level target harus antara 1 dan {$toCap} untuk Elite {$toElite}.";
        }

        return null;
    }

    /**
     * Aggregate promotion material requirements and compare against owned stock.
     *
     * @param  array<int|string, int>  $owned
     * @return list<array{id: int, name: string, emoji: string, color: string, tier: int, need: int, have: int, missing: int}>
     */
    private function materialRequirements(Operator $operator, array $promotions, array $owned): array
    {
        if ($promotions === []) {
            return [];
        }

        $rows = PromotionCost::with('material')
            ->forOperator($operator->rarity, $promotions, $operator->class)
            ->get();

        return $rows
            ->groupBy(fn (PromotionCost $row): int => $row->material_id)
            ->map(function ($group) use ($owned) {
                /** @var PromotionCost $first */
                $first = $group->first();
                $need = (int) $group->sum('qty');
                $have = max(0, (int) ($owned[$first->material_id] ?? 0));

                return [
                    'id' => $first->material_id,
                    'name' => $first->material->name,
                    'emoji' => $first->material->emoji,
                    'color' => $first->material->color,
                    'tier' => $first->material->tier,
                    'need' => $need,
                    'have' => $have,
                    'missing' => max(0, $need - $have),
                ];
            })
            ->sortBy([['tier', 'asc'], ['name', 'asc']])
            ->values()
            ->all();
    }

    /**
     * @param  array<int|string, int>  $owned
     * @return array{
     *     ok: bool,
     *     error: ?string,
     *     exp: int,
     *     lmd_total: int,
     *     lmd_leveling: int,
     *     lmd_promotion: int,
     *     lmd_owned: int,
     *     lmd_missing: int,
     *     promotions: list<int>,
     *     materials: list<array{id: int, name: string, emoji: string, color: string, tier: int, need: int, have: int, missing: int}>,
     *     records: list<array{name: string, emoji: string, exp: int, count: int}>,
     * }
     */
    private function emptyResult(string $error, int $ownedLmd = 0): array
    {
        return [
            'ok' => false,
            'error' => $error,
            'exp' => 0,
            'lmd_total' => 0,
            'lmd_leveling' => 0,
            'lmd_promotion' => 0,
            'lmd_owned' => $ownedLmd,
            'lmd_missing' => 0,
            'promotions' => [],
            'materials' => [],
            'records' => [],
        ];
    }
}
