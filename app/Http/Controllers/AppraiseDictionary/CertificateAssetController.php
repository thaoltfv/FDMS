<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Http\Controllers\Controller;

use App\Contracts\AppraiseAssetRepository;
use App\Contracts\CertificateAssetRepository;
use App\Contracts\AppraiserCompanyRepository;
use App\Contracts\BuildingPriceRepository;
use App\Contracts\CertificateRepository;
use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\DictionaryRepository;
use App\Contracts\PreCertificateRepository;
use App\Contracts\UserRepository;

use App\Services\AppraiseAsset\AppraiseAsset;
use App\Services\Document\CertificateAsset\PhuLuc2;
use App\Services\Document\BaoCaoTest;

use App\Http\Requests\Appraise\CreateAppraiseRequest;
use App\Http\Requests\Appraise\UpdateAppraiseRequest;
use App\Enum\ErrorMessage;
use App\Models\Certificate;
use App\Models\Appraiser;
use App\Models\CertificateRealEstate;
use App\Models\DocumentDictionary;
use App\Models\RealEstate;
use App\Notifications\ActivityLog;
use App\Services\Document\CertificateAsset\PhuLuc1;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use File;
use ZipArchive;
use Ramsey\Uuid\Uuid;
use Storage;

class CertificateAssetController extends Controller
{
    use ActivityLog;
    private CertificateAssetRepository $certificateAssetRepository;
    private CertificateRepository $certificateRepository;
    public CompareAssetGeneralRepository $compareAssetGeneralRepository;
    public UserRepository $userRepository;
    public DictionaryRepository $dictionaryRepository;
    public BuildingPriceRepository $buildingPriceRepository;
    private AppraiseAssetRepository $appraiseAssetRepository;
    private AppraiserCompanyRepository $appraiserCompanyRepository;
    private PreCertificateRepository $preCertificateRepository;
    private string $envDocument = '';
    /**
     * ProvinceController constructor.
     */
    public function __construct(
        CertificateAssetRepository    $certificateAssetRepository,
        CertificateRepository         $certificateRepository,
        CompareAssetGeneralRepository $compareAssetGeneralRepository,
        UserRepository                $userRepository,
        DictionaryRepository          $dictionaryRepository,
        BuildingPriceRepository       $buildingPriceRepository,
        AppraiseAssetRepository       $appraiseAssetRepository,
        AppraiserCompanyRepository    $appraiserCompanyRepository,
        PreCertificateRepository     $preCertificateRepository
    ) {
        $this->certificateAssetRepository = $certificateAssetRepository;
        $this->certificateRepository = $certificateRepository;
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
        $this->userRepository = $userRepository;
        $this->dictionaryRepository = $dictionaryRepository;
        $this->buildingPriceRepository = $buildingPriceRepository;
        $this->appraiseAssetRepository = $appraiseAssetRepository;
        $this->appraiserCompanyRepository = $appraiserCompanyRepository;
        $this->preCertificateRepository = $preCertificateRepository;
        $this->envDocument = config('services.document_service.document_module');
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->certificateAssetRepository->findPaging());
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
            return $this->respondWithCustomData($this->certificateAssetRepository->findAll());
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
            return $this->respondWithCustomData($this->certificateAssetRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $ids
     * @return JsonResponse
     */
    public function findByIds($ids): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->certificateAssetRepository->findByIds($ids));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param CreateAppraiseRequest $request
     * @return JsonResponse
     */
    public function store(CreateAppraiseRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->certificateAssetRepository->createAppraise($request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param UpdateAppraiseRequest $request
     * @return JsonResponse
     */
    public function update($id, UpdateAppraiseRequest $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->certificateAssetRepository->updateAppraise($id, $request->toArray()));
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
            return $this->respondWithCustomData($this->certificateAssetRepository->deleteAppraise($id));
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

    public function printPL1($id): JsonResponse
    {
        try {
            // $certificate = $this->certificateRepository->getCertificateAppraiseReportData($id);
            // $documentType = $certificate->document_type;
            // if (!empty($documentType) && (in_array('DCN', $documentType) || in_array('DT', $documentType)) ) {
            //     $format = 'docx';
            //     $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            //     $certificate = $this->certificateRepository->findById($id);
            //     $appraises = [];
            //     // if ($certificate->appraises) {
            //     //     $ids = [];
            //     //     foreach ($certificate->appraises as $appraise) {
            //     //         $ids[] = $appraise->id;
            //     //     }
            //     //     $appraises = $this->certificateAssetRepository->findByIds(json_encode($ids));
            //     // }
            //     if (!empty($certificate->realEstate)) {
            //         $ids = [];
            //         foreach ($certificate->realEstate as $realEstate) {
            //             if (!empty($realEstate->appraises)) {
            //                 $ids[] = $realEstate->appraises->id; // real_estate_id  = appraise_id
            //             }
            //         }
            //         // dd(json_encode($ids));
            //         $appraises = $this->certificateAssetRepository->findByIds(json_encode($ids));
            //     }
            //     $result = $this->respondWithCustomData((new PhuLuc1())->generateDocx($certificate, $company, $appraises, $format));

            // } else {
            $certificate = $this->certificateRepository->getCertificateAppraiseReportData($id);
            $service = 'App\\Services\\Document\\Appendix1\\ReportAppendix1' . $this->envDocument;
            $format = '.docx';
            $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            $report = new $service;
            $documentConfig = DocumentDictionary::query()->get();
            $result = $this->respondWithCustomData($report->generateDocx($company, $certificate, $format, $documentConfig));
            // }
            // activity-log download bảng điều chỉnh QSDĐ
            $this->CreateActivityLog($certificate, $certificate, 'download', 'tải xuống bảng điều chỉnh QSDĐ');
            return $result;
        } catch (\Exception $exception) {
            // dd($exception);
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function printPL2($id): JsonResponse
    {
        try {
            // $format = 'docx';
            // $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            // $certificate = $this->certificateRepository->findById($id);
            // $appraises = [];
            // // if ($certificate->appraises) {
            // //     $ids = [];
            // //     foreach ($certificate->appraises as $appraise) {
            // //         $ids[] = $appraise->id;
            // //     }
            // //     $appraises = $this->certificateAssetRepository->findByIds(json_encode($ids));
            // // }
            // if (!empty($certificate->realEstate)) {
            //     $ids = [];
            //     foreach ($certificate->realEstate as $realEstate) {
            //         if (!empty($realEstate->appraises)) {
            //             $ids[] = $realEstate->appraises->id; // real_estate_id  = appraise_id
            //         }
            //     }
            //     // dd(json_encode($ids));
            //     $appraises = $this->certificateAssetRepository->findByIds(json_encode($ids));
            // }
            // $result = $this->respondWithCustomData((new PhuLuc2())->generateDocx($certificate, $company, $appraises, $format, $this->buildingPriceRepository));
            // $edited = $this->certificateRepository->findById($id)->first();
            // activity-log download bảng điều chỉnh CTXD

            $certificate = $this->certificateRepository->getCertificateAppraiseReportData($id);
            $service = 'App\\Services\\Document\\Appendix2\\ReportAppendix2' . $this->envDocument;
            $format = '.docx';
            $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            $report = new $service;
            $documentConfig = DocumentDictionary::query()->get();
            $result = $this->respondWithCustomData($report->generateDocx($company, $certificate, $format, $documentConfig));

            $this->CreateActivityLog($certificate, $certificate, 'download', 'tải xuống bảng điều chỉnh CTXD');
            return $result;
        } catch (\Exception $exception) {
            // dd($exception);
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function printPhuLucHinhAnh($id): JsonResponse
    {
        try {
            // $certificate = $this->certificateRepository->getCertificateAppraiseReportData($id);
            // $documentType = $certificate->document_type;
            // if (!empty($documentType) && (in_array('DCN', $documentType) || in_array('DT', $documentType)) ) {
            //     $format = 'docx';
            //     $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            //     $certificate = $this->certificateRepository->findById($id);
            //     $appraises = [];
            //     // if ($certificate->appraises) {
            //     //     $ids = [];
            //     //     foreach ($certificate->appraises as $appraise) {
            //     //         $ids[] = $appraise->id;
            //     //     }
            //     //     $appraises = $this->certificateAssetRepository->findByIds(json_encode($ids));
            //     // }
            //     if (!empty($certificate->realEstate)) {
            //         $ids = [];
            //         foreach ($certificate->realEstate as $realEstate) {
            //             if (!empty($realEstate->appraises)) {
            //                 $ids[] = $realEstate->appraises->id; // real_estate_id  = appraise_id
            //             }
            //         }
            //         // dd(json_encode($ids));
            //         $appraises = $this->certificateAssetRepository->findByIds(json_encode($ids));
            //     }
            //     $result = $this->respondWithCustomData((new PhuLucHinhAnh())->generateDocx($certificate, $company, $appraises, $format));
            // } else {
            $service = 'App\Services\Document\Appendix3\ReportAppendix3' . $this->envDocument;
            $certificate = $this->certificateRepository->getCertificateAppraiseReportData($id);
            $format = '.docx';
            $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            $report = new $service;
            $documentConfig = DocumentDictionary::query()->get();
            $result = $this->respondWithCustomData($report->generateDocx($company, $certificate, $format, $documentConfig));
            // }
            // activity-log download hình ảnh hiện trạng
            $this->CreateActivityLog($certificate, $certificate, 'download', 'tải xuống hình ảnh hiện trạng');
            return $result;
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function printChungThu(Request $request, $id): JsonResponse
    {
        try {
            // $certificate = $this->certificateRepository->getCertificateAppraiseReportData($id);
            // $documentType = $certificate->document_type;
            // if (!empty($documentType) && (in_array('DCN', $documentType) || in_array('DT', $documentType)) ) {

            //     $format = 'docx';
            //     $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            //     $certificate = $this->certificateRepository->findById($id);
            //     $appraises = [];
            //     if (!empty($certificate->realEstate)) {
            //         $ids = [];
            //         foreach ($certificate->realEstate as $realEstate) {
            //             if (!empty($realEstate->appraises)) {
            //                 $ids[] = $realEstate->appraises->id; // real_estate_id  = appraise_id
            //             }
            //         }
            //         $appraises = $this->certificateAssetRepository->findByIds(json_encode($ids));
            //     }
            //     $result = $this->respondWithCustomData((new ChungThu())->generateDocx($certificate, $company, $appraises, $format));
            // } else {
            $service = 'App\\Services\\Document\\Certificate\\ReportCertificate' . $this->envDocument;
            $report = new $service;
            $certificate = $this->certificateRepository->getCertificateAppraiseReportData($id);
            $format = '.docx';
            $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            $documentConfig = DocumentDictionary::query()->get();
            $result = $this->respondWithCustomData($report->generateDocx($company, $certificate, $format, $documentConfig));
            // }
            // activity-log download chứng thư thẩm định
            $this->CreateActivityLog($certificate, $certificate, 'download', 'tải xuống chứng thư thẩm định');
            return $result;
        } catch (\Exception $exception) {
            // dd($exception);
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function printBaoCao(Request $request, $id): JsonResponse
    {
        try {
            // $certificate = $this->certificateRepository->getCertificateAppraiseReportData($id);
            // $documentType = $certificate->document_type;
            // if (!empty($documentType) && (in_array('DCN', $documentType) || in_array('DT', $documentType)) ) {
            //     $format = 'docx';
            //     $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            //     $certificate = $this->certificateRepository->findById($id);
            //     $appraises = [];
            //     // if ($certificate->appraises) {
            //     //     $ids = [];
            //     //     foreach ($certificate->appraises as $appraise) {
            //     //         $ids[] = $appraise->id;
            //     //     }
            //     //     $appraises = $this->certificateAssetRepository->findByIds(json_encode($ids));
            //     // }
            //     if (!empty($certificate->realEstate)) {
            //         $ids = [];
            //         foreach ($certificate->realEstate as $realEstate) {
            //             if (!empty($realEstate->appraises)) {
            //                 $ids[] = $realEstate->appraises->id; // real_estate_id  = appraise_id
            //             }
            //         }
            //         // dd(json_encode($ids));
            //         $appraises = $this->certificateAssetRepository->findByIds(json_encode($ids));
            //     }
            //     // dd(count($appraises));
            //     $result = $this->respondWithCustomData((new BaoCao())->generateDocx($company, $certificate, $appraises, $format));
            // } else {
            $service = 'App\\Services\\Document\\Appraisal\\ReportAppraisal' . $this->envDocument;
            $format = '.docx';
            $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            $certificate = $this->certificateRepository->getCertificateAppraiseReportData($id);

            $documentConfig = DocumentDictionary::query()->get();
            $report = new $service;
            $result = $this->respondWithCustomData($report->generateDocx($company, $certificate, $format, $documentConfig));

            // }
            // activity-log download báo cáo thẩm định
            $this->CreateActivityLog($certificate, $certificate, 'download', 'tải xuống báo cáo thẩm định');
            return $result;
        } catch (\Exception $exception) {
            // dd($exception);
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function printGiayYeuCauTDG($id, $is_pc = 0)
    {
        $service = 'App\\Services\\Document\\DocumentExport\\GiayYeuCau';
        return $this->printDocument($id, $is_pc, $service);
    }
    public function printHopDongTDG($id, $is_pc = 0)
    {
        $service = 'App\\Services\\Document\\DocumentExport\\HopDongTDG';
        return $this->printDocument($id, $is_pc, $service);
    }
    public function printKeHoachTDG($id, $is_pc = 0)
    {
        $service = 'App\\Services\\Document\\DocumentExport\\KeHoachTDG';
        return $this->printDocument($id, $is_pc, $service);
    }
    public function printBienBanThanhLy($id, $is_pc = 0)
    {
        $service = 'App\\Services\\Document\\DocumentExport\\BienBanThanhLy';
        return $this->printDocument($id, $is_pc, $service);
    }
    public function downloadAllOfficial($id)
    {
        $certificate = $this->certificateRepository->findById($id);
        if ($certificate->otherDocuments && count($certificate->otherDocuments) > 0) {
            $arrayLink = [];
            foreach ($certificate->otherDocuments as  $document) {
                if ($document->description != 'appendix' && $document->description != 'other' && $document->description != 'original') {
                    $item = [
                        'link' => $document->link,
                        'name' => $document->name,
                    ];

                    $arrayLink[] = $item;
                }
            }
            if (count($arrayLink) > 0) {
                // Tạo file zip mới
                $zipFileName = 'TaiLieuChinhThuc_HSTD_' . $id . '.zip';
                // $name = $path . $zipFileName;
                $name = sys_get_temp_dir() . '/' . $zipFileName;
                $zip = new ZipArchive;
                $zip->open($name, ZipArchive::CREATE | ZipArchive::OVERWRITE);

                // Tải các file về và thêm vào zip
                foreach ($arrayLink as $fileLink) {

                    $zip->addFile($fileLink['link'], $fileLink['name']);
                }

                // Đóng file zip
                $zip->close();
                $response = response()->download($name, $zipFileName, array('Content-Type: application/zip'))->deleteFileAfterSend(true);

                return $response;

                // Trả về file zip cho người dùng download
                // return Response::download($name, $zipFileName, array('Content-Type: application/octet-stream', 'Content-Length: ' . filesize($name)))->deleteFileAfterSend(true);
            } else {
                return response()->make('Có lỗi xảy ra trong quá trình tải xuống.', 404);
            }
        } else {
            return response()->make('Không có tài liệu chính thức nào để tải xuống.', 404);
        }
    }

    public function printDocument($id, $is_pc, $service): JsonResponse
    {
        $format = '.docx';
        $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
        // $certificate = Certificate::where('id', $id)->first();
        $select = ['*'];
        $with = [
            'assetType:id,acronym,description',
        ];
        if ($is_pc) {
            $realEstate = null;
            $precertificate = $this->preCertificateRepository->getPreCertificate($id);
            // if (isset($precertificate->certificate_id)) {
            //     $priceEstimatePrint = null;
            //     $certificate = $this->certificateRepository->dataPrintExport($precertificate->certificate_id);
            // } else {
            $priceEstimatePrint = [];
            $certificate = new Certificate;
            $fillable = $certificate->getFillable();

            foreach ($fillable as $attribute) {
                $certificate->$attribute = $precertificate->$attribute ?? null;
            }
            $appraiserManager = Appraiser::whereHas('appraisePosition', function ($query) {
                $query->where('description', 'TỔNG GIÁM ĐỐC');
            })
                ->with(['appraisePosition:id,description'])
                ->first();

            if ($appraiserManager) {
                $certificate->appraiserManager = $appraiserManager;
            }

            $certificate->service_fee = $precertificate->total_service_fee;
            $certificate->document_type = [];
            $certificate->appraisePurpose = $precertificate->appraisePurpose;
            $certificate->appraises = [];
            $certificate->apartmentAssetPrint = [];

            foreach ($precertificate->priceEstimates as $priceEstimates) {
                $tempPriceEstimate = new \stdClass();
                $tempPriceEstimate->total_area = 0;
                $tempPriceEstimate->appraise_asset = $priceEstimates['appraise_asset'];
                $tempPriceEstimate->full_address = $priceEstimates['full_address'];

                if (isset($priceEstimates['landFinalEstimate']) && count($priceEstimates['landFinalEstimate']) > 0) {
                    if (isset($priceEstimates['landFinalEstimate'][0]['apartmentFinals']) && isset($priceEstimates['landFinalEstimate'][0]['apartmentFinals'][0])) {
                        $tempPriceEstimate->total_area = $priceEstimates['landFinalEstimate'][0]['apartmentFinals'][0]['total_area'];
                    }
                    if (isset($priceEstimates['landFinalEstimate'][0]['lands'])) {
                        foreach ($priceEstimates['landFinalEstimate'][0]['lands'] as $elementland) {
                            $tempPriceEstimate->total_area += $elementland['planning_area'] + $elementland['main_area'];
                        }
                    }
                }

                $priceEstimatePrint[] = $tempPriceEstimate;
            }
            // }
        } else {
            $realEstate = RealEstate::with($with)->where('certificate_id', $id)->select($select)->first();
            $certificate = $this->certificateRepository->dataPrintExport($id);
            $priceEstimatePrint = null;
        }
        if ($certificate === null) {
            $data = ['message' => 'Có lỗi xảy ra trong quá trình xuất tài liệu', 'exception' => null];
            return $this->respondWithErrorData($data);
        }
        // $certificate = $this->certificateRepository->getCertificateAppraiseReportData($id);
        // $documentConfig = DocumentDictionary::query()->get();
        $report = new $service;
        return $this->respondWithCustomData($report->generateDocx($company, $certificate, $format, $realEstate, $priceEstimatePrint));
    }

    public function printBaoCaoTest1(Request $request, $id): JsonResponse
    {
        try {
            $format = 'docx';
            $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            $certificate = $this->certificateRepository->findById($id);
            $appraises = [];
            if ($certificate->appraises) {
                $ids = [];
                foreach ($certificate->appraises as $appraise) {
                    $ids[] = $appraise->id;
                }
                $appraises = $this->certificateAssetRepository->findByIds(json_encode($ids));
            }
            return $this->respondWithCustomData([]);
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function uploadImage(Request $request): JsonResponse
    {
        try {
            $image = ($request->data);
            $path = env('STORAGE_IMAGES') . '/' . 'certification_assets/';
            $name = $path . Uuid::uuid4()->toString() . '.png';
            Storage::put($name, file_get_contents($image));
            $fileUrl = Storage::url($name);
            return $this->respondWithCustomData(['link' => $fileUrl, 'picture_type' => 'png']);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::UPLOAD_IMAGE_ERROR, 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function updateComparisonFactor(Request $objects): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->certificateAssetRepository->updateComparisonFactor($objects));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function getComparisonFactor($id): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->certificateAssetRepository->getComparisonFactor($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function updateTangibleComparisonFactor(Request $objects): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->certificateAssetRepository->updateTangibleComparisonFactor($objects));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
