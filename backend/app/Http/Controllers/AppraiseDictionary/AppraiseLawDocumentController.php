<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Contracts\AppraiseDictionaryRepository;
use App\Contracts\AppraiseLawDocumentRepository;
use App\Contracts\UserRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppraiseDictionary\AppraiseLawDocumentRequest;
use App\Http\Requests\Dictionary\DictionaryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AppraiseLawDocumentController extends Controller
{

    private AppraiseLawDocumentRepository $appraiseLawDocumentRepository;


    /**
     * ProvinceController constructor.
     */
    public function __construct(AppraiseLawDocumentRepository $appraiseLawDocumentRepository)
    {
        $this->appraiseLawDocumentRepository = $appraiseLawDocumentRepository;

    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiseLawDocumentRepository->findPaging());
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
            return $this->respondWithCustomData($this->appraiseLawDocumentRepository->findAll());
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
            return $this->respondWithCustomData($this->appraiseLawDocumentRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param AppraiseLawDocumentRequest $request
     * @return JsonResponse
     */
    public function store(AppraiseLawDocumentRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiseLawDocumentRepository->createAppraiseLawDocument($request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param AppraiseLawDocumentRequest $request
     * @return JsonResponse
     */
    public function update($id, AppraiseLawDocumentRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiseLawDocumentRepository->updateAppraiseLawDocument($id, $request->toArray()));
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
            return $this->respondWithCustomData($this->appraiseLawDocumentRepository->deleteAppraiseLawDocument($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
