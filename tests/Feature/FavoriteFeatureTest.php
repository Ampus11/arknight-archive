<?php

namespace Tests\Feature;

use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite_toggle_requires_doctor_name(): void
    {
        $this->seed();

        $operator = Operator::where('slug', 'kroos')->first();

        $response = $this->from('/')->post(route('favorites.toggle', $operator));
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_favorite_toggle_stores_for_named_doctor(): void
    {
        $this->seed();

        $operator = Operator::where('slug', 'kroos')->first();

        $response = $this->withSession(['doctor_name' => 'Doctor'])
            ->post(route('favorites.toggle', $operator));
        $response->assertRedirect();
        $this->assertDatabaseHas('favorites', [
            'operator_id' => $operator->id,
            'doctor_name' => 'Doctor',
        ]);
    }
}
