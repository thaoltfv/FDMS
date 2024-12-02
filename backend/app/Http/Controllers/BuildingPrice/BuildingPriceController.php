<?php

namespace App\Http\Controllers\BuildingPrice;

use App\Contracts\BuildingPriceRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuildingPrice\BuildingPriceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BuildingPriceController extends Controller
{

    private BuildingPriceRepository $buildingPriceRepository;

    /**
     * ProvinceController constructor.
     */
    public function __construct(BuildingPriceRepository $buildingPriceRepository)
    {
        $this->buildingPriceRepository = $buildingPriceRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->buildingPriceRepository->findPaging());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @return JsonResponse
     */
    public function findAll(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->buildingPriceRepository->findAll());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }


    /**
     * @param $type
     * @return JsonResponse
     */
    public function show($type): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->buildingPriceRepository->findById($type));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param BuildingPriceRequest $request
     * @return JsonResponse
     */
    public function store(BuildingPriceRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->buildingPriceRepository->createBuildingPrice($request->toArray()));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param BuildingPriceRequest $request
     * @return JsonResponse
     */
    public function update($id, BuildingPriceRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->buildingPriceRepository->updateBuildingPrice($id, $request->toArray()));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::DELETE_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->buildingPriceRepository->deleteBuildingPrice($id));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getAverageBuildPrice(): JsonResponse
    {
        try {
            //if ($this->getUserPermission(PermissionsDefault::VIEW_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->buildingPriceRepository->getAverageBuildPrice());
            /* } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            } */
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getAverageBuildPriceNew(): JsonResponse
    {
        try {
            //if ($this->getUserPermission(PermissionsDefault::VIEW_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->buildingPriceRepository->getAverageBuildPriceNew());
            /* } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            } */
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
