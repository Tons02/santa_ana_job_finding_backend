<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $status = $request->query('status');
        $pagination = $request->query('pagination');

        $Course = Course::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->orderBy('created_at', 'desc')
            ->useFilters()
            ->dynamicPaginate();

        if (!$pagination) {
            CourseResource::collection($Course);
        } else {
            $Course = CourseResource::collection($Course);
        }

        return $this->responseSuccess('Course display successfully', $Course);
    }

    public function store(CourseRequest $request)
    {
        $create_course = Course::create([
            "education_level" => $request->education_level,
            "name" => $request->name,
        ]);

        return $this->responseCreated('Course Successfully Created', $create_course);
    }

    public function update(CourseRequest $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return $this->responseNotFound('', 'Invalid ID provided for updating. Please check the ID and try again.');
        }

        $course->education_level = $request['education_level'];
        $course->name = $request['name'];

        if (!$course->isDirty()) {
            return $this->responseSuccess('No Changes', $course);
        }

        $course->save();

        return $this->responseSuccess('Course successfully updated', $course);
    }


    public function archived(Request $request, $id)
    {
        $course = Course::withTrashed()->find($id);

        if (!$course) {
            return $this->responseUnprocessable('', 'Invalid id please check the id and try again.');
        }

        if ($course->deleted_at) {

            $course->restore();

            return $this->responseSuccess('Course successfully restored', $course);
        }

        if (!$course->deleted_at) {

            $course->delete();
            return $this->responseSuccess('Course successfully archived', $course);
        }
    }
}
