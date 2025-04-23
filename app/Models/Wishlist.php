<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    /** @use HasFactory<\Database\Factories\WishlistFactory> */
    use HasFactory;

    protected $fillable = [
        "customer_id",
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }
}
