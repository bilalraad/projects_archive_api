<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithHeadings;




class ProjectsExports implements FromQuery, WithHeadings
{

    use Exportable;



    public function __construct(Request $request)
    {
        $this->graduation_year = $request->graduation_year;
        $this->name = $request->name;
        $this->student_name = $request->student_name;
        $this->student_phone_no = $request->student_phone_no;
        $this->level = $request->level;
        $this->abstract = $request->abstract;
        $this->supervisor_name = $request->supervisor_name;
    }

    public function headings(): array
    {
        return ['#', "Title", "Student Name", "Student Phone", "Supervisor Name", "Graduation Year", "Abstract", "Key Words", "Level"];
    }


    public function query()
    {
        return Project::select('*')->where(function ($q) {
            if (!is_null($this->graduation_year))
                $q->whereYear('graduation_year', $this->graduation_year);
            if (!is_null($this->name))
                $q->where('name', 'like',  "%$this->name%");
            if (!is_null($this->student_name))
                $q->where('student_name', 'like',  "%$this->student_name%");
            if (!is_null($this->student_phone_no))
                $q->where('student_phone_no', 'like', "%$this->student_phone_no%");
            if (!is_null($this->level))
                $q->where('level', '=', $this->level);
            if (!is_null($this->abstract))
                $q->where('abstract', 'like', "%$this->abstract%");
            if (!is_null($this->supervisor_name))
                $q->where('supervisor_name', 'like',  "%$this->supervisor_name%");
        });
    }
}