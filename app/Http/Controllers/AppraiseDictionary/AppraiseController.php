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
use App\Enum\ValueDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appraise\CreateAppraiseRequest;
use App\Http\Requests\Appraise\UpdateAppraiseRequest;
use App\Models\Appraise;
use App\Models\AppraiserCompany;
use App\Services\AppraiseAsset\AppraiseAsset;
use App\Services\Document\AssetReport;
use App\Services\Document\Appraise\PhuLuc1;
use App\Services\Document\Appraise\PhuLuc2;
use App\Services\Document\Appraise\PhuLucHinhAnh;
//use App\Services\Document\ChungThu;
use App\Services\Document\Appraise\BaoCao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Services\CommonService;

class AppraiseController extends Controller
{

    private AppraiseRepository $appraiseRepository;
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
    public function __construct(AppraiseRepository            $appraiseRepository,
                                CertificateRepository         $certificateRepository,
                                CompareAssetGeneralRepository $compareAssetGeneralRepository,
                                UserRepository                $userRepository,
                                DictionaryRepository          $dictionaryRepository,
                                BuildingPriceRepository       $buildingPriceRepository,
                                AppraiseAssetRepository       $appraiseAssetRepository,
                                AppraiserCompanyRepository    $appraiserCompanyRepository)
    {
        $this->appraiseRepository = $appraiseRepository;
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
            return $this->respondWithCustomData($this->appraiseRepository->findPaging());
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
            return $this->respondWithCustomData($this->appraiseRepository->findAll());
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
            $result = $this->appraiseRepository->updateStatus($id, $request);
            if(is_string($result)) {
                $data = ['message' => $result];
                return $this->respondWithErrorData($data);
            } else {
                return $this->respondWithCustomData($result);
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
                $result = $this->appraiseRepository->findByIdTest($id);
            } else {
                $result = $this->appraiseRepository->findById($id);
            }

            if (isset($result->assetGeneral) && !empty($result->assetGeneral)) {
                CommonService::getAssetPriceTotal($result);
            }

            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            dd($exception);
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
            return $this->respondWithCustomData($this->appraiseRepository->findByIds($ids));
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
            $objects = $request->toArray();
            if (isset($objects['properties'])) {
                foreach( $objects['properties'] as $property){
                    if(isset($property['property_detail']))
                    {
                        $property_detail  = $property['property_detail'];
                        foreach ($property_detail as $item) {
                            if ($item['circular_unit_price'] <= ValueDefault::PRICE_VALIDATION_VALUE  ) {
                                $data = ['message' => ValueDefault::PRICE_VALIDATION_MESSAGE_UBND, 'exception' => null];
                                return $this->respondWithErrorData($data);
                            }
                        }
                    }
                }
            }
            return $this->respondWithCustomData($this->appraiseRepository->createAppraise($request->toArray()));
        } catch (\Exception $exception) {
            dd($exception);
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
              $objects = $request->toArray();
            if (isset($objects['properties'])) {
                foreach( $objects['properties'] as $property){
                    if(isset($property['property_detail']))
                    {
                        $property_detail  = $property['property_detail'];
                        foreach ($property_detail as $item) {
                            if ($item['circular_unit_price'] <= ValueDefault::PRICE_VALIDATION_VALUE  ) {
                                $data = ['message' => ValueDefault::PRICE_VALIDATION_MESSAGE_UBND, 'exception' => null];
                                return $this->respondWithErrorData($data);
                            }
                        }
                    }
                }
            }
            return $this->respondWithCustomData($this->appraiseRepository->updateAppraise($id, $request->toArray()));
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
    public function destroy($id): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiseRepository->deleteAppraise($id));
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
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /* public function printChungThu(Request $request, $id): JsonResponse
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
                $appraises = $this->appraiseRepository->findByIds(json_encode($ids));
            }
            return $this->respondWithCustomData((new ChungThu())->generateDocx($certificate, $company, $appraises, $format));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
     */

    public function uploadImage(Request $request): JsonResponse
    {
        try {
            $image = ($request->data);
            $path = env('STORAGE_IMAGES') .'/'. 'certification_assets/';
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

    public function uploadDocument(Request $request): JsonResponse
    {
        try {
            $files = $request->file('files');
            $fileUrls = []; 
            $path = env('STORAGE_OTHERS') .'/'. 'certification_assets/';
            if (isset($files) && !empty($files)) {
                foreach ($files as $file) {
                    $fileName = $file->getClientOriginalName();
                    $fileType = $file->getClientOriginalExtension();
                    $fileSize = $file->getSize();
                    $uuidName = Uuid::uuid4()->toString();
                    $name = $path . $uuidName . '.' . $fileType;
                    Storage::put($name, file_get_contents($file));
                    $fileUrl = Storage::url($name);
                    $item = [
                        'uuidName' => $uuidName,
                        'originalName' => $fileName,
                        'link' => $fileUrl,
                        'type' => $fileType,
                        'size' => $fileSize,
                    ];
                    $fileUrls[] = $item;
                }
            }
            return $this->respondWithCustomData($fileUrls);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::UPLOAD_IMAGE_ERROR, 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function downloadDocument($uuid, $type, Request $request): JsonResponse 
    {
        try {
            $path = env('STORAGE_OTHERS') .'/'. 'certification_assets/';
            $link = $path . $uuid . '.' . $type;
            $fileUrl = Storage::url($link);
            if (isset($item->link)) {
                return $this->respondWithCustomData(['file_name' => $uuid, 'url' => $fileUrl]);
            } else {
                return $this->respondWithErrorData(['message' => 'Không tìm thấy link tải', 'exception' => '']);
            }
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
    public function deleteDocument(Request $request): JsonResponse
    {
        try {
            $link = $request->data;
            Storage::disk(env('FILESYSTEM_DRIVER'))->delete($link);
            return $this->respondWithCustomData( ['message' => 'Xóa thành công' ]);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::UPLOAD_IMAGE_ERROR, 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function updateComparisonFactor(Request $objects): JsonResponse
    {
        try {
            $data = $objects->toArray();
            if($data['layer_cutting_procedure']){
                if($data['layer_cutting_price']<=0){
                    $data = ['message' => 'Đơn giá sau cắt lớp phải lớn hơn 0', 'exception' => null];
                    return $this->respondWithErrorData($data);

                }
            }
            if(isset($data['remaining_price']['remaining_commerce_price'])){
                if($data['remaining_price']['remaining_commerce_price']<=0){
                    $data = ['message' => 'Đơn giá đất '. $data['remaining_price']['land_type'] .' thị trường phải lớn hơn 0', 'exception' => null];
                    return $this->respondWithErrorData($data);

                }
            }
            return $this->respondWithCustomData($this->appraiseRepository->updateComparisonFactor($objects));
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }


    public function getComparisonFactor($id): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->appraiseRepository->getComparisonFactor($id));
        } catch (\Exception $exception) {
            // dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function updateTangibleComparisonFactor(Request $objects): JsonResponse
    {
        if( isset($objects["construction_company"]) ) {
            foreach($objects["construction_company"] as $item) {
                if( !isset($item["unit_price_m2"]) || !is_numeric($item["unit_price_m2"]) ) {
                    $data = ['message' => "Dữ liệu nhập vào sai định dạng vui lòng nhập lại."];
                    return $this->respondWithErrorData($data);
                }
            }
        }
        //if(!is_numeric())
        try {
            $result = $this->appraiseRepository->updateTangibleComparisonFactor($objects);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
            // return $this->respondWithCustomData($this->appraiseRepository->updateTangibleComparisonFactor($objects));
        } catch (\Exception $exception) {
            // dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
