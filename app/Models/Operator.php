<?php

namespace App\Models;

use Database\Factories\OperatorFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Operator extends Model
{
    /** @use HasFactory<OperatorFactory> */
    use HasFactory;

    protected $fillable = [
        'char_id',
        'name',
        'slug',
        'rarity',
        'class',
        'subclass',
        'position',
        'nation',
        'display_number',
        'tags',
        'description',
        'usage',
        'accent',
        'image',
        'portrait',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'rarity' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Operator $operator): void {
            if (blank($operator->slug)) {
                $operator->slug = Str::slug($operator->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return HasMany<Comment> */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /** @return HasMany<Favorite> */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * High-resolution art for the operator detail hero, with graceful fallbacks.
     */
    public function artUrl(): string
    {
        if ($this->char_id) {
            $full = 'img/full/'.$this->char_id.'.webp';

            if (is_file(public_path($full))) {
                return asset($full);
            }
        }

        return $this->cardUrl();
    }

    /**
     * Vertical portrait used by archive cards.
     */
    public function cardUrl(): string
    {
        if ($this->portrait && is_file(public_path($this->portrait))) {
            return asset($this->portrait);
        }

        return $this->avatarUrl();
    }

    public function avatarUrl(): string
    {
        return $this->image ? asset($this->image) : asset('img/operators/default.svg');
    }

    /**
     * Filter the operator list by search text, class, rarity and nation.
     *
     * @param  array{q?: string, class?: string, rarity?: string, nation?: string}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (! empty($filters['q'])) {
            $term = trim($filters['q']);

            $query->where(function (Builder $inner) use ($term): void {
                $like = '%'.$term.'%';

                $inner->where('name', 'like', $like)
                    ->orWhere('subclass', 'like', $like)
                    ->orWhere('nation', 'like', $like)
                    ->orWhere('display_number', 'like', $like);
            });
        }

        if (! empty($filters['class'])) {
            $query->where('class', $filters['class']);
        }

        if (! empty($filters['rarity'])) {
            $query->where('rarity', (int) $filters['rarity']);
        }

        if (! empty($filters['nation'])) {
            $query->where('nation', $filters['nation']);
        }

        return $query;
    }

    /** @return list<string> */
    public static function classes(): array
    {
        return [
            'Vanguard',
            'Guard',
            'Defender',
            'Sniper',
            'Caster',
            'Medic',
            'Supporter',
            'Specialist',
        ];
    }

    /** @return array<string, string> */
    public static function positions(): array
    {
        return [
            'Melee' => 'Melee',
            'Ranged' => 'Ranged',
        ];
    }
}
