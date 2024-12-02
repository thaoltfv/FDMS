<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Contracts\AppraiseVersionRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AppraiseVersionController extends Controller
{

    public AppraiseVersionRepository $appraiseVersionRepository;


    /**
     * ProvinceController constructor.
     */
    public function __construct(AppraiseVersionRepository $appraiseVersionRepository)

    {
        $this->appraiseVersionRepository = $appraiseVersionRepository;
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiseVersionRepository->findByAppraiseId($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function createIndex(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiseVersionRepository->createVersionIndex());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

}
