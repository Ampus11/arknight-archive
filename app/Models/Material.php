<?php

namespace App\Models;

use Database\Factories\MaterialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    /** @use HasFactory<MaterialFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'tier',
        'emoji',
        'color',
    ];

    /** @return HasMany<PromotionCost> */
    public function promotionCosts(): HasMany
    {
        return $this->hasMany(PromotionCost::class);
    }
}
