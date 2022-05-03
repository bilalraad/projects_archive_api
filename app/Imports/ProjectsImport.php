<?php

namespace App\Imports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ProjectsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Project([
            "name" => $row['title'],
            "student_name" => $row["student_name"],
            "student_phone_no" => $row['student_phone'],
            "supervisor_name" => $row['supervisor_name'],
            "graduation_year" => $row["graduation_year"],
            "abstract" => $row['abstract'],
            "key_words" => [],
            "level" => $row["level"]
        ]);
    }
}