<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Seller extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\SellerFactory> */
    use HasFactory, HasApiTokens;

    protected $fillable = [
        "username",
        "password",
        "email",
        "phone",
        "store_name",
        "bio",
        "last_login_at"
    ];

    protected $hidden =[
        "password",
        "role"
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
