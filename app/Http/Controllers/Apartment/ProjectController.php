<?php

namespace App\Http\Controllers\Apartment;

use App\Contracts\ProjectRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS;

class ProjectController extends Controller
{
    private ProjectRepository $project;
    public function __construct(ProjectRepository $project)
    {
        $this->project = $project;
    }

    public function index()
    {
        try {
            return $this->respondWithCustomData($this->project->findPaging());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
    public function getAll()
    {
        try {
            return $this->respondWithCustomData($this->project->getAll());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
    public function createProject(Request $request)
    {
        $rules = [
            'name' => 'string|required|max:255',
            'province_id' => 'integer|required',
            'district_id' => 'integer|nullable',
            'ward_id' => 'integer|nullable',
            'street_id' => 'integer|nullable',
            'total_blocks' => 'integer|required',
            'total_apartments' => 'integer|required',
            'coordinates' => 'string|required',
            'block' => 'array|nullable',
            'block.*.name' => 'string|nullable|required_with:block',
            'block.*.total_floors' => 'integer|nullable|required_with:block',
            // 'block.*.first_floor' => 'integer|nullable|required_with:block',
            // 'block.*.last_floor' => 'integer|nullable|required_with:block',
            'block.*.apartments_per_floor' => 'integer|nullable|required_with:block',
            'block.*.floor' => 'array|nullable',
            'block.*.floor.*.name' => 'string|required_with:block.*.floor',
            'block.*.floor.*.apartment' => 'array|nullable',
            'block.*.floor.*.apartment.*.name' => 'string|required_with:block.*.floor.*.apartment',
        ];

        $customAttributes = [
            'name' => 'Tên chung cư',
            'province_id' => 'Tỉnh thành',
            'district_id' => 'Quận/Huyện',
            'ward_id' => 'Phường/Xã',
            'street_id' => 'Đường',
            'total_blocks' => 'Tổng số block',
            'total_apartments' => 'Tổng số căn hộ',
            'coordinates' => 'Location',
            'block' => 'Block',
            'block.*.name' => 'Tên block',
            'block.*.total_floors' => 'Tổng số tầng/block',
            'block.*.first_floor' => 'Tầng dầu tiên',
            'block.*.last_floor' => 'Tâng cuối cùng',
            'block.*.apartments_per_floor' => 'Tổng số căn hộ/tầng',
            'block.*.floor' => 'Tầng',
            'block.*.floor.*.name' => 'Tên tâng',
            'block.*.floor.*.apartment' => 'Căn hộ',
            'block.*.floor.*.apartment.*.name' => 'Tên căn hộ',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->project->createProject($request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function getProjectById(int $id)
    {
        try {
            $result =  $this->project->getProjectById($id);
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function updateProject(int $id, Request $request)
    {
        $rules = [
            'name' => 'string|required|max:255',
            'province_id' => 'integer|required',
            'district_id' => 'integer|nullable',
            'ward_id' => 'integer|nullable',
            'street_id' => 'integer|nullable',
            'total_blocks' => 'integer|required',
            'total_apartments' => 'integer|required',
            'coordinates' => 'string|required',
            'block' => 'array|nullable',
            'block.*.name' => 'string|nullable|required_with:block',
            'block.*.total_floors' => 'integer|nullable|required_with:block',
            // 'block.*.first_floor' => 'integer|nullable|required_with:block',
            // 'block.*.last_floor' => 'integer|nullable|required_with:block',
            'block.*.apartments_per_floor' => 'integer|nullable|required_with:block',
            'block.*.floor' => 'array|nullable',
            'block.*.floor.*.name' => 'string|required_with:block.*.floor',
            'block.*.floor.*.apartment' => 'array|nullable',
            'block.*.floor.*.apartment.*.name' => 'string|required_with:block.*.floor.*.apartment',
        ];

        $customAttributes = [
            'name' => 'Tên chung cư',
            'province_id' => 'Tỉnh thành',
            'district_id' => 'Quận/Huyện',
            'ward_id' => 'Phường/Xã',
            'street_id' => 'Đường',
            'total_blocks' => 'Tổng số block',
            'total_apartments' => 'Tổng số căn hộ',
            'coordinates' => 'Location',
            'block' => 'Block',
            'block.*.name' => 'Tên block',
            'block.*.total_floors' => 'Tổng số tầng/block',
            'block.*.first_floor' => 'Tầng dầu tiên',
            'block.*.last_floor' => 'Tâng cuối cùng',
            'block.*.apartments_per_floor' => 'Tổng số căn hộ/tầng',
            'block.*.floor' => 'Tầng',
            'block.*.floor.*.name' => 'Tên tâng',
            'block.*.floor.*.apartment' => 'Căn hộ',
            'block.*.floor.*.apartment.*.name' => 'Tên căn hộ',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->project->updateProject($id, $request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }
    public function updateOrCreateBlock(int $projectId, Request $request)
    {
        $rules = [
            'name' => 'string|required|max:255',
            'total_floors' => 'integer|required',
            'first_floor' => 'integer|required',
            'last_floor' => 'integer|required',
            'apartments_per_floor' => 'integer|required',
        ];

        $customAttributes = [
            'name' => 'Tên block',
            'total_floors' => 'Tổng số tầng/block',
            'first_floor' => 'Tầng dầu tiên',
            'last_floor' => 'Tâng cuối cùng',
            'apartments_per_floor' => 'Tổng số căn hộ/tầng',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->project->updateOrCreateBlock($projectId, $request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }
    public function updateOrCreateFloor(int $blockId, Request $request)
    {
        $rules = [
            'name' => 'string|required|max:255',
        ];

        $customAttributes = [
            'name' => 'Tên tầng',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->project->updateOrCreateFloor($blockId, $request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }
    public function updateOrCreateApartment(int $floorId, Request $request)
    {
        $rules = [
            'name' => 'string|required|max:255',
        ];
        $customAttributes = [
            'name' => 'Tên căn hộ',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->project->updateOrCreateApartment($floorId, $request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }
    public function updateStatusProject(int $id, bool $status)
    {
        $result = $this->project->updateStatus($id, $status,'project');
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function updateStatusBlock(int $id, bool $status)
    {
        $result = $this->project->updateStatus($id, $status,'block');
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function updateStatuFloor(int $id, bool $status)
    {
        $result = $this->project->updateStatus($id, $status,'floor');
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function updateStatuApartment(int $id, bool $status)
    {
        $result = $this->project->updateStatus($id, $status,'apartment');
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function getProjectActiveById(int $id)
    {
        try {
            $result =  $this->project->getProjectActiveById($id);
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
    public function getAllActive()
    {
        try {
            $result =  $this->project->getAllActive();
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function getApartmentByFloorId(int $floorId)
    {
        try {
            $result =  $this->project->getApartmentByFloorId($floorId);
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
    public function getProjectByDistrictId ()
    {
        try {
            $result =  $this->project->getProjectByDistrictId();
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
}
