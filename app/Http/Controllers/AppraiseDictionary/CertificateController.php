<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Contracts\AppraiseAssetRepository;
use App\Contracts\AppraiserCompanyRepository;
use App\Contracts\AppraiseRepository;
use App\Contracts\BuildingPriceRepository;
use App\Contracts\CertificateRepository;
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

class CertificateController extends Controller
{

    private CertificateRepository $certificateRepository;
    public CompareAssetGeneralRepository $compareAssetGeneralRepository;
    public UserRepository $userRepository;
    public DictionaryRepository $dictionaryRepository;
    public BuildingPriceRepository $buildingPriceRepository;
    private AppraiseAssetRepository $appraiseAssetRepository;
    private AppraiserCompanyRepository $appraiserCompanyRepository;

    /**
     * ProvinceController constructor.
     */
    public function __construct(CertificateRepository         $certificateRepository,
                                CompareAssetGeneralRepository $compareAssetGeneralRepository,
                                UserRepository                $userRepository,
                                DictionaryRepository          $dictionaryRepository,
                                BuildingPriceRepository       $buildingPriceRepository,
                                AppraiseAssetRepository       $appraiseAssetRepository,
                                AppraiserCompanyRepository    $appraiserCompanyRepository)
    {
        $this->certificateRepository = $certificateRepository;
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
        $this->userRepository = $userRepository;
        $this->dictionaryRepository = $dictionaryRepository;
        $this->buildingPriceRepository = $buildingPriceRepository;
        $this->appraiseAssetRepository = $appraiseAssetRepository;
        $this->appraiserCompanyRepository = $appraiserCompanyRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->certificateRepository->findPaging());
        } catch (\Exception $exception) {
            dd($exception);
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
            return $this->respondWithCustomData($this->certificateRepository->findAll());
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
            return $this->respondWithCustomData($this->certificateRepository->updateStatus($id, $request));
        } catch (\Exception $exception) {
            dd($exception);
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
            return $this->respondWithCustomData($this->certificateRepository->otherDocumentUpload($id, $request));
        } catch (\Exception $exception) {
            dd($exception);
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
            return $this->respondWithCustomData($this->certificateRepository->otherDocumentRemove($id, $request));
        } catch (\Exception $exception) {
            dd($exception);
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
            $item = $this->certificateRepository->otherDocumentDownload($id, $request);
            if(isset($item->link)) {
                return response()->streamDownload(function () use ($item) {
                    echo file_get_contents($item->link);
                }, $item->name);
            }
        } catch (\Exception $exception) {
            dd($exception);
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
            if(isset($test)) {
                $result = $this->certificateRepository->findByIdTest($id);
            } else {
                $result = $this->certificateRepository->findById($id);
            }

            CommonService::getCertificateAssetPriceTotal($result);
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $result = $this->certificateRepository->createCertificate($request->toArray());
            if(is_numeric($result)) {
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

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $result = $this->certificateRepository->updateCertificate($id, $request->toArray());
            if(is_numeric($result)) {
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
            return $this->respondWithCustomData($this->certificateRepository->updateTangibleComparisonFactor($id, $request->toArray()));
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
            return $this->respondWithCustomData($this->certificateRepository->deleteCertificate($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function appraiseAsset(Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData((new AppraiseAsset(
                $this->appraiseAssetRepository,
                $this->dictionaryRepository
            ))
                ->AppraiseAsset($request->toArray()));

        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function print($id): JsonResponse
    {
        try {
            $format = 'docx';
            $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            $appraise = $this->certificateRepository->findById($id);
            $assets = [];
            if ($appraise->assetGeneral) {
                $ids = [];
                foreach ($appraise->assetGeneral as $assetGeneral) {
                    $ids[] = $assetGeneral->id;
                }
                $assets = $this->compareAssetGeneralRepository->findByIds(json_encode($ids));
            }
            return $this->respondWithCustomData((new PhuLuc1())->generateDocx($appraise, $company, $assets, $format));
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
    public function saleDocumentUpload($id, Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->certificateRepository->saleDocumentUpload($id, $request));
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
