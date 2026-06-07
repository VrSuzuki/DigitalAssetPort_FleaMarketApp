<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContentImage extends Model
{
    use HasFactory;

    protected $fillable = ['content_id', 'path', 'sort_order'];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function getUrlAttribute()
    {
        if (Str::startsWith($this->path, ['http://', 'https://'])) {
            return $this->path;
        }

        if (Str::startsWith($this->path, ['assets/', '/assets/'])) {
            return asset(ltrim($this->path, '/'));
        }

        return asset('storage/'.$this->path);
    }
}
