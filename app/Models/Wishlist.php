<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    /** @use HasFactory<\Database\Factories\WishlistFactory> */
    use HasFactory;

    protected $fillable = [
        "user_id",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,         // target akhir
            WishlistItem::class,    // tabel perantara
            'wishlist_id',          // FK di WishlistItem → Wishlist
            'id',                   // PK di Product
            'id',                   // PK di Wishlist
            'product_id'            // FK di WishlistItem → Product
        );
    }
}
