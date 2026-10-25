<?php

namespace App\Models;

use Database\Factories\FavoriteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    /** @use HasFactory<FavoriteFactory> */
    use HasFactory;

    protected $fillable = [
        'doctor_name',
        'operator_id',
    ];

    /** @return BelongsTo<Operator, Favorite> */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}
