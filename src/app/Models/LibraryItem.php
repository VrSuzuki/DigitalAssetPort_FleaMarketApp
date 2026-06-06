<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryItem extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content_id', 'order_item_id', 'added_type'];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
