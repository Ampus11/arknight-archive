<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'doctor_name',
        'body',
    ];

    /** @return BelongsTo<Operator, Comment> */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}
