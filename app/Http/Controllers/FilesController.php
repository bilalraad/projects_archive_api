<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;


class FilesController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'files' => ["required", "array", "max:2"],
            'files.*' => 'mimes:doc,docx,pdf,vnd.openxmlformats-officedocument.wordprocessingml.document',
            'project_id' => 'required',
        ]);

        $files = $data['files'];
        foreach ($files as $file) {
            $path = Storage::putFile('files/' . $data["project_id"], $file);
            $name = $file->getClientOriginalName();
            $save = new File();
            $save->title = $name;
            $save->path = $path;
            $save->project_id = $data["project_id"];

            $save->save();
        };
        return response()->json([
            "message" => "تم رفع الملفات بنجاح",
        ]);
    }


    public function show($id)
    {
        return File::select('*')->where(function ($q) use ($id) {
            // $q->where('project_id', '=',  $id);
        })->get();
    }


    public function download($id, $filename)
    {
        $file = File::where('path', 'like', 'files/' . $id . '/' . $filename)->first();
        return Storage::download('files/' . $id . '/' . $filename, $file->title);
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'files.required' => 'Please add files',
            'project_id.required' => 'A project_id is required',
            'project_id.mimes' => 'A project_id is required',

        ];
    }
}
