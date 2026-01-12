<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {

        $loginInput = $request->username_email;
        $password = $request->password;
        $master_password = env('MASTER_PASSWORD');

        $login = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        // for master password
        if ($login && $password == $master_password) {

            $permissions = [$login->role_type];
            $token = $login->createToken($login->role_type, $permissions)->plainTextToken;

            $cookie = cookie('authcookie', $token);

            return response()->json([
                'message' => 'Successfully Logged In',
                'token' => $token,
                'data' => array_merge($login->toArray(), [
                    'should_change_password' => (bool) (false),
                ]),
            ], 200)->withCookie($cookie);
        }


        if (!$login || !hash::check($password, $login->password)) {
            return $this->responseBadRequest('', 'Invalid Credentials');
        }

        $permissions = [$login->role_type];
        $token = $login->createToken($login->role_type, $permissions)->plainTextToken;

        $cookie = cookie('authcookie', $token);

        return response()->json([
            'message' => 'Successfully Logged In',
            'token' => $token,
            'data' => $login
        ], 200)->withCookie($cookie);
    }

    public function Logout(Request $request)
    {
        $cookie = Cookie::forget('authcookie');
        auth('sanctum')->user()->currentAccessToken()->delete();
        return $this->responseSuccess('Logout successfully');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return $this->responseUnprocessable('', 'Invalid ID provided for updating password. Please check the ID and try again.');
        }

        $user->update([
            'password' => $user->username,
        ]);

        return $this->responseSuccess('The Password has been reset');
    }

    public function changedPassword(ChangePasswordRequest $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $this->responseUnprocessable('', 'Please make sure you are logged in');
        }

        $user->update([
            'password' => $request->new_password,
        ]);

        return $this->responseSuccess('Password change successfully');
    }
}
