<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SkillRequest;
use App\Models\Skill;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $status = $request->query('status');

        $Skill = Skill::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->orderBy('created_at', 'desc')
            ->useFilters()
            ->dynamicPaginate();

        return $this->responseSuccess('Skills display successfully', $Skill);
    }

    public function store(SkillRequest $request)
    {
        $create_skill = Skill::create([
            "name" => $request->name,
        ]);

        return $this->responseCreated('Skill Successfully Created', $create_skill);
    }

    public function update(SkillRequest $request, $id)
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
