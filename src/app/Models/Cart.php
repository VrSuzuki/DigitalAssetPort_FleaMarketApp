<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function contents()
    {
        return $this->belongsToMany(Content::class, 'cart_items');
    }
}
