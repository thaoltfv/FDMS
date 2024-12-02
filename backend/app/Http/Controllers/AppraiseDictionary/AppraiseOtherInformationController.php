<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Contracts\AppraiseOtherInformationRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppraiseDictionary\AppraiseOtherInformationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AppraiseOtherInformationController extends Controller
{

    private AppraiseOtherInformationRepository $appraiseOtherInformationRepository;

    /**
     * ProvinceController constructor.
     */
    public function __construct(AppraiseOtherInformationRepository $appraiseOtherInformationRepository)
    {
        $this->appraiseOtherInformationRepository = $appraiseOtherInformationRepository;

    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiseOtherInformationRepository->findPaging());
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
            return $this->respondWithCustomData($this->appraiseOtherInformationRepository->findAll());
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
            return $this->respondWithCustomData($this->appraiseOtherInformationRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param AppraiseOtherInformationRequest $request
     * @return JsonResponse
     */
    public function store(AppraiseOtherInformationRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiseOtherInformationRepository->createAppraiseOtherInformation($request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param AppraiseOtherInformationRequest $request
     * @return JsonResponse
     */
    public function update($id, AppraiseOtherInformationRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiseOtherInformationRepository->updateAppraiseOtherInformation($id, $request->toArray()));
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
            return $this->respondWithCustomData($this->appraiseOtherInformationRepository->deleteAppraiseOtherInformation($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
