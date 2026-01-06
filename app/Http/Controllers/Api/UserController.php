<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $status = $request->query('status');
        $pagination = $request->query('pagination');

        $User = User::when($status === "inactive", function ($query) {
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
            'password'  => $request->password,
            'role_type'  => $request->role_type,
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
            'role_type'  => $request->role_type,
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
}
