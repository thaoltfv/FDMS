<?php

namespace App\Http\Controllers\CustomerGroup;

use App\Contracts\CustomerGroupFourthRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerGroup\CustomerGroupFourthRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CustomerGroupFourthController extends Controller
{

    private CustomerGroupFourthRepository $customerGroupFourthRepository;

    /**
     * CustomerGroupFourthController constructor.
     */
    public function __construct(CustomerGroupFourthRepository $customerGroupFourthRepository)
    {
        $this->customerGroupFourthRepository = $customerGroupFourthRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->customerGroupFourthRepository->findPaging());
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
            return $this->respondWithCustomData($this->customerGroupFourthRepository->findAll());
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
            return $this->respondWithCustomData($this->customerGroupFourthRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param CustomerGroupFourthRequest $request
     * @return JsonResponse
     */
    public function store(CustomerGroupFourthRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->customerGroupFourthRepository->createCGF($request->toArray()));
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
     * @param CustomerGroupFourthRequest $request
     * @return JsonResponse
     */
    public function update($id, CustomerGroupFourthRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->customerGroupFourthRepository->updateCGF($id, $request->toArray()));
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
                return $this->respondWithCustomData($this->customerGroupFourthRepository->deleteCGF($id));
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
}
