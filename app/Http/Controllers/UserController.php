<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;



class UserController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')
            ->only(['destroy', 'create', 'update']);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response('كلمة السر او البريد الالكتروني غير صحيح', 401);
        }
        $user->tokens()->delete();
        $token = $user->createToken($request->input('email'))->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required'
        ]);
        $users = User::where('email', '=', $request->input('email'))->first();
        if ($users !== null) {
            return response('البريد الالكتروني مأخوذ سابقا', 409);
        }
        if (!Gate::allows('create-user')) {
            return response('انت غير مخول لانشاء ادمن جديد', 403);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken($request->input('email'))->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => 'يجب ادخال البريد الالكتروني',
            'email.email' => 'البريد الالكتروني غير صالح',
            'password.required' => 'كلمة السر مطلوبة',
        ];
    }
}