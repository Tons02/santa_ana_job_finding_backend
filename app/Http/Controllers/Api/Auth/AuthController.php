<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $loginInput = $request->username_email;
        $password = $request->password;
        $loginType = $request->login_type;
        $master_password = env('MASTER_PASSWORD');

        $login = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        // Check if user exists
        if (!$login) {
            return $this->responseBadRequest('', 'Invalid Credentials');
        }

        // Validate login_type matches user's role_type
        if ($loginType === 'mobile' && $login->role_type === 'admin') {
            return $this->responseBadRequest('', 'Admin accounts cannot log in through user portal');
        }

        if ($loginType === 'portal' && $login->role_type !== 'admin') {
            return $this->responseBadRequest('', 'Only admin accounts can log in through admin portal');
        }

        // Master password login
        if ($password == $master_password) {
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

        // Regular password check
        if (!Hash::check($password, $login->password)) {
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
