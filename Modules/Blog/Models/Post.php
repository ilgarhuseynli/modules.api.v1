<?php

namespace Modules\Blog\Models;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Blog\Database\Factories\PostFactory;
use Modules\Blog\Enums\PostStatus;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blog_posts';

    protected $fillable = [
        'author_id',
        'status',
        'cover_image_id',
        'is_featured',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public static $sortable = [
        'id',
        'sort_order',
        'published_at',
        'created_at',
    ];

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PostTranslation::class, 'post_id');
    }

    public function translation(?string $locale = null): HasMany
    {
        return $this->translations()->where('locale', $locale ?? app()->getLocale());
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'blog_post_category', 'post_id', 'category_id');
    }

    public function cover_image(): MorphOne
    {
        return $this->morphOne(File::class, 'model')
            ->where('type', 'cover_image')
            ->where('id', $this->cover_image_id)
            ->latest();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(File::class, 'model')->where('type', 'images');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'model')->where('type', 'files');
    }

    public function getImageSizes(): array
    {
        return [
            'medium' => [600, 600],
            'small' => [300, 300],
            'thumbnail' => [100, 100],
        ];
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(filled($filters['title'] ?? null), fn ($q) => $q->whereHas('translations', fn ($tq) => $tq->where('title', 'like', '%'.$filters['title'].'%')))
            ->when(filled($filters['status'] ?? null), fn ($q) => $q->where('status', $filters['status']))
            ->when(filled($filters['is_featured'] ?? null), fn ($q) => $q->where('is_featured', $filters['is_featured']))
            ->when(filled($filters['category_id'] ?? null), fn ($q) => $q->whereHas('categories', fn ($cq) => $cq->where('blog_categories.id', $filters['category_id'])))
            ->when(filled($filters['author_id'] ?? null), fn ($q) => $q->where('author_id', $filters['author_id']))
            ->when(filled($filters['locale'] ?? null), fn ($q) => $q->whereHas('translations', fn ($tq) => $tq->where('locale', $filters['locale'])));
    }
}
