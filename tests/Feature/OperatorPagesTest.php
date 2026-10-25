<?php

namespace Tests\Feature;

use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_listing_displays_highest_rarity_operator(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertOk()->assertSee('Aak');
    }

    public function test_operator_search_returns_matching_operator(): void
    {
        $this->seed();

        $response = $this->get('/?q=Kroos');

        $response->assertOk()->assertSee('Kroos');
    }

    public function test_seeder_imports_complete_roster_with_local_assets(): void
    {
        $this->seed();

        $this->assertSame(417, Operator::query()->count());
        $this->assertNotNull(Operator::where('slug', 'amiya')->first());
        $this->assertNotNull(Operator::where('slug', 'kaltsit-esperanta')->first());

        foreach (Operator::query()->cursor() as $operator) {
            $assets = [
                $operator->image,
                $operator->portrait,
                'img/full/'.$operator->char_id.'.webp',
            ];

            foreach ($assets as $asset) {
                $path = public_path($asset);
                $imageInfo = @getimagesize($path);

                $this->assertFileExists($path, "Missing local asset for {$operator->name}: {$asset}");
                $this->assertNotFalse($imageInfo, "Invalid local asset for {$operator->name}: {$asset}");
                $this->assertSame(IMAGETYPE_WEBP, $imageInfo[2], "Asset is not WebP for {$operator->name}: {$asset}");
            }
        }
    }

    public function test_operator_list_filters_by_class_and_rarity(): void
    {
        $this->seed();

        $response = $this->get(route('home', ['class' => 'Caster', 'rarity' => 6]));

        $response->assertOk()->assertSee('Ceobe')->assertDontSee('Aak');
    }

    public function test_operator_detail_page_loads(): void
    {
        $this->seed();

        $operator = Operator::where('slug', 'silverash')->first();
        $response = $this->get(route('operators.show', $operator));
        $response->assertOk()->assertSee('SilverAsh');
    }

    public function test_planner_shows_selected_operator(): void
    {
        $this->seed();

        $response = $this->get(route('planner.show', ['operator' => 'myrtle']));
        $response->assertOk()->assertSee('Myrtle');
    }

    public function test_calculation_requires_different_target(): void
    {
        $this->seed();

        $response = $this->get(route('planner.show', [
            'operator' => 'kroos',
            'from_elite' => 0,
            'from_level' => 1,
            'to_elite' => 0,
            'to_level' => 1,
        ]));
        $response->assertOk()->assertSee('berbeda');
    }

    public function test_calculation_computes_exp_for_rarity_three(): void
    {
        $this->seed();

        $response = $this->get(route('planner.show', [
            'operator' => 'kroos',
            'from_elite' => 0,
            'from_level' => 1,
            'to_elite' => 1,
            'to_level' => 55,
        ]));
        $response->assertOk()->assertSee('115,400');
    }
}
