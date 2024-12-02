<?php

namespace App\Http\Controllers\CustomerGroup;

use App\Contracts\CustomerGroupFirstRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerGroup\CustomerGroupFirstRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CustomerGroupFirstController extends Controller
{

    private CustomerGroupFirstRepository $customerGroupFirstRepository;

    /**
     * CustomerGroupFirstController constructor.
     */
    public function __construct(CustomerGroupFirstRepository $customerGroupFirstRepository)
    {
        $this->customerGroupFirstRepository = $customerGroupFirstRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->customerGroupFirstRepository->findPaging());
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
            return $this->respondWithCustomData($this->customerGroupFirstRepository->findAll());
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
            return $this->respondWithCustomData($this->customerGroupFirstRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param CustomerGroupFirstRequest $request
     * @return JsonResponse
     */
    public function store(CustomerGroupFirstRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                $first = $this->customerGroupFirstRepository->findByName($request->name);
                if ($first) {
                    $data = ['message' => ErrorMessage::DUPLICATE_PROVINCE];
                    return $this->respondWithErrorData($data);
                }
                return $this->respondWithCustomData($this->customerGroupFirstRepository->createCGF($request->toArray()));
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
     * @param CustomerGroupFirstRequest $request
     * @return JsonResponse
     */
    public function update($id, CustomerGroupFirstRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                $first = $this->customerGroupFirstRepository->findByName($request->name);
                if ($first && $first->id != $id) {
                    $data = ['message' => ErrorMessage::DUPLICATE_PROVINCE];
                    return $this->respondWithErrorData($data);
                }
                return $this->respondWithCustomData($this->customerGroupFirstRepository->updateCGF($id, $request->toArray()));
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
                return $this->respondWithCustomData($this->customerGroupFirstRepository->deleteCGF($id));
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

    public function getAllFirstGroup()
    {
        try {
            return $this->respondWithCustomData($this->customerGroupFirstRepository->getAllFirstGroup());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
