<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionCost extends Model
{
    protected $fillable = [
        'rarity',
        'promotion',
        'class',
        'material_id',
        'qty',
    ];

    /** @return BelongsTo<Material, PromotionCost> */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Promotion material rows that apply to a given rarity and class.
     *
     * Rows with a null class are generic materials required by every class.
     */
    public function scopeForOperator(Builder $query, int $rarity, array $promotions, string $class): Builder
    {
        return $query
            ->where('rarity', $rarity)
            ->whereIn('promotion', $promotions)
            ->where(function (Builder $inner) use ($class): void {
                $inner->whereNull('class')->orWhere('class', $class);
            });
    }
}
