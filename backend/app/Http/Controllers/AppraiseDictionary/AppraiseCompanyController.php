<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Contracts\AppraiserCompanyRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppraiseDictionary\AppraiserCompanyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AppraiseCompanyController extends Controller
{

    private AppraiserCompanyRepository $appraiserCompanyRepository;


    /**
     * ProvinceController constructor.
     */
    public function __construct(AppraiserCompanyRepository $appraiserCompanyRepository)
    {
        $this->appraiserCompanyRepository = $appraiserCompanyRepository;

    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiserCompanyRepository->findPaging());
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
            return $this->respondWithCustomData($this->appraiserCompanyRepository->findAll());
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
            return $this->respondWithCustomData($this->appraiserCompanyRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param AppraiserCompanyRequest $request
     * @return JsonResponse
     */
    public function store(AppraiserCompanyRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiserCompanyRepository->createAppraiserCompany($request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param AppraiserCompanyRequest $request
     * @return JsonResponse
     */
    public function update($id, AppraiserCompanyRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiserCompanyRepository->updateAppraiserCompany($id, $request->toArray()));
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
            return $this->respondWithCustomData($this->appraiserCompanyRepository->deleteAppraiserCompany($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
