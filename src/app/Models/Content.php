<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'genre_id',
        'sub_genre_id',
        'title',
        'slug',
        'format',
        'description',
        'price',
        'thumbnail_path',
        'file_path',
        'license_type',
        'environment',
        'file_size_mb',
        'rating_rate',
        'ratings_count',
        'profile_order',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'file_size_mb' => 'decimal:2',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function subGenre()
    {
        return $this->belongsTo(SubGenre::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function images()
    {
        return $this->hasMany(ContentImage::class)->orderBy('sort_order');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail_path) {
            return asset('assets/content-placeholder.svg');
        }

        if (Str::startsWith($this->thumbnail_path, ['http://', 'https://'])) {
            return $this->thumbnail_path;
        }

        if (Str::startsWith($this->thumbnail_path, ['assets/', '/assets/'])) {
            return asset(ltrim($this->thumbnail_path, '/'));
        }

        return asset('storage/'.$this->thumbnail_path);
    }

    public function getFormattedPriceAttribute()
    {
        return $this->price === 0 ? '無料' : '¥'.number_format($this->price);
    }

    public function getRatingLabelAttribute()
    {
        return $this->rating_rate.'%';
    }
}
