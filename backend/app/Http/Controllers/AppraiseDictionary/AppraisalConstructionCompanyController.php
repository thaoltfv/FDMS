<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Contracts\AppraisalConstructionCompanyRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppraiseDictionary\AppraisalConstructionCompanyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AppraisalConstructionCompanyController extends Controller
{

    private AppraisalConstructionCompanyRepository $appraisalConstructionCompanyRepository;


    /**
     * ProvinceController constructor.
     */
    public function __construct(AppraisalConstructionCompanyRepository  $appraisalConstructionCompanyRepository)
    {
        $this->appraisalConstructionCompanyRepository = $appraisalConstructionCompanyRepository;

    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraisalConstructionCompanyRepository->findPaging());
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
            return $this->respondWithCustomData($this->appraisalConstructionCompanyRepository->findAll());
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
            return $this->respondWithCustomData($this->appraisalConstructionCompanyRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param AppraisalConstructionCompanyRequest $request
     * @return JsonResponse
     */
    public function store(AppraisalConstructionCompanyRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraisalConstructionCompanyRepository->createAppraiserConstructionCompany($request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param AppraisalConstructionCompanyRequest $request
     * @return JsonResponse
     */
    public function update($id, AppraisalConstructionCompanyRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraisalConstructionCompanyRepository->updateAppraiserConstructionCompany($id, $request->toArray()));
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
            return $this->respondWithCustomData($this->appraisalConstructionCompanyRepository->deleteAppraiserConstructionCompany($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
