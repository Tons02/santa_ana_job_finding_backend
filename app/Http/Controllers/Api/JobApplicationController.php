<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $status = $request->query('status');
        $pagination = $request->query('pagination');

        $JobApplications = JobApplication::with('job', 'user')
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->orderBy('created_at', 'desc')
            ->useFilters()
            ->dynamicPaginate();

        if (!$pagination) {
            JobApplicationResource::collection($JobApplications);
        } else {
            $JobApplications = JobApplicationResource::collection($JobApplications);
        }

        return $this->responseSuccess('Job Applications display successfully', $JobApplications);
    }

    public function store(JobApplicationRequest $request)
    {
        if (!auth()->user()->resume) {
            return $this->responseNotFound('', 'Please upload your resume before applying for jobs.');
        }

        $create_job_application = JobApplication::create([
            "job_id" => $request->job_id,
            "cover_letter" => $request->cover_letter,
            "user_id" => auth()->id(),
            "applied_at" => now(),
        ]);

        return $this->responseCreated('Job Application Successfully Created', $create_job_application);
    }

    public function job_application_view(Request $request, $id)
    {
        $job_application = JobApplication::with('job', 'user')->find($id);

        if (!auth()->user()->role_type == 'admin') {
            return $this->responseNotFound('', 'You are not authorized to view this job application.');
        }

        if (!$job_application) {
            return $this->responseNotFound('', 'Invalid ID provided. Please check the ID and try again.');
        }

        if ($job_application->status === 'submitted') {
            $job_application->status = 'viewed';
            $job_application->save();

            return $this->responseSuccess('Job Application viewed successfully', new JobApplicationResource($job_application));
        }

        // just show the data dont update if already viewed
        return $this->responseSuccess('Job Application viewed successfully', new JobApplicationResource($job_application));
    }

    public function archived(Request $request, $id)
    {
        $job_application = JobApplication::withTrashed()->find($id);

        if (!$job_application) {
            return $this->responseUnprocessable('', 'Invalid id please check the id and try again.');
        }

        if (auth()->user()->id != $job_application->user_id) {
            return $this->responseNotFound('', 'You are not authorized to archived or restore this application.');
        }

        if ($job_application->deleted_at) {

            $job_application->restore();

            return $this->responseSuccess('Job Application successfully restored', $job_application);
        }

        if (!$job_application->deleted_at) {

            $job_application->delete();

            return $this->responseSuccess('Job Application successfully archived', $job_application);
        }
    }
}
