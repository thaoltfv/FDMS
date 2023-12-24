<?php

namespace App\Http\Controllers\PreCertificateConfig;

use App\Contracts\PreCertificateConfigRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PreCertificateConfigController extends Controller
{

    private PreCertificateConfigRepository $preCertificateConfigRepository;


    /**
     * @param PreCertificateConfigRepository $preCertificateConfigRepository
     */
    public function __construct(PreCertificateConfigRepository $preCertificateConfigRepository)
    {
        $this->preCertificateConfigRepository = $preCertificateConfigRepository;

    }

    /**
     * @return JsonResponse
     */
    public function findAll(Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateConfigRepository->findAll($request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

}
