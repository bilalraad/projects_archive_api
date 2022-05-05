<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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


    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);


        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response("تم ارسال رابط اعادة كلمة السر الى بريدك الالكتروني")
            : response(['email' => __($status)], 500);
    }

    public  function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response('تم تغيير كلمة السر بنجاح')
            : $this->getPasswordResetErrorResponse($status);
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    private function getPasswordResetErrorResponse($status)
    {
        if ($status === Password::INVALID_TOKEN)
            return response('الرابط غير صالح', 401);

        if ($status === Password::INVALID_USER)
            return response('البريد الالكتروني خطأ', 401);

        return response('حدث خطأ غير معروف ', 500);
    }
}