<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubGenre extends Model
{
    use HasFactory;

    protected $fillable = ['genre_id', 'name', 'slug'];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
