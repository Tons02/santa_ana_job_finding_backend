<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $status = $request->query('status');
        $pagination = $request->query('pagination');

        $User = User::with('skills')->when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->orderBy('created_at', 'desc')
            ->useFilters()
            ->dynamicPaginate();

        if (!$pagination) {
            UserResource::collection($User);
        } else {
            $User = UserResource::collection($User);
        }
        return $this->responseSuccess('User display successfully', $User);
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'first_name'      => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'  => $request->last_name,
            'suffix'  => $request->suffix,
            'date_of_birth'  => $request->date_of_birth,
            'gender'  => $request->gender,
            'landline'  => $request->landline,
            'mobile_number' => $request->mobile_number,
            'civil_status'  => $request->civil_status,
            'height'  => $request->height,
            'religion'  => $request->religion,
            'full_address'  => $request->full_address,
            'province'  => $request->province,
            'lgu'  => $request->lgu,
            'barangay'  => $request->barangay,
            'email'  => $request->email,
            'password'  => $request->email,
            'role_type'  => 'admin',
        ]);

        return $this->responseCreated(
            'User Successfully Created',
            $user
        );
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->responseNotFound('', 'Invalid ID provided for updating. Please check the ID and try again.');
        }

        $user->update([
            'first_name'      => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name'  => $request->last_name,
            'suffix'  => $request->suffix,
            'date_of_birth'  => $request->date_of_birth,
            'gender'  => $request->gender,
            'landline'  => $request->landline,
            'mobile_number' => $request->mobile_number,
            'civil_status'  => $request->civil_status,
            'height'  => $request->height,
            'religion'  => $request->religion,
            'full_address'  => $request->full_address,
            'province'  => $request->province,
            'lgu'  => $request->lgu,
            'barangay'  => $request->barangay,
            'email'  => $request->email,
            // 'role_type'  => $request->role_type,
        ]);

        return $this->responseCreated(
            'User Successfully Updated',
            $user
        );
    }

    public function archived(Request $request, $id)
    {
        $user = User::withTrashed()->find($id);

        if (!$user) {
            return $this->responseUnprocessable('', 'Invalid id please check the id and try again.');
        }

        if ($user->deleted_at) {

            $user->restore();

            return $this->responseSuccess('User successfully restore', $user);
        }

        if (!$user->deleted_at) {

            $user->delete();

            return $this->responseSuccess('User successfully archive', $user);
        }
    }

    public function user_registration(UserRegistrationRequest $request)
    {
        DB::beginTransaction();

        try {
            $user_registration = User::create([
                'first_name'      => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name'  => $request->last_name,
                'suffix'  => $request->suffix,
                'date_of_birth'  => $request->date_of_birth,
                'gender'  => $request->gender,
                'landline'  => $request->landline,
                'mobile_number' => $request->mobile_number,
                'civil_status'  => $request->civil_status,
                'height'  => $request->height,
                'religion'  => $request->religion,
                'resume'  => $request->file('resume')->store('applicant_resume', 'private'),
                'full_address'  => $request->full_address,
                'province'  => $request->province,
                'lgu'  => $request->lgu,
                'barangay'  => $request->barangay,
                'employment_status'  => $request->employment_status,
                'employment_type'  => $request->employment_type,
                'months_looking'  => $request->months_looking,
                'is_ofw'  => $request->is_ofw,
                'is_former_ofw'  => $request->is_former_ofw,
                'last_deployment'  => $request->last_deployment,
                'return_date'  => $request->return_date,
                'username'  => $request->username,
                'email'  => $request->email,
                'password'  => $request->password,
                'role_type'  => 'user',
            ]);

            // Generate token for auto-login
            $permissions = [$user_registration->role_type];
            $token = $user_registration->createToken($user_registration->role_type, $permissions)->plainTextToken;

            // Create auth cookie
            $cookie = cookie('authcookie', $token);

            DB::commit();

            return response()->json([
                'message' => 'User Registered Successfully',
                'token' => $token,
                'data' => $user_registration
            ], 201)->withCookie($cookie);
        } catch (\Exception $e) {
            DB::rollBack();

            // Optionally delete the uploaded resume file if it was stored
            if (isset($user_registration) && $user_registration->resume) {
                Storage::disk('private')->delete($user_registration->resume);
            }

            return response()->json([
                'message' => 'User registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ This displays in browser
    public function viewResume(User $user)
    {
        $path = Storage::disk('private')->path($user->resume);

        if (!file_exists($path)) {
            abort(404, 'Resume not found');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($user->resume) . '"'
        ]);
    }
}
