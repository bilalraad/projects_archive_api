<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class File extends Model
{
    use HasFactory;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public static function create($file, $project_id)
    {
        $path = Storage::putFile('files/' . $project_id, $file);
        $name = $file->getClientOriginalName();
        $save = new File();
        $save->title = $name;
        $save->path = $path;
        $save->project_id = $project_id;

        $save->save();
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // static::deleted(function ($file) {});
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'project_id'
    ];
}