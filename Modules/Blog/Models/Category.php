<?php

namespace Modules\Blog\Models;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Blog\Database\Factories\CategoryFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blog_categories';

    protected $fillable = [
        'parent_id',
        'sort_order',
        'is_active',
        'image_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public static $sortable = [
        'id',
        'sort_order',
        'created_at',
    ];

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }

    public function translation(?string $locale = null): HasMany
    {
        return $this->translations()->where('locale', $locale ?? app()->getLocale());
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'blog_post_category', 'category_id', 'post_id');
    }

    public function image(): MorphOne
    {
        return $this->morphOne(File::class, 'model')
            ->where('type', 'image')
            ->where('id', $this->image_id)
            ->latest();
    }


    public function getImageSizes(): array
    {
        return [
            'medium' => [600, 600],
            'small' => [300, 300],
            'thumbnail' => [100, 100],
        ];
    }
}
