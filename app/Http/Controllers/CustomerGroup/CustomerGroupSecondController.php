<?php

namespace App\Http\Controllers\CustomerGroup;

use App\Contracts\CustomerGroupSecondRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerGroup\CustomerGroupSecondRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CustomerGroupSecondController extends Controller
{

    private CustomerGroupSecondRepository $customerGroupSecondRepository;

    /**
     * CustomerGroupSecondController constructor.
     */
    public function __construct(CustomerGroupSecondRepository $customerGroupSecondRepository)
    {
        $this->customerGroupSecondRepository = $customerGroupSecondRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->customerGroupSecondRepository->findPaging());
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
            return $this->respondWithCustomData($this->customerGroupSecondRepository->findAll());
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
            return $this->respondWithCustomData($this->customerGroupSecondRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param CustomerGroupSecondRequest $request
     * @return JsonResponse
     */
    public function store(CustomerGroupSecondRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                $second = $this->customerGroupSecondRepository->findByName($request->name, $request->province_id);
                if ($second) {
                    $data = ['message' => ErrorMessage::DUPLICATE_DISTRICT];
                    return $this->respondWithErrorData($data);
                }
                return $this->respondWithCustomData($this->customerGroupSecondRepository->createCGS($request->toArray()));
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
     * @param CustomerGroupSecondRequest $request
     * @return JsonResponse
     */
    public function update($id, CustomerGroupSecondRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                $second = $this->customerGroupSecondRepository->findByName($request->name, $request->first_id);
                if ($second && $second->id != $id) {
                    $data = ['message' => ErrorMessage::DUPLICATE_DISTRICT];
                    return $this->respondWithErrorData($data);
                }
                return $this->respondWithCustomData($this->customerGroupSecondRepository->updateCGS($id, $request->toArray()));
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
                return $this->respondWithCustomData($this->customerGroupSecondRepository->deleteCGS($id));
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

    public function findAllByFirstGroup()
    {
        try {
            return $this->respondWithCustomData($this->customerGroupSecondRepository->findAllByFirstGroup());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
