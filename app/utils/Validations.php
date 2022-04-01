<?php

namespace App\utils;

class Validations
{
    // property declaration
    const required = 'required';
    const nullable = 'nullable';
    const url = 'url';
    const date = 'date';

    // method declaration
    public static function max($number)
    {
        return 'max:' . $number;
    }

    public static function digits($length)
    {
        return 'digits:' . $length;
    }
}