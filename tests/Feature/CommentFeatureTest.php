<?php

namespace Tests\Feature;

use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_stores_and_redirects(): void
    {
        $this->seed();

        $operator = Operator::where('slug', 'myrtle')->first();

        $response = $this->post(route('comments.store', $operator), [
            'doctor_name' => 'Doctor',
            'body' => 'Barang bagus buat early game.',
        ]);

        $response->assertRedirect(route('operators.show', $operator));
        $this->assertDatabaseHas('comments', [
            'operator_id' => $operator->id,
            'doctor_name' => 'Doctor',
            'body' => 'Barang bagus buat early game.',
        ]);
        $this->assertEquals('Doctor', session('doctor_name'));
    }
}
