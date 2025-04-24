<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    /** @use HasFactory<\Database\Factories\SellerFactory> */
    use HasFactory;

    protected $fillable = [
        "username",
        "password",
        "email",
        "phone",
        "store_name",
        "bio",
        "last_active_at"
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
