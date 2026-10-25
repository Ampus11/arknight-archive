<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promotion_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('rarity');
            $table->unsignedTinyInteger('promotion');
            $table->string('class')->nullable();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('qty');
            $table->timestamps();
            $table->index(['rarity', 'promotion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_costs');
    }
};
