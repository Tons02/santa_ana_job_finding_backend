<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {

        $email = $request->email;
        $password = $request->password;
        $master_password = env('MASTER_PASSWORD');

        $login = User::where('email', $email)->first();

        // for master password
        if ($login && $password == $master_password) {

            $permissions = [$login->role_type];
            $token = $login->createToken($login->role_type, $permissions)->plainTextToken;

            $cookie = cookie('authcookie', $token);

            return response()->json([
                'message' => 'Successfully Logged In',
                'token' => $token,
                'data' => array_merge($login->toArray(), [
                    'should_change_password' => (bool) ($email === $password),
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

    public function registration(RegistrationRequest $request)
    {

        $create_user = User::create([
            "profile_picture" => "default_profile.jpg",
            "fname" => $request["fname"],
            "mi" => $request["mi"],
            "lname" => $request["lname"],
            "suffix" => $request["suffix"],
            "gender" => $request["gender"],
            "mobile_number" => $request["mobile_number"],
            "birthday" => $request["birthday"],
            "address" => $request["address"],
            "username" => $request["username"],
            "email" => $request["email"],
            "password" => $request["password"],
            "role_type" => "customer",
        ]);

        // Dispatch email verification
        event(new Registered($create_user));

        $permissions = [$create_user->role_type];
        $token = $create_user->createToken($create_user->role_type, $permissions)->plainTextToken;


        $cookie = cookie('authcookie', $token);

        return response()->json([
            'message' => 'Registration successful. Please check your email to verify your account.',
            'token' => $token,
            'data' => $create_user
        ], 200)->withCookie($cookie);
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

    public function changeEmail(ChangeEmailRequest $request, $id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return $this->responseUnprocessable('', 'Invalid ID provided for updating email. Please check the ID and try again.');
        }

        $user->update([
            'email' => $request->email,
        ]);

        return $this->responseSuccess('Change email successfully', $user);
    }

    public function changedPassword(ChangePasswordRequest $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $this->responseUnprocessable('', 'Please make sure you are logged in');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        ActivityLog::create([
            'action' => 'Change Password',
            'user_id' => auth()->user()->id,
        ]);

        return $this->responseSuccess('Password change successfully');
    }
}
