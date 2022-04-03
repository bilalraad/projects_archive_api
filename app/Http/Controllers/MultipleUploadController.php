<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;

class MultipleUploadController extends Controller
{
    public function store(Request $request)
    {


        $data = $request->validate([
            'files' => 'required|mimes:doc,docx,pdf,vnd.openxmlformats-officedocument.wordprocessingml.document',
            'project_id' => 'required',

        ]);
        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 401);
        // }

        dd($request->file('files'));

        // if ($files = $data['files']) {
        $paths = [];
        foreach ($request->files('files') as
            $key => $file) {
            dd($file);
            $path = $file->store('public/files');
            $name = $file->getClientOriginalName();
            $paths[$key] = $name;

            //store your file into directory and db
            $save = new File();
            $save->title = $name;
            $save->path = $path;
            $save->project_id = $data["project_id"];

            $save->save();
        }
        return response()->json([
            "success" => true,
            "message" => "Files successfully uploaded",
            "file" => $paths,
        ]);
        // }
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