<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreferredPositionRequest;
use App\Http\Resources\PreferredPositionResource;
use App\Models\PreferredPosition;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;

class PreferredPositionController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $status = $request->query('status');
        $pagination = $request->query('pagination');

        $PreferredPosition = PreferredPosition::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->orderBy('created_at', 'desc')
            ->useFilters()
            ->dynamicPaginate();

        if (!$pagination) {
            PreferredPositionResource::collection($PreferredPosition);
        } else {
            $PreferredPosition = PreferredPositionResource::collection($PreferredPosition);
        }

        return $this->responseSuccess('Preferred Positions display successfully', $PreferredPosition);
    }

    public function store(PreferredPositionRequest $request)
    {
        $create_preferred_position = PreferredPosition::create([
            "name" => $request->name,
        ]);

        return $this->responseCreated('Preferred Position Successfully Created', $create_preferred_position);
    }

    public function update(PreferredPositionRequest $request, $id)
    {
        $preferred_position = PreferredPosition::find($id);

        if (!$preferred_position) {
            return $this->responseNotFound('', 'Invalid ID provided for updating. Please check the ID and try again.');
        }

        $preferred_position->name = $request['name'];

        if (!$preferred_position->isDirty()) {
            return $this->responseSuccess('No Changes', $preferred_position);
        }

        $preferred_position->save();

        return $this->responseSuccess('Preferred Position successfully updated', $preferred_position);
    }


    public function archived(Request $request, $id)
    {
        $preferred_position = PreferredPosition::withTrashed()->find($id);

        if (!$preferred_position) {
            return $this->responseUnprocessable('', 'Invalid id please check the id and try again.');
        }

        if ($preferred_position->deleted_at) {

            $preferred_position->restore();

            return $this->responseSuccess('Preferred Position successfully restore', $preferred_position);
        }

        if (!$preferred_position->deleted_at) {

            $preferred_position->delete();
            return $this->responseSuccess('Preferred Position successfully archive', $preferred_position);
        }
    }
}
