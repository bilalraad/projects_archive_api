<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Validation\Rule;

use App\Models\Project;

// use app\utils\Validations;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $projects = Project::with("files")->select('*')->where(function ($q) use ($request) {
            if ($request->has('year'))
                $q->whereYear('graduation_year', $request->year);
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
        })
            ->offset($request->skip)
            ->limit($request->take)
            ->get();
        return [
            'count' => count($projects),
            'results' => $projects,
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
        $data = $request->validate([
            'name' => ["required", 'max:255', 'bail'],
            'student_name' => ['required', 'max:255', 'bail'],
            'student_phone_no' => ['digits:11', 'bail'],
            'student_email' => ['email', 'bail'],
            'supervisor_name' => ['required', "max:255", 'bail'],
            'graduation_year' => ['required', 'date', 'bail'],
            'level' =>  ['required', 'bail'],
            'key_words' => ['nullable', 'array', 'bail'],
            'abstract' => ['nullable', 'bail'],
        ]);

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
        return Project::findOrFail($id)->with("files")->first();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        $data = $request->validate([
            'name' => ['max:255'],
            'student_name' => ['max:255'],
            'student_phone_no' => ['digits:11'],
            'supervisor_name' => ["max:255"],
            'graduation_year' => ['date'],
            'abstract' => ['nullable'],
            'key_words' => ['nullable'],

        ]);
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
        return response('تم حذف المشروع بنجاح', 200);
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
