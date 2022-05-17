<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Graduate;
use App\Models\Teacher;



class GraduatesAndTeachersController extends Controller
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


    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function graduates(Request $request)
    // {
    //     $graduates = Graduate::select('*')->where(function ($q) use ($request) {
    //         if ($request->has('level'))
    //             $q->where('level', '=', $request->level);
    //     });

    //     $count = $graduates->count();
    //     $graduates = $graduates->get();

    //     return [
    //         'count' => $count,
    //         'results' => $graduates,
    //     ];
    // }
    public function teachers()
    {
        $teachers = Teacher::select('*');
        $count = $teachers->count();
        $teachers = $teachers->get();

        return [
            'count' => $count,
            'results' => $teachers,
        ];
    }
}