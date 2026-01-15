<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserGetDisplayRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserSingleGetDisplayRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserUpdateResumeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use ApiResponse;

    public function index(UserGetDisplayRequest $request)
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

    public function show(UserSingleGetDisplayRequest $request, User $user)
    {
        $user = User::with('skills')->where('id', $user->id)->first();

        return $this->responseSuccess('User retrieved successfully', new UserResource($user));
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
            'username'  => $request->username,
            'email'  => $request->email,
            'password'  => $request->username,
            'role_type'  => 'admin',
        ]);

        return $this->responseCreated(
            'Admin Successfully Created',
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
            'username'  => $request->username,
            'email'  => $request->email,
        ]);

        return $this->responseCreated(
            'Admin Successfully Updated',
            $user
        );
    }

    public function archived(Request $request, $id)
    {
        $user = User::withTrashed()->find($id);

        if ($id == auth('sanctum')->user()->id) {
            return $this->responseUnprocessable('', 'Unable to archive. You cannot archive your own account.');
        }

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

            // Sync skills if provided
            if ($request->has('skills') && is_array($request->skills)) {
                $user_registration->skills()->sync($request->skills);
            }

            // Generate token for auto-login
            $permissions = [$user_registration->role_type];
            $token = $user_registration->createToken($user_registration->role_type, $permissions)->plainTextToken;

            // Create auth cookie
            $cookie = cookie('authcookie', $token);

            DB::commit();

            // Load skills relationship for response
            $user_registration->load('skills');

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

    public function update_user(UserUpdateRequest $request, User $user)
    {
        DB::beginTransaction();

        try {
            $updateData = $request->only([
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'date_of_birth',
                'gender',
                'landline',
                'mobile_number',
                'civil_status',
                'height',
                'religion',
                'full_address',
                'province',
                'lgu',
                'barangay',
                'employment_status',
                'employment_type',
                'months_looking',
                'is_ofw',
                'is_former_ofw',
                'last_deployment',
                'return_date',
            ]);

            // Handle resume upload if provided
            if ($request->hasFile('resume')) {
                if ($user->resume) {
                    Storage::disk('private')->delete($user->resume);
                }
                $updateData['resume'] = $request->file('resume')->store('applicant_resume', 'private');
            }

            // Update user basic information
            $user->update($updateData);

            // Sync skills if provided
            if ($request->has('skills') && is_array($request->skills)) {
                $user->skills()->sync($request->skills);
            }

            DB::commit();

            // Load skills relationship for response
            $user->load('skills');

            return response()->json([
                'message' => 'User information updated successfully',
                'data' => $user->fresh(['skills'])
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($updateData['resume'])) {
                Storage::disk('private')->delete($updateData['resume']);
            }

            return response()->json([
                'message' => 'User update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update_resume(UserUpdateResumeRequest $request, User $user)
    {

        if ($user->resume) {
            Storage::disk('private')->delete($user->resume);
        }

        // Store new resume
        $path = $request->file('resume')->store('applicant_resume', 'private');

        // Update user record
        $user->update(['resume' => $path]);

        return $this->responseSuccess('Resume updated successfully', $user);
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
