<?php

namespace App\Services;

class LevelingCalculator
{
    /**
     * Total EXP required to level from 1 up to the cap of each elite stage,
     * keyed by rarity then elite stage index.
     *
     * Segment totals are calibrated so that a full run (E0L1 up to the rarity
     * cap) matches the published community figures:
     *
     * - 1-2★ E0L30: 9,800 EXP / 6,043 LMD
     * - 3★   E1L55: 115,400 EXP / 104,040 LMD (incl. promotion)
     * - 4★   E2L70: 484,000 EXP / 482,003 LMD (incl. promotions)
     * - 5★   E2L80: 734,400 EXP / 819,325 LMD (incl. promotions)
     * - 6★   E2L90: 1,151,400 EXP / 1,334,796 LMD (incl. promotions)
     *
     * @var array<int, array<int, int>>
     */
    private const SEGMENT_EXP = [
        1 => [0 => 9800],
        2 => [0 => 9800],
        3 => [0 => 26150, 1 => 89250],
        4 => [0 => 32500, 1 => 100000, 2 => 351500],
        5 => [0 => 40000, 1 => 300000, 2 => 394400],
        6 => [0 => 40000, 1 => 380000, 2 => 731400],
    ];

    /**
     * Ratio of level-up LMD cost to EXP cost per rarity, calibrated so that
     * the total LMD of a full run matches the published figures above.
     *
     * @var array<int, float>
     */
    private const LMD_RATIOS = [
        1 => 0.616633,
        2 => 0.616633,
        3 => 0.814905,
        4 => 0.840915,
        5 => 0.925007,
        6 => 0.976894,
    ];

    /**
     * Exponent of the cumulative cost curve inside one elite stage. Higher
     * values push more of the cost towards the tail levels.
     */
    private const GROWTH = 1.8;

    public function maxLevel(int $rarity, int $elite): ?int
    {
        return config("arknights.max_levels.{$rarity}.{$elite}");
    }

    public function eliteStages(int $rarity): int
    {
        $stages = 0;

        for ($elite = 0; $elite < 3; $elite++) {
            if ($this->maxLevel($rarity, $elite) !== null) {
                $stages = $elite + 1;
            }
        }

        return $stages;
    }

    public function isPromotable(int $rarity, int $promotion): bool
    {
        return $this->maxLevel($rarity, $promotion) !== null;
    }

    public function promotionLmd(int $rarity, int $promotion): int
    {
        return (int) config("arknights.promotion_lmd.{$rarity}.{$promotion}", 0);
    }

    /**
     * EXP required to level from $fromLevel to $toLevel inside one elite stage.
     */
    public function expBetween(int $rarity, int $elite, int $fromLevel, int $toLevel): int
    {
        return $this->costBetween($rarity, $elite, 'exp', $fromLevel, $toLevel);
    }

    /**
     * LMD required for the same level range (feeding battle records costs LMD).
     */
    public function lmdBetween(int $rarity, int $elite, int $fromLevel, int $toLevel): int
    {
        return $this->costBetween($rarity, $elite, 'lmd', $fromLevel, $toLevel);
    }

    private function costBetween(int $rarity, int $elite, string $kind, int $fromLevel, int $toLevel): int
    {
        $cap = $this->maxLevel($rarity, $elite);

        if ($cap === null || $toLevel <= $fromLevel) {
            return 0;
        }

        $total = $this->segmentTotal($rarity, $elite, $kind);
        $steps = $cap - 1;

        $cumulative = function (int $level) use ($total, $steps): int {
            $ups = max(0, $level - 1);

            return (int) round($total * (($ups ** self::GROWTH) / ($steps ** self::GROWTH)));
        };

        return $cumulative($toLevel) - $cumulative($fromLevel);
    }

    private function segmentTotal(int $rarity, int $elite, string $kind): int
    {
        $exp = self::SEGMENT_EXP[$rarity][$elite];

        if ($kind === 'exp') {
            return $exp;
        }

        return (int) round($exp * self::LMD_RATIOS[$rarity]);
    }
}
