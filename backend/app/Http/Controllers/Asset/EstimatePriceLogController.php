<?php

namespace App\Http\Controllers\Asset;

use App\Contracts\EstimatePriceLogRepository;
use App\Contracts\UserRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\CompareAssetGeneralRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

use Tymon\JWTAuth\Facades\JWTAuth;

class EstimatePriceLogController extends Controller
{

    private EstimatePriceLogRepository $estimatePriceLogRepository;
    private UserRepository $userRepository;


    /**
     * ProvinceController constructor.
     */
    public function __construct(EstimatePriceLogRepository $estimatePriceLogRepository,
                                UserRepository             $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->estimatePriceLogRepository = $estimatePriceLogRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->estimatePriceLogRepository->findPaging());
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
            return $this->respondWithCustomData($this->estimatePriceLogRepository->findAll());
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
            return $this->respondWithCustomData($this->estimatePriceLogRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function getLog($id): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->estimatePriceLogRepository->getLog($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        try {
            $request = request()->toArray();
            return $this->respondWithCustomData($this->estimatePriceLogRepository->createEstimatePriceLog($request));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::CREATE_ASSET_ERROR, 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function create(): JsonResponse
    {
        try {
            $request = request()->toArray();
            $jwt = JWTAuth::getToken();
            $user = $this->userRepository->validateToken($jwt);
            $estimateLog = [
                'input' => json_encode($request),
                'output' => json_encode($request),
                'status' => null,
                'type' => 'CREATE',
                'user' => $user->name ?? null,
            ];
            return $this->respondWithCustomData($this->estimatePriceLogRepository->createLog($estimateLog));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::CREATE_ASSET_ERROR, 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param CompareAssetGeneralRequest $request
     * @return JsonResponse
     */
    public function update($id, CompareAssetGeneralRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->estimatePriceLogRepository->updateEstimatePriceLog($id, $request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::CREATE_ASSET_ERROR, 'exception' => $exception];
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
                return $this->respondWithCustomData($this->estimatePriceLogRepository->deleteEstimatePriceLog($id));
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

    public function createIndex(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->estimatePriceLogRepository->createIndex());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

}
