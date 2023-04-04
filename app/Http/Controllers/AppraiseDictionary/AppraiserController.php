<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Contracts\AppraiserRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppraiseDictionary\AppraiserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AppraiserController extends Controller
{

    private AppraiserRepository $appraiserRepository;


    /**
     * ProvinceController constructor.
     */
    public function __construct(AppraiserRepository $appraiserRepository)
    {
        $this->appraiserRepository = $appraiserRepository;

    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiserRepository->findPaging());
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
            return $this->respondWithCustomData($this->appraiserRepository->findAll());
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
            return $this->respondWithCustomData($this->appraiserRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param AppraiserRequest $request
     * @return JsonResponse
     */
    public function store(AppraiserRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiserRepository->createAppraiser($request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param AppraiserRequest $request
     * @return JsonResponse
     */
    public function update($id, AppraiserRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiserRepository->updateAppraiser($id, $request->toArray()));
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
                return $this->respondWithCustomData($this->appraiserRepository->deleteAppraiser($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
