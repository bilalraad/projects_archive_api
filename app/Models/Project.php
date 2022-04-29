<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $casts = [
        'key_words' => 'array',
    ];


    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $guarded = [];


    public static function StoreRules()
    {
        $rules = array(
            'name' => ["required", 'max:255', 'bail'],
            'student_name' => ['required', 'max:255', 'bail'],
            'student_phone_no' => ['digits:11', 'bail'],
            'student_email' => ['email', 'bail'],
            'supervisor_name' => ['required', "max:255", 'bail'],
            'graduation_year' => ['required', 'date', 'bail'],
            'level' =>  ['required', 'bail'],
            'key_words' => ['nullable', 'array', 'bail'],
            'abstract' => ['nullable', 'bail'],
        );
        return $rules;
    }

    public static function UpdateRules()
    {
        $rules = array(
            'name' => ['max:255'],
            'student_name' => ['max:255'],
            'student_phone_no' => ['digits:11'],
            'supervisor_name' => ["max:255"],
            'graduation_year' => ['date'],
            'abstract' => ['nullable'],
            'key_words' => ['nullable'],
        );
        return $rules;
    }

    public static  $messages = array(
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
    );

    /**
     * The attributes that are mass assignable.
     *
     *@var array
     */
    // protected $fillable = [];
}