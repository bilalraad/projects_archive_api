<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Student;
use App\Models\Teacher;



class StudentsAndTeachersController extends Controller
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
    public function students(Request $request)
    {
        $students = Student::select('*')->where(function ($q) use ($request) {
            if ($request->has('level'))
                $q->where('level', '=', $request->level);
        });
        $maxYear = $students->max('year_number');

        $count = $students->count();
        $students = $students->where('year_number', '=', $maxYear)->get();

        return [
            'count' => $count,
            'results' => $students,
        ];
    }
    public function teachers()
    {
        $teachers = Teacher::select('*');
        $count = $teachers->count();
        $students = $teachers->get();

        return [
            'count' => $count,
            'results' => $students,
        ];
    }
}