<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Operator;
use App\Models\PromotionCost;
use Illuminate\Database\Seeder;
use RuntimeException;

class ArknightsSeeder extends Seeder
{
    /**
     * Official rarity colors, used for card accents and badges.
     *
     * @var array<int, string>
     */
    private const RARITY_COLORS = [
        1 => '#9aa0a6',
        2 => '#9ee493',
        3 => '#5fa8ff',
        4 => '#c07bff',
        5 => '#ffd54a',
        6 => '#ff9f1c',
    ];

    /**
     * Material tier colors.
     *
     * @var array<int, string>
     */
    private const TIER_COLORS = [
        1 => '#9aa0ab',
        2 => '#58b368',
        3 => '#4f8fd8',
        4 => '#a06fd0',
        5 => '#e05a5a',
    ];

    public function run(): void
    {
        $materials = $this->seedMaterials();
        $this->seedOperators();
        $this->seedPromotionCosts($materials);
        $this->seedSampleComments();
    }

    /** @return array<string, Material> */
    private function seedMaterials(): array
    {
        $catalog = [
            // Tier 1
            'Damaged Device' => 1, 'Diketon' => 1, 'Ester' => 1,
            'Orirock' => 1, 'Oriron Shard' => 1, 'Sugar Substitute' => 1,
            // Tier 2
            'Device' => 2, 'Orirock Cube' => 2, 'Oriron' => 2,
            'Polyester' => 2, 'Polyketon' => 2, 'Sugar' => 2,
            // Tier 3
            'Aketon' => 3, 'Coagulating Gel' => 3, 'Grindstone' => 3,
            'Incandescent Alloy' => 3, 'Integrated Device' => 3,
            'Loxic Kohl' => 3, 'Manganese Ore' => 3, 'Orirock Cluster' => 3,
            'Oriron Cluster' => 3, 'Polyester Pack' => 3, 'RMA70-12' => 3,
            'Sugar Pack' => 3,
            // Tier 4
            'Grindstone Pentahydrate' => 4, 'Incandescent Alloy Block' => 4,
            'Keton Colloid' => 4, 'Manganese Trihydrate' => 4,
            'Optimized Device' => 4, 'Orirock Concentration' => 4,
            'Oriron Block' => 4, 'Polyester Lump' => 4,
            'Polymerized Gel' => 4, 'RMA70-24' => 4, 'Sugar Lump' => 4,
            'White Horse Kohl' => 4,
            // Tier 5
            'Bipolar Nanoflake' => 5, 'Crystalline Electronic Unit' => 5,
            'D32 Steel' => 5, 'Nucleic Crystal Sinter' => 5,
            'Polymerization Preparation' => 5,
        ];

        $materials = [];

        foreach ($catalog as $name => $tier) {
            $materials[$name] = Material::updateOrCreate(['name' => $name], [
                'tier' => $tier,
                'emoji' => '',
                'color' => self::TIER_COLORS[$tier],
            ]);
        }

        foreach (Operator::classes() as $class) {
            $chipTypes = [
                ' Chip' => 2,
                ' Chip Pack' => 3,
                ' Dualchip' => 4,
            ];

            foreach ($chipTypes as $suffix => $tier) {
                $name = $class.$suffix;
                $materials[$name] = Material::updateOrCreate(['name' => $name], [
                    'tier' => $tier,
                    'emoji' => '',
                    'color' => self::TIER_COLORS[$tier],
                ]);
            }
        }

        return $materials;
    }

    private function seedOperators(): void
    {
        $path = database_path('data/operators.json');

        if (! is_file($path)) {
            throw new RuntimeException("Operator dataset not found at {$path}.");
        }

        /** @var list<array<string, mixed>> $dataset */
        $dataset = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        $slugs = array_column($dataset, 'slug');

        Operator::query()->whereNotIn('slug', $slugs)->delete();

        foreach ($dataset as $row) {
            $rarity = (int) $row['rarity'];

            Operator::updateOrCreate(['slug' => $row['slug']], [
                'char_id' => $row['char_id'],
                'name' => $row['name'],
                'rarity' => $rarity,
                'class' => $row['class'],
                'subclass' => $row['subclass'] ?: $row['class'],
                'position' => $row['position'],
                'nation' => $row['nation'] ?: 'Unaffiliated',
                'display_number' => $row['display_number'] ?: null,
                'tags' => $row['tags'] ?: [],
                'description' => $row['description'] ?: $row['usage'],
                'usage' => $row['usage'] ?: null,
                'accent' => self::RARITY_COLORS[$rarity] ?? '#9aa0a6',
                'image' => $row['image'],
                'portrait' => $row['portrait'],
            ]);
        }
    }

    /** @param  array<string, Material>  $materials */
    private function seedPromotionCosts(array $materials): void
    {
        PromotionCost::query()->delete();

        /**
         * Generic (class-agnostic) material rows per rarity and promotion,
         * using approximate quantities based on the official wiki ranges.
         *
         * @var array<int, array<int, array<string, int>>>
         */
        $generic = [
            3 => [
                1 => [],
            ],
            4 => [
                1 => ['Oriron' => 4, 'Sugar' => 4, 'Orirock Cube' => 4],
                2 => ['Oriron Cluster' => 6, 'Sugar Pack' => 6, 'Polyester Pack' => 4, 'Integrated Device' => 4],
            ],
            5 => [
                1 => ['Oriron' => 8, 'Sugar' => 6, 'Polyester' => 6, 'Device' => 4],
                2 => ['Loxic Kohl' => 6, 'Manganese Ore' => 6, 'Grindstone' => 4, 'RMA70-12' => 4, 'Oriron Block' => 2, 'White Horse Kohl' => 2],
            ],
            6 => [
                1 => ['Oriron' => 12, 'Sugar' => 10, 'Polyester' => 8, 'Device' => 6, 'Polyketon' => 6],
                2 => ['Oriron Block' => 6, 'White Horse Kohl' => 6, 'Optimized Device' => 4, 'Keton Colloid' => 4, 'Manganese Trihydrate' => 4, 'D32 Steel' => 2],
            ],
        ];

        /**
         * Class-specific chip counts per rarity and promotion.
         *
         * @var array<int, array<int, int>>
         */
        $chips = [
            3 => [1 => 0, 2 => 0],
            4 => [1 => 3, 2 => 5],
            5 => [1 => 4, 2 => 3],
            6 => [1 => 5, 2 => 4],
        ];

        foreach ($generic as $rarity => $promotions) {
            foreach ($promotions as $promotion => $rows) {
                foreach ($rows as $materialName => $qty) {
                    PromotionCost::create([
                        'rarity' => $rarity,
                        'promotion' => $promotion,
                        'class' => null,
                        'material_id' => $materials[$materialName]->id,
                        'qty' => $qty,
                    ]);
                }
            }
        }

        foreach ($chips as $rarity => $promotions) {
            foreach ($promotions as $promotion => $count) {
                if ($count === 0) {
                    continue;
                }

                $chipName = $promotion === 1 ? ' Chip' : ' Chip Pack';

                foreach (Operator::classes() as $class) {
                    PromotionCost::create([
                        'rarity' => $rarity,
                        'promotion' => $promotion,
                        'class' => $class,
                        'material_id' => $materials[$class.$chipName]->id,
                        'qty' => $count,
                    ]);
                }
            }
        }

        // E2 for 5-6 star operators uses Dualchips instead of Chip Packs.
        PromotionCost::query()
            ->where('promotion', 2)
            ->where('rarity', '>=', 5)
            ->whereNotNull('class')
            ->delete();

        foreach ([5, 6] as $rarity) {
            $count = $chips[$rarity][2];

            foreach (Operator::classes() as $class) {
                PromotionCost::create([
                    'rarity' => $rarity,
                    'promotion' => 2,
                    'class' => $class,
                    'material_id' => $materials[$class.' Dualchip']->id,
                    'qty' => $count,
                ]);
            }
        }
    }

    private function seedSampleComments(): void
    {
        $samples = [
            ['kroos', 'Doctor Bunny', 'Kroos selalu jadi andalan awal game, recommend banget buat di-level.'],
            ['silverash', 'SchwingSchwing', 'True Silver Slash memang juara, tapi biaya E2-nya lumayan juga.'],
            ['myrtle', 'LMD Farmer', 'Myrtle wajib dibangun, DP-nya bikin early game jadi mudah.'],
        ];

        foreach ($samples as [$slug, $doctor, $body]) {
            $operator = Operator::where('slug', $slug)->first();

            if ($operator === null) {
                continue;
            }

            $operator->comments()->firstOrCreate(
                ['doctor_name' => $doctor, 'body' => $body],
            );
        }
    }
}
