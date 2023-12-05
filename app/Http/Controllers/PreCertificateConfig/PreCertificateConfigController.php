<?php

namespace App\Http\Controllers\PreCertificateConfig;

use App\Contracts\PreCertificateConfigRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreCertificateConfig\PreCertificateConfigRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PreCertificateConfigController extends Controller
{

    private PreCertificateConfigRepository $preCertificateRepository;


    /**
     * @param PreCertificateConfigRepository $preCertificateRepository
     */
    public function __construct(PreCertificateConfigRepository $preCertificateRepository)
    {
        $this->preCertificateRepository = $preCertificateRepository;

    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->findPaging());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @return JsonResponse
     */
    public function findAll(Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->findAll($request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }


    /**
     * @param $name
     * @return JsonResponse
     */
    public function show($name): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->findByName($name));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param PreCertificateConfigRequest $request
     * @return JsonResponse
     */
    public function store(PreCertificateConfigRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->createPreCertificateConfig($request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

}
