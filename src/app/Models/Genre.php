<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    public function subGenres()
    {
        return $this->hasMany(SubGenre::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
