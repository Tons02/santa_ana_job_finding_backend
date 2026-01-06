<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvailableJobRequest;
use App\Models\AvailableJob;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;

class AvailableJobController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $status = $request->query('status');

        $Job = AvailableJob::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->orderBy('created_at', 'desc')
            ->useFilters()
            ->dynamicPaginate();

        return $this->responseSuccess('Jobs display successfully', $Job);
    }

    public function store(AvailableJobRequest $request)
    {
        $create_job = AvailableJob::create([
            "title" => $request->title,
            "description" => $request->description,
            "location" => $request->location,
            "is_remote" => $request->is_remote,
            "employment_type" => $request->employment_type,
            "experience_level" => $request->experience_level,
            "salary_min" => $request->salary_min,
            "salary_max" => $request->salary_max,
            "salary_currency" => $request->salary_currency,
            "salary_period" => $request->salary_period,
            "status" => $request->status,
            "hiring_status" => $request->hiring_status,
            "posted_at" => $request->posted_at,
            "expires_at" => $request->expires_at,
        ]);

        if ($request->filled('skills')) {
            $create_job->skills()->sync($request->skills);
        }

        return $this->responseCreated('Skill Successfully Created', $create_job);
    }

    public function update(AvailableJobRequest $request, $id)
    {
        $skill = Skill::find($id);

        if (!$skill) {
            return $this->responseNotFound('', 'Invalid ID provided for updating. Please check the ID and try again.');
        }

        $skill->name = $request['name'];

        if (!$skill->isDirty()) {
            return $this->responseSuccess('No Changes', $skill);
        }
        $skill->save();

        return $this->responseSuccess('Skill successfully updated', $skill);
    }


    public function archived(Request $request, $id)
    {
        $skill = Skill::withTrashed()->find($id);

        if (!$skill) {
            return $this->responseUnprocessable('', 'Invalid id please check the id and try again.');
        }

        if ($skill->deleted_at) {

            $skill->restore();

            return $this->responseSuccess('Skill successfully restore', $skill);
        }

        if (!$skill->deleted_at) {

            $skill->delete();

            return $this->responseSuccess('Skill successfully archive', $skill);
        }
    }
}
