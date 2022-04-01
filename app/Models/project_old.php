<?php

namespace App\Models;


use Illuminate\Support\Facades\File;

class Project
{

    protected $casts = [
        'key_words' => 'array',
    ];

    public static function getAll()
    {

        $jsonData = __DIR__ . "/../resources/jsonData/studentprojects.json";

        if (file_exists($jsonData = resource_path("jsonData/studentprojects.json"))) {
            return cache()->remember('projects.all', 0, fn () => response(File::get($jsonData), 200)->send());
        }

        return response('not found', 404)->send();
    }

    public static function getById($id)
    {

        $jsonData = __DIR__ . "/../resources/jsonData/studentprojects.json";

        if (file_exists($jsonData = resource_path("jsonData/studentprojects.json"))) {
            $project = json_decode(File::get($jsonData));

            return cache()->remember('projects.all', 0, fn () => response(File::get($jsonData), 200)->send());
        }

        return response('not found', 404)->send();
    }
}