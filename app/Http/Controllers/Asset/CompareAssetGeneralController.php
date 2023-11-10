<?php

namespace App\Http\Controllers\Asset;

use App\Contracts\AppraiserCompanyRepository;
use App\Contracts\BuildingPriceRepository;
use App\Contracts\CertificateRepository;
use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\DictionaryRepository;
use App\Contracts\EstimatePriceLogRepository;
use App\Contracts\EstimatePriceRepository;
use App\Contracts\UserRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Enum\ValueDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\CompareAssetGeneralRequest;
use App\Models\Certificate;
use App\Models\CompareAssetGeneral;
use App\Notifications\ActivityLog;
use App\Services\CommonService;
use App\Services\Document\AssetReport;
use App\Services\EstimatePrice\EstimatePrice;
use App\Services\Excel\ExportComparisonAssets;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Storage;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Auth\SignInResult\SignInResult;
use Kreait\Firebase\Exception\FirebaseException;
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;
use Session;


class CompareAssetGeneralController extends Controller
{
    use ActivityLog;
    public CompareAssetGeneralRepository $compareAssetGeneralRepository;
    public UserRepository $userRepository;
    public DictionaryRepository $dictionaryRepository;
    public BuildingPriceRepository $buildingPriceRepository;
    public EstimatePriceLogRepository $estimatePriceLogRepository;
    private EstimatePriceRepository $estimatePriceRepository;
    public CertificateRepository $certificateRepository;
    private AppraiserCompanyRepository $appraiserCompanyRepository;

    private array $permissionView =['VIEW_PRICE'];
    private array $permissionAdd =['ADD_PRICE'];
    private array $permissionEdit =['EDIT_PRICE'];
    private array $permissionExport =['EXPORT_PRICE'];


    /**
     * ProvinceController constructor.
     */
    public function __construct(CompareAssetGeneralRepository $compareAssetGeneralRepository,
                                UserRepository                $userRepository,
                                DictionaryRepository          $dictionaryRepository,
                                BuildingPriceRepository       $buildingPriceRepository,
                                EstimatePriceLogRepository    $estimatePriceLogRepository,
                                EstimatePriceRepository       $estimatePriceRepository,
                                CertificateRepository       $certificateRepository,
                                AppraiserCompanyRepository $appraiserCompanyRepository)

    {
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
        $this->userRepository = $userRepository;
        $this->dictionaryRepository = $dictionaryRepository;
        $this->buildingPriceRepository = $buildingPriceRepository;
        $this->estimatePriceLogRepository = $estimatePriceLogRepository;
        $this->estimatePriceRepository = $estimatePriceRepository;
        $this->certificateRepository = $certificateRepository;
        $this->appraiserCompanyRepository = $appraiserCompanyRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // return $this->respondWithCustomData($this->compareAssetGeneralRepository->findPaging());
            return $this->respondWithCustomData($this->compareAssetGeneralRepository->findPaging2());
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
            return $this->respondWithCustomData($this->compareAssetGeneralRepository->findAll());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @return JsonResponse
     */
    public function findAllInElastic(): JsonResponse
    {
        try {
            $property_type = request()->get('property_type');
            if($property_type == null){
                $certificate1 = $this->certificateRepository->getFinishCertificateAssets();
                $certificate2 = $this->certificateRepository->getFinishCertificateApartment();
                $certificateAssets = array_merge ($certificate1, $certificate2);
            }elseif($property_type == 0){
                $certificateAssets = $this->certificateRepository->getFinishCertificateAssets();
                // dd($certificateAssets);
            }else{
                $certificateAssets = $this->certificateRepository->getFinishCertificateApartment();
                // dd($certificateAssets);
            }
            // $certificateAssets = $this->certificateRepository->getFinishCertificateAssets();
            
            $comparisonAsset = $this->compareAssetGeneralRepository->findAllInElastic_v3();
            // $data = array_map(null,$certificateAssets,$comparisonAsset);
            // $data =  array_merge ($certificateAssets, []);
            $data =  array_merge ($certificateAssets, $comparisonAsset ? $comparisonAsset->toArray() : []);
            return $this->respondWithCustomData($data);
            // return $this->respondWithCustomData($certificateAssets);
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
            return $this->respondWithCustomData($this->compareAssetGeneralRepository->findById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param CompareAssetGeneralRequest $request
     * @return JsonResponse
     */
    public function store(CompareAssetGeneralRequest $request): JsonResponse
    {
        $objects = $request->toArray();
        if (isset($objects['tangible_assets'])) {
            $isValidTSPrice = true;
            foreach ($objects['tangible_assets'] as $tangibleAssetData) {
                if (isset($tangibleAssetData['unit_price_m2'])) {
                    if (($tangibleAssetData['unit_price_m2'] <= 0)) {
                        $isValidTSPrice = false;
                    }
                } else {
                    $isValidTSPrice = false;
                }
            }

            if(!$isValidTSPrice) {
                $data = ['message' => "Đơn giá xây dựng phải lớn hơn 0.", 'exception' => null];
                return $this->respondWithErrorData($data);
            }
        }
        if (isset($objects['properties'])) {
            foreach($objects['properties'] as $property)
            {
                if(isset($property['property_detail'])){
                    $property_detail  = $property['property_detail'];
                    foreach ($property_detail as $item) {
                        if ($item['circular_unit_price'] <= ValueDefault::PRICE_VALIDATION_VALUE  ) {
                            $data = ['message' => ValueDefault::PRICE_VALIDATION_MESSAGE_UBND, 'exception' => null];
                            return $this->respondWithErrorData($data);
                        }
                        if ($item['price_land'] <=ValueDefault::PRICE_VALIDATION_VALUE  ) {
                            $data = ['message' => ValueDefault::PRICE_VALIDATION_MESSAGE_OTHER, 'exception' => null];
                            return $this->respondWithErrorData($data);
                        }
                    }
                }

            }
        }

        $lock = Cache::lock('comparison_asset_create', 5);
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::PRICE_SCREEN)) {
                $result = [];
                if ($lock->get()) {
                    $result = $this->compareAssetGeneralRepository->createCompareAssetGeneral($request->toArray());
                } else {
                    $data = ['message' => 'Hệ thống đang xử lý, vui lòng đợi trong giây lát.'];
                    return $this->respondWithErrorData($data, 401);
                }
                if(isset($result['message']) && isset($result['exception']))
                    return $this->respondWithErrorData( $result);
                return $this->respondWithCustomData($result);
                // return $this->respondWithCustomData($this->compareAssetGeneralRepository->createCompareAssetGeneral($request->toArray()));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::CREATE_ASSET_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        } finally {
            $lock->release();
         }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadImage(Request $request): JsonResponse
    {
        try {
            $image = $request->file('image');
            $path =env('STORAGE_IMAGES') .'/'. 'comparison_assets/';
            // $name = $path . Uuid::uuid4()->toString() . '.' . $image->getClientOriginalExtension();
            $name = Uuid::uuid4()->toString() . '.' . $image->getClientOriginalExtension();
            // dd(Storage::disk('s3'));
            Storage::put($name, file_get_contents($image));
            
            $fileUrl = Storage::url($name);

            //test s3
            // Storage::disk('spaces')->put($name, 'public');
            // $fileUrl = Storage::disk('spaces')->url($name);

            // test firebase

            // $image = $request->file('image');
            // $firebase_storage_path = env('STORAGE_IMAGES') .'/'. 'comparison_assets/';
            // $name = $firebase_storage_path . Uuid::uuid4()->toString();
            // $localfolder = public_path('firebase-temp-uploads') .'/';  
            // $extension = $image->getClientOriginalExtension();  
            // $file      = $name. '.' . $extension;  
            // $storage = app('firebase.storage');
            // if ($image->move($localfolder, $file)) {  
            //     $uploadedfile = fopen($localfolder.$file, 'r');  
            //     $storage->getBucket()->upload($uploadedfile, ['name' => $firebase_storage_path . $file]);  
            //     //will remove from local laravel folder  
            //     unlink($localfolder . $file);  
            //     Session::flash('message', 'Succesfully Uploaded');
            //     $expiresAt = new \DateTime('tomorrow');
            //     $fileUrl = $storage->getBucket()->object($firebase_storage_path . $file)->signedUrl($expiresAt);
            // } 
            return $this->respondWithCustomData(['link' => $fileUrl, 'picture_type' => $image->extension()]);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::UPLOAD_IMAGE_ERROR, 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param CompareAssetGeneralRequest $request
     * @return JsonResponse
     */
    public function update($id, CompareAssetGeneralRequest $request): JsonResponse
    {
        $objects = $request->toArray();
        if (isset($objects['tangible_assets'])) {
            $isValidTSPrice = true;
            foreach ($objects['tangible_assets'] as $tangibleAssetData) {
                if (isset($tangibleAssetData['unit_price_m2'])) {
                    if (($tangibleAssetData['unit_price_m2'] <= 0)) {
                        $isValidTSPrice = false;
                    }
                } else {
                    $isValidTSPrice = false;
                }
            }
            if(!$isValidTSPrice) {
                $data = ['message' => "Đơn giá xây dựng phải lớn hơn 0.", 'exception' => null];
                return $this->respondWithErrorData($data);
            }
        }
        if (isset($objects['properties'])) {
            foreach($objects['properties'] as $property)
            {
                if(isset($property['property_detail'])){
                    $property_detail  = $property['property_detail'];
                    foreach ($property_detail as $item) {
                        if ($item['circular_unit_price'] <= ValueDefault::PRICE_VALIDATION_VALUE  ) {
                            $data = ['message' => ValueDefault::PRICE_VALIDATION_MESSAGE_UBND, 'exception' => null];
                            return $this->respondWithErrorData($data);
                        }
                        if ($item['price_land'] <=ValueDefault::PRICE_VALIDATION_VALUE  ) {
                            $data = ['message' => ValueDefault::PRICE_VALIDATION_MESSAGE_OTHER, 'exception' => null];
                            return $this->respondWithErrorData($data);
                        }
                    }
                }

            }
        }
        try {
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::PRICE_SCREEN)) {
                if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::PRICE_SCREEN)) {
                    // return $this->respondWithCustomData($this->compareAssetGeneralRepository->updateCompareAssetGeneral($id, $request->toArray()));
                    $result = $this->compareAssetGeneralRepository->updateCompareAssetGeneral($id, $request->toArray());
                    
                    if(isset($result['message']) && isset($result['exception']))
                    {
                        return $this->respondWithErrorData( $result);
                    } else {
                        $user = CommonService::getUser();
                        $data_log['updated_by'] = $user->name;
                        $data_log['updated_at'] = now();
                        $edited = CompareAssetGeneral::query()->where('id', $id)->first();
                        $log_note = $objects['data_change'];
                        $note = "";
                        for ($i=0; $i < count($log_note); $i++) { 
                            $e = $log_note[$i];
                            $note= $note.$e.', ';
                        }
                        $note = substr_replace($note ,"",-2);
                        $this->CreateActivityLog($edited, $data_log, 'capnhat_TSSS', 'Cập nhật tài sản so sánh', $note);
                        return $this->respondWithCustomData($result);
                    }      
                } else {
                    $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                    return $this->respondWithErrorData($data, 403);
                }
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::CREATE_ASSET_ERROR, 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus($id, Request $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::PRICE_SCREEN)) {
                return $this->respondWithCustomData($this->compareAssetGeneralRepository->updateStatusCompareAssetGeneral($id, $request->toArray()));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
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
    public function destroy($id): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::DELETE_PERMISSION . '_' . ScreensDefault::PRICE_SCREEN)) {
                return $this->respondWithCustomData($this->compareAssetGeneralRepository->deleteCompareAssetGeneral($id));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
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
    public function print($id): JsonResponse
    {
        try {
            $format = 'docx';
            if (request()->get('format') == 'pdf') {
                $format = 'pdf';
            }
            $company = $this->appraiserCompanyRepository->getCompany();
            $result = $this->respondWithCustomData((new AssetReport())->generateDocx($company, ($this->compareAssetGeneralRepository->findByIds($id)), $format));
            $certificateId = request()->get('certificate_id');
            if( !($certificateId == null)){
                $edited = Certificate::query()->where('id', $certificateId)->first();
                // activity-log download phiếu thu thập thông tin tài sản
                $this->CreateActivityLog($edited, $edited, 'download', 'tải xuống phiếu thu thập thông tin tài sản');
            }
            return $result;
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function estimatePrice(Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData((new EstimatePrice(
                $this->compareAssetGeneralRepository,
                $this->userRepository,
                $this->dictionaryRepository,
                $this->buildingPriceRepository,
                $this->estimatePriceLogRepository,
                $this->estimatePriceRepository))
                ->estimatePriceLandAssets($request->toArray()));

        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @return JsonResponse
     */
    public function createIndex(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->compareAssetGeneralRepository->createVersionIndex());
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
    public function findVersionById($id): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->compareAssetGeneralRepository->findVersionById($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }


    /**
     * @return JsonResponse
     */
    public function findAllInElastic_v2(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->compareAssetGeneralRepository->findAllInElastic_v2());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function assetExport(Request $request)
    {
        if(! CommonService::checkUserPermission($this->permissionExport)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::PERMISSION_ERROR ,'exception' =>''], 403);
        }
        $result = $this->compareAssetGeneralRepository->exportAsset();
        // dd($result->toArray());
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData((new ExportComparisonAssets())->exportAsset($result));
    }

}
