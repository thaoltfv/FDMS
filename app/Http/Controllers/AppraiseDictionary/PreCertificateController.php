<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Contracts\AppraiseAssetRepository;
use App\Contracts\AppraiserCompanyRepository;
use App\Contracts\AppraiseRepository;
use App\Contracts\BuildingPriceRepository;
use App\Contracts\PreCertificateRepository;
use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\DictionaryRepository;
use App\Contracts\UserRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appraise\CreateAppraiseRequest;
use App\Http\Requests\Appraise\UpdateAppraiseRequest;
use App\Services\AppraiseAsset\AppraiseAsset;
use App\Services\Document\PhuLuc1;
use App\Services\Document\AssetReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Storage;
use App\Services\CommonService;


class PreCertificateController extends Controller
{
    private PreCertificateRepository $preCertificateRepository;
    private UserRepository $userRepository;
    private DictionaryRepository $dictionaryRepository;
    private AppraiseAssetRepository $appraiseAssetRepository;

    /**
     * PreCertificateController constructor.
     */
    public function __construct(
        PreCertificateRepository $preCertificateRepository,
        UserRepository $userRepository,
        DictionaryRepository $dictionaryRepository,
        AppraiseAssetRepository $appraiseAssetRepository,
    ) {
        $this->preCertificateRepository = $preCertificateRepository;
        $this->userRepository = $userRepository;
        $this->dictionaryRepository = $dictionaryRepository;
        $this->appraiseAssetRepository = $appraiseAssetRepository;
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
    public function findAll(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->findAll());
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
    public function updateStatus($id, Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->updateStatus($id, $request));
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
    public function otherDocumentUpload($id, Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->otherDocumentUpload($id, $request));
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
    public function otherDocumentRemove($id, Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->otherDocumentRemove($id, $request));
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
    public function otherDocumentDownload($id, Request $request)
    {
        try {
            $item = $this->preCertificateRepository->otherDocumentDownload($id, $request);
            if (isset($item->link)) {
                return response()->streamDownload(function () use ($item) {
                    echo file_get_contents($item->link);
                }, $item->name);
            }
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
            $test = request()->get('test');
            if (isset($test)) {
                $result = $this->preCertificateRepository->findByIdTest($id);
            } else {
                $result = $this->preCertificateRepository->findById($id);
            }

            // CommonService::getPreCertificateAssetPriceTotal($result);
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    // /**
    //  * @param Request $request
    //  * @return JsonResponse
    //  */
    // public function store(Request $request): JsonResponse
    // {
    //     try {
    //         $result = $this->preCertificateRepository->create($request->toArray());
    //         if (is_numeric($result)) {
    //             return $this->respondWithCustomData($result);
    //         } else {
    //             $data = ['message' => $result, 'exception' => []];
    //             return $this->respondWithErrorData($data);
    //         }
    //     } catch (\Exception $exception) {
    //         Log::error($exception);
    //         $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
    //         return $this->respondWithErrorData($data);
    //     }
    // }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $result = $this->preCertificateRepository->update($id, $request->toArray());
            if (is_numeric($result)) {
                return $this->respondWithCustomData($result);
            } else {
                $data = ['message' => $result, 'exception' => []];
                return $this->respondWithErrorData($data);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function updateTangibleComparisonFactor($id, Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->updateTangibleComparisonFactor($id, $request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    // /**
    //  * @param $id
    //  * @return JsonResponse
    //  */
    // public function destroy($id): JsonResponse
    // {
    //     try {
    //         return $this->respondWithCustomData($this->preCertificateRepository->delete($id));
    //     } catch (\Exception $exception) {
    //         Log::error($exception);
    //         $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
    //         return $this->respondWithErrorData($data);
    //     }
    // }

    public function appraiseAsset(Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData((new AppraiseAsset(
                $this->appraiseAssetRepository,
                $this->dictionaryRepository
            ))
                ->appraiseAsset($request->toArray()));

        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

   
}
