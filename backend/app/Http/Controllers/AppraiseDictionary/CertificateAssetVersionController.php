<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Contracts\CompareAssetVersionRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;



class CertificateAssetVersionController extends Controller
{

    public CompareAssetVersionRepository $compareAssetVersionRepository;


    /**
     * ProvinceController constructor.
     */
    public function __construct(CompareAssetVersionRepository $compareAssetVersionRepository)

    {
        $this->compareAssetVersionRepository = $compareAssetVersionRepository;
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
                return $this->respondWithCustomData($this->compareAssetVersionRepository->findByGeneralId($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

}
