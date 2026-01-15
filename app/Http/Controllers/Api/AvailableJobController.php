<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvailableJobRequest;
use App\Http\Resources\AvailableJobResource;
use App\Models\AvailableJob;
use App\Models\JobApplication;
use Carbon\Carbon;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;

class AvailableJobController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $status = $request->query('status');
        $pagination = $request->query('pagination');

        $Job = AvailableJob::with('skills')->when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->orderBy('created_at', 'desc')
            ->useFilters()
            ->dynamicPaginate();

        if (!$pagination) {
            AvailableJobResource::collection($Job);
        } else {
            $Job = AvailableJobResource::collection($Job);
        }

        return $this->responseSuccess('Jobs display successfully', $Job);
    }

    public function show($id)
    {
        $job = AvailableJob::with('skills')->find($id);

        if (!$job) {
            return $this->responseNotFound('', 'Invalid ID provided. Please check the ID and try again.');
        }

        return $this->responseSuccess('Job display successfully', new AvailableJobResource($job));
    }

    public function store(AvailableJobRequest $request)
    {

        $now = Carbon::now();
        $posted  = Carbon::createFromFormat('Y-m-d H:i:s', $request->posted_at);
        $expires = Carbon::createFromFormat('Y-m-d H:i:s', $request->expires_at);

        $status = ($now->between($posted, $expires))
            ? 'active'
            : 'closed';

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
            "hiring_status" => $status,
            "posted_at" => $request->posted_at,
            "expires_at" => $request->expires_at,
        ]);

        // Use sync() for skills
        if ($request->filled('skills')) {
            $create_job->skills()->sync($request->skills);
        }

        return $this->responseCreated('Job Successfully Created', $create_job);
    }

    public function update(AvailableJobRequest $request, $id)
    {
        $job = AvailableJob::find($id);

        if (!$job) {
            return $this->responseNotFound('', 'Invalid ID provided for updating. Please check the ID and try again.');
        }

        $now = Carbon::now();
        $posted  = Carbon::createFromFormat('Y-m-d H:i:s', $request->posted_at);
        $expires = Carbon::createFromFormat('Y-m-d H:i:s', $request->expires_at);

        $hiring_status = ($now->between($posted, $expires))
            ? 'active'
            : 'closed';

        $job->fill([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'is_remote' => $request->is_remote,
            'employment_type' => $request->employment_type,
            'experience_level' => $request->experience_level,
            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,
            'salary_currency' => $request->salary_currency,
            'salary_period' => $request->salary_period,
            'hiring_status' => $hiring_status,
            'posted_at' => $request->posted_at,
            'expires_at' => $request->expires_at,
        ]);

        if (!$job->isDirty() && !$request->has('skills')) {
            return $this->responseSuccess('No Changes', $job);
        }

        $job->save();

        // 🔥 Sync skills (attach + detach automatically)
        if ($request->has('skills')) {
            $job->skills()->sync($request->skills);
        }

        return $this->responseSuccess('Job successfully updated', $job);
    }



    public function archived(Request $request, $id)
    {
        $job = AvailableJob::withTrashed()->find($id);

        if (!$job) {
            return $this->responseUnprocessable('', 'Invalid id please check the id and try again.');
        }

        if ($job->id === JobApplication::where('job_id', $job->id)->pluck('job_id')->first()) {
            return $this->responseUnprocessable('', 'Job cannot be archived because there are existing job applications associated with it.');
        }

        if ($job->deleted_at) {

            $job->restore();

            return $this->responseSuccess('Job successfully restore', $job);
        }

        if (!$job->deleted_at) {

            $job->delete();

            return $this->responseSuccess('Job successfully archive', $job);
        }
    }
}
