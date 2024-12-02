<?php

namespace App\Http\Controllers\Address;

use App\Contracts\ProvinceRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\ProvinceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProvinceController extends Controller
{

    private ProvinceRepository $provinceRepository;

    /**
     * ProvinceController constructor.
     */
    public function __construct(ProvinceRepository $provinceRepository)
    {
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->provinceRepository->findPaging());
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
            return $this->respondWithCustomData($this->provinceRepository->findAll());
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
    public function show($id): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->provinceRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param ProvinceRequest $request
     * @return JsonResponse
     */
    public function store(ProvinceRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                $province = $this->provinceRepository->findByName($request->name);
                if ($province) {
                    $data = ['message' => ErrorMessage::DUPLICATE_PROVINCE];
                    return $this->respondWithErrorData($data);
                }
                return $this->respondWithCustomData($this->provinceRepository->createProvince($request->toArray()));
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
     * @param ProvinceRequest $request
     * @return JsonResponse
     */
    public function update($id, ProvinceRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                $province = $this->provinceRepository->findByName($request->name);
                if ($province && $province->id != $id) {
                    $data = ['message' => ErrorMessage::DUPLICATE_PROVINCE];
                    return $this->respondWithErrorData($data);
                }
                return $this->respondWithCustomData($this->provinceRepository->updateProvince($id, $request->toArray()));
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
                return $this->respondWithCustomData($this->provinceRepository->deleteProvince($id));
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

    public function getAllProvince()
    {
        try {
            return $this->respondWithCustomData($this->provinceRepository->getAllProvince());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
