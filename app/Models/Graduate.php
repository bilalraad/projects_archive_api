<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// ! NOTE: THIS MODEL IS NO LONGER BEING USED
class Graduate extends Model
{
    use HasFactory;

    protected $connection = "mysql2";
    protected $table = "graduates";

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'name_en',
        'email',
        'password',
        "phone",
        "address",
    ];
}