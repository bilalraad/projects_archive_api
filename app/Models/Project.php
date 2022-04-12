<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $casts = [
        'key_words' => 'array',
    ];

    /**
     * Get the comments for the discussions post.
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     *@var array
     */
    // protected $fillable = [];
}
