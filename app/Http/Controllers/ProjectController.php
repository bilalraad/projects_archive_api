<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use App\Exports\ProjectsExports;
use App\Imports\ProjectsImport;
use App\Models\File;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')
            ->only(['destroy', 'store', 'update', 'import']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $projects = Project::with("files")->select('*')->where(function ($q) use ($request) {
            if ($request->has('graduation_year'))
                $q->whereYear('graduation_year', $request->graduation_year);
            if ($request->has('name'))
                $q->where('name', 'like',  "%$request->name%");
            if ($request->has('student_name'))
                $q->where('student_name', 'like',  "%$request->student_name%");
            if ($request->has('student_phone_no'))
                $q->where('student_phone_no', 'like', "%$request->student_phone_no%");
            if ($request->has('level'))
                $q->where('level', '=', $request->level);
            if ($request->has('abstract'))
                $q->where('abstract', 'like', "%$request->abstract%");
            if ($request->has('supervisor_name'))
                $q->where('supervisor_name', 'like',  "%$request->supervisor_name%");
        });
        $count = $projects->count();
        $withPaging = $projects->offset($request->skip)
            ->limit($request->take)
            ->get();

        return [
            'count' => $count,
            'results' => $withPaging,
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate(
            Project::StoreRules(),
            Project::$messages
        );

        $project = tap(new Project($data))->save();

        return response($project, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $p = Project::where('id', $id)->with("files")->first();
        if (is_null($p))
            return response('المشروع محذوف او غير موجود', 404);
        return $p;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, project $project)
    {

        $data = $request->validate(
            Project::UpdateRules(),
            Project::$messages
        );
        $project->update($data);
        $project->save();

        return $project;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Project::destroy($id);
        // Get all files in a directory
        $files =   Storage::allFiles('files/' . $id);

        // Delete Files
        Storage::delete($files);
        return response('تم حذف المشروع بنجاح', 200);
    }

    public function export(Request $request)
    {
        return (new ProjectsExports($request))->download('projects.xlsx');
    }

    public function import(Request $request)
    {
        $data = $request->validate([
            'file' => ["required"],
            'file.*' => 'mimes:xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
        Excel::import(new ProjectsImport, $data['file']);

        return response('تم رفع المشاريع بنجاح');
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name.max' => 'يجب ان يكون الاسم اقصر من ٢٥٥ حرف',
            'student_name.required' => 'سجب ادخال اسم الطالب',
            'student_name.max' => 'يجب اني يكون اسم الطالب اقصر من ٢٥٥ حرف',
            'student_phone_no.digits' => 'يجب ان يكون رقم الهاتف مكون من ١١ رقم',
            'student_email.email' => 'البريد الالكتروني الخاص بالطالب غير صالح',
            'supervisor_name.required' => 'يجب ادخال اسم المشرف',
            'supervisor_name.max' => 'يجب ان يكون اسم المشرف اقل من ٢٥٥ حرف',
            'graduation_year.required' => 'سنة التخرج مطلوبة',
            'level.required' => 'يجب اختيار المستوى الجامعي',
        ];
    }
}