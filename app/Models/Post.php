<?php

namespace App\Models;

use App\Enums\ModerationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'book_author',
        'slug',
        'description',
        'image',
        'moderation_status',
        'user_rating',
    ];

    public function casts(): array
    {
        return [
            'moderation_status' => ModerationStatus::class,
        ];
    }

    /**
     * Usuário autor do post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Categoria do post.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Post $post): void {
            if (empty($post->slug)) {
                $post->slug = static::generateUniqueSlug($post->title);
            }
        });

        static::updating(function (Post $post): void {
            if ($post->isDirty('title')) {
                $post->slug = static::generateUniqueSlug($post->title, $post->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i    = 1;
        while (static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeByAuthor($q, $userId): mixed
    {
        return $q->where('user_id', $userId);
    }

    public function scopeApproved($q): mixed
    {
        return $q->where('moderation_status', ModerationStatus::Approved);
    }

    /**
     * Todas as avaliações (inclui possivelmente a do autor se também gravada em ratings futuramente)
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Média das avaliações da comunidade (excluindo o user_rating armazenado no próprio post)
     */
    public function getCommunityAverageRatingAttribute(): ?float
    {
        // Se carregado via withAvg('ratings','stars') usar atributo em cache
        if (array_key_exists('ratings_avg_stars', $this->attributes)) {
            $val = $this->attributes['ratings_avg_stars'];

            return $val !== null ? round((float) $val, 1) : null;
        }
        $avg = $this->ratings()->avg('stars');

        return $avg ? round((float) $avg, 1) : null;
    }

    /**
     * Quantidade de avaliações da comunidade
     */
    public function getCommunityRatingsCountAttribute(): int
    {
        if (array_key_exists('ratings_count', $this->attributes)) {
            return (int) $this->attributes['ratings_count'];
        }

        return (int) $this->ratings()->count();
    }
}
