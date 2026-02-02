<?php

namespace App\Http\Controllers\Api;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserGetDisplayRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserSingleGetDisplayRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserUpdateResumeRequest;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\User;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use ApiResponse;

    public function index(UserGetDisplayRequest $request)
    {
        $status = $request->query('status');
        $pagination = $request->query('pagination');

        $User = User::with('skills', 'courses', 'preferred_positions')->when($status === "inactive", function ($query) {
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


    /**
     * Export users to Excel with the same filters as index method
     */
    public function export(UserGetDisplayRequest $request)
    {
        // Merge the request with pagination=false to get all data
        $request->merge(['pagination' => false]);

        $status = $request->query('status');
        $filters = $request->all();

        $fileName = 'users_export_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new UserExport($status, $filters),
            $fileName
        );
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
            'region'  => $request->region,
            'province'  => $request->province,
            'city_municipality'  => $request->city_municipality,
            'barangay'  => $request->barangay,
            'street_address'  => $request->street_address,
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
            // Handle resume file upload and renaming
            $resumePath = null;
            if ($request->hasFile('resume')) {
                $file = $request->file('resume');
                $extension = $file->getClientOriginalExtension();

                // Create filename: LastName_FirstName_YYYYMMDD.ext
                $date = date('dmY'); // Current date in DMYYYY format
                $code = Str::random(4);
                $lastName = str_replace(' ', '_', $request->last_name);
                $firstName = str_replace(' ', '_', $request->first_name);
                $filename = "{$lastName}_{$firstName}_{$date}_{$code}.{$extension}";

                // Store the file with the new name
                $resumePath = $file->storeAs('applicant_resume', $filename, 'private');
            }

            $user_registration = User::create([
                'first_name'      => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name'  => $request->last_name,
                'suffix'  => $request->suffix,
                'date_of_birth'  => $request->date_of_birth,
                'gender'  => $request->gender,
                'civil_status'  => $request->civil_status,
                'region'  => $request->region,
                'province'  => $request->province,
                'city_municipality'  => $request->city_municipality,
                'barangay'  => $request->barangay,
                'street_address'  => $request->street_address,
                'telephone'  => $request->telephone,
                'mobile_number'  => $request->mobile_number,
                'height'  => $request->height,
                'religion'  => $request->religion,
                'resume'  => $resumePath, // Store the path instead of request value
                'employment_status'  => $request->employment_status,
                'employment_type'  => $request->employment_type,
                'months_looking'  => $request->months_looking,
                'is_4ps'  => $request->is_4ps,
                'is_pwd'  => $request->is_pwd,
                'disability'  => $request->disability,
                'is_ofw'  => $request->is_ofw,
                'work_experience'  => $request->work_experience,
                'is_former_ofw'  => $request->is_former_ofw,
                'country'  => $request->country,
                'last_deployment'  => $request->last_deployment,
                'return_date'  => $request->return_date,
                'transaction_date'  => $request->transaction_date,
                'program_service'  => $request->program_service,
                'event'  => $request->event,
                'transaction'  => $request->transaction,
                'remarks'  => $request->remarks,
                'email'  => $request->email,
                'username'  => $request->username,
                'password'  => $request->password,
                'role_type'  => 'user',
            ]);

            if ($request->has('skills') && is_array($request->skills)) {
                $user_registration->skills()->sync($request->skills);
            }

            if ($request->has('courses') && is_array($request->courses)) {
                $courseIds = [];

                foreach ($request->courses as $courseData) {
                    $course = Course::firstOrCreate(
                        ['name' => $courseData['name']],
                        [
                            'education_level' => $courseData['education_level'],
                        ]
                    );

                    $courseIds[] = $course->id;
                }

                $user_registration->courses()->sync($courseIds);
            }

            if ($request->has('preferred_positions') && is_array($request->preferred_positions)) {
                $user_registration->preferred_positions()->sync($request->preferred_positions);
            }

            $permissions = [$user_registration->role_type];
            $token = $user_registration->createToken($user_registration->role_type, $permissions)->plainTextToken;
            $cookie = cookie('authcookie', $token);

            DB::commit();

            $user_registration->load(['skills', 'courses', 'preferred_positions']);

            return response()->json([
                'message' => 'User Registered Successfully',
                'token' => $token,
                'data' => $user_registration
            ], 201)->withCookie($cookie);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($resumePath) && $resumePath) {
                Storage::disk('private')->delete($resumePath);
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
            $updateData = [
                'first_name'      => $request->first_name,
                'middle_name'     => $request->middle_name,
                'last_name'       => $request->last_name,
                'suffix'          => $request->suffix,
                'date_of_birth'   => $request->date_of_birth,
                'gender'          => $request->gender,
                'civil_status'    => $request->civil_status,
                'region'          => $request->region,
                'province'        => $request->province,
                'city_municipality' => $request->city_municipality,
                'barangay'        => $request->barangay,
                'street_address'  => $request->street_address,
                'telephone'       => $request->telephone,
                'mobile_number'   => $request->mobile_number,
                'height'          => $request->height,
                'religion'        => $request->religion,
                'employment_status' => $request->employment_status,
                'employment_type' => $request->employment_type,
                'months_looking'  => $request->months_looking,
                'is_4ps'          => $request->is_4ps,
                'is_pwd'          => $request->is_pwd,
                'disability'      => $request->disability,
                'is_ofw'          => $request->is_ofw,
                'work_experience' => $request->work_experience,
                'is_former_ofw'   => $request->is_former_ofw,
                'country'         => $request->country,
                'last_deployment' => $request->last_deployment,
                'return_date'     => $request->return_date,
                'transanction_date'     => $request->transanction_date,
                'program_service'     => $request->program_service,
                'event'     => $request->event,
                'transaction' => $request->transaction,
                'remarks'         => $request->remarks,
            ];

            // Update user basic information
            $user->update($updateData);

            // Sync skills if provided
            if ($request->has('skills') && is_array($request->skills)) {
                $user->skills()->sync($request->skills);
            }

            // Sync courses if provided
            if ($request->has('courses') && is_array($request->courses)) {
                $courseIds = [];

                foreach ($request->courses as $courseData) {
                    $course = Course::firstOrCreate(
                        ['name' => $courseData['name']],
                        [
                            'education_level' => $courseData['education_level'],
                        ]
                    );

                    $courseIds[] = $course->id;
                }

                $user->courses()->sync($courseIds);
            }

            // Sync preferred positions if provided
            if ($request->has('preferred_positions') && is_array($request->preferred_positions)) {
                $user->preferred_positions()->sync($request->preferred_positions);
            }

            DB::commit();

            return response()->json([
                'message' => 'User information updated successfully',
                'data' => new UserResource(
                    $user->load(['skills', 'courses', 'preferred_positions'])
                ),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'User update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update_resume(UserUpdateResumeRequest $request, User $user)
    {
        // Delete old resume if exists
        if ($user->resume) {
            Storage::disk('private')->delete($user->resume);
        }

        // Handle new resume file upload with naming convention
        $file = $request->file('resume');
        $extension = $file->getClientOriginalExtension();

        // Create filename: LastName_FirstName_DMYYYY_CODE.ext
        $date = date('dmY');
        $code = Str::random(4);
        $lastName = str_replace(' ', '_', $user->last_name);
        $firstName = str_replace(' ', '_', $user->first_name);
        $filename = "{$lastName}_{$firstName}_{$date}_{$code}.{$extension}";

        // Store the file with the new name
        $path = $file->storeAs('applicant_resume', $filename, 'private');

        // Update user record
        $user->update(['resume' => $path]);

        $user->load(['skills', 'courses', 'preferred_positions']);

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
