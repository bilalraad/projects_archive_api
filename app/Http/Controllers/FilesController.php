<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;


class FilesController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')
            ->only(['removeFile', 'store', 'update']);
    }
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'files' => ["required", "array", "max:2"],
                'files.*' => 'mimes:doc,docx,pdf,zip,application/octet-stream,application/x-zip-compressed,multipart/x-zip|max:10000',
                'project_id' => 'required',
            ]);
        } catch (\Throwable $th) {
            info($th);
            return response('الملف كبير جدا الحد الاعلى هو 10MB', 413);
        }

        $files = $data['files'];
        foreach ($files as $file) {
            File::create($file, $data["project_id"]);
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

    public function removeFile($id, $filename)
    {
        $file = File::where('path', 'like', 'files/' . $id . '/' . $filename)->first();
        if (is_null($file))
            return response('الملف محذوف او غير موجود', 404);
        $file->delete();
        Storage::delete('files/' . $id . '/' . $filename, $file->title);
        return response('تم حذف الملف بنجاح', 200);
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'files.required' => 'يجب اختيار ملفات',
            'files.mimes' => 'يجب اختيار ملفات بصيغ pdf او word فقط',
        ];
    }
}