<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'title', 'description', 'image', 'tags', 'slug', 'category_id'];

    /**
     * @var string[]
     */
    protected $casts = [
        'tags' => 'array',
    ];

    protected $appends = [
        'full_path'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * @return string|void
     */
    public function getFullPathAttribute()
    {
        if ($this->image) {
            return config('app.url') . '/storage/' . $this->image;
        }
    }

    /**
     * @param $query
     * @param $filters
     * @return mixed
     */
    public function scopeFilters($query, $filters)
    {
        return $query->when(isset($filters['tags']), function($q) use ($filters) {
            $search = trim($filters['tags']);
            return $q->where('tags', 'like', "%\"{$search}\"%");
        })->when(isset($filters['category']), function ($q) use ($filters) {
            return $q->where('category_id', $filters['category']);
        });
    }
}
