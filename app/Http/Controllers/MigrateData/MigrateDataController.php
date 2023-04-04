<?php

namespace App\Http\Controllers\MigrateData;

use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\CompareGeneralPicRepository;
use App\Contracts\DictionaryRepository;
use App\Contracts\DistrictRepository;
use App\Contracts\DonavaOldEstatesRepository;
use App\Contracts\DonavaOldUserRepository;
use App\Contracts\MigrateStatusRepository;
use App\Contracts\ProvinceRepository;
use App\Contracts\StreetRepository;
use App\Contracts\UnitPriceRepository;
use App\Contracts\UserRepository;
use App\Contracts\WardRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dictionary\DictionaryRequest;
use App\Repositories\EloquentDictionaryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MigrateDataController extends Controller
{

    private DonavaOldEstatesRepository $donavaOldEstates;
    private DonavaOldUserRepository $donavaOldUserRepository;
    private UserRepository $userRepository;
    private CompareAssetGeneralRepository $compareAssetGeneralRepository;
    private DictionaryRepository $dictionaryRepository;
    private ProvinceRepository $provinceRepository;
    private DistrictRepository $districtRepository;
    private WardRepository $wardRepository;
    private StreetRepository $streetRepository;
    private CompareGeneralPicRepository $compareGeneralPicRepository;
    private MigrateStatusRepository $migrateStatusRepository;


    /**
     * @param DonavaOldEstatesRepository $donavaOldEstates
     * @param DonavaOldUserRepository $donavaOldUserRepository
     * @param CompareAssetGeneralRepository $compareAssetGeneralRepository
     * @param DictionaryRepository $dictionaryRepository
     * @param UserRepository $userRepository
     * @param ProvinceRepository $provinceRepository
     * @param DistrictRepository $districtRepository
     * @param WardRepository $wardRepository
     * @param StreetRepository $streetRepository
     * @param CompareGeneralPicRepository $compareGeneralPicRepository
     * @param MigrateStatusRepository $migrateStatusRepository
     */

    public function __construct(DonavaOldEstatesRepository    $donavaOldEstates,
                                DonavaOldUserRepository       $donavaOldUserRepository,
                                CompareAssetGeneralRepository $compareAssetGeneralRepository,
                                DictionaryRepository          $dictionaryRepository,
                                UserRepository                $userRepository,
                                ProvinceRepository            $provinceRepository,
                                DistrictRepository            $districtRepository,
                                WardRepository                $wardRepository,
                                StreetRepository              $streetRepository,
                                CompareGeneralPicRepository   $compareGeneralPicRepository,
                                MigrateStatusRepository       $migrateStatusRepository
    )
    {
        $this->donavaOldEstates = $donavaOldEstates;
        $this->donavaOldUserRepository = $donavaOldUserRepository;
        $this->userRepository = $userRepository;
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
        $this->dictionaryRepository = $dictionaryRepository;
        $this->provinceRepository = $provinceRepository;
        $this->districtRepository = $districtRepository;
        $this->wardRepository = $wardRepository;
        $this->streetRepository = $streetRepository;
        $this->compareGeneralPicRepository = $compareGeneralPicRepository;
        $this->migrateStatusRepository = $migrateStatusRepository;
    }

    /**
     * @return JsonResponse
     */
    public function asyncDonavaToDb(): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::PROPERTIES_SCREEN)) {
                $perPage = (int)request()->get('limit');
                $page = (int)request()->get('page');
                $status = $this->migrateStatusRepository->CheckProcessIsRunning($perPage, $page);
                if ($status) {
                    return $this->respondWithCustomData(['message' => "Tác vụ đã hoặc đang thực hiện ở quy trình khác, vui lòng thử lại sau!"]);
                } else {
                    if ($page == 1) {
                        $this->createIndex();
                    }
                    MigrationAsyncDonavaToDb::dispatchAfterResponse(
                        $this->donavaOldEstates,
                        $this->donavaOldUserRepository,
                        $this->compareAssetGeneralRepository,
                        $this->dictionaryRepository,
                        $this->userRepository,
                        $this->provinceRepository,
                        $this->districtRepository,
                        $this->wardRepository,
                        $this->streetRepository,
                        $this->migrateStatusRepository,
                        $perPage,
                        $page
                    );
                    return $this->respondWithCustomData(['message' => "Tác vụ đang thực hiện!"]);
                }
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

    public function asyncDonavaToElastic(): JsonResponse
    {
        try {
                $perPage = (int)request()->get('limit');
                $page = (int)request()->get('page');
                $id = (int)request()->get('id');
                $this->migrateStatusRepository->updateMigrateStatus($id, ['status' => 4]);
                MigrationAsyncDonavaToElastic::dispatchAfterResponse($this->compareAssetGeneralRepository, $this->migrateStatusRepository, $perPage, $page, $id);
                return $this->respondWithCustomData(['message' => "Đang đồng bộ dữ liệu với estates!"]);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function asyncImageToS3(): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::PROPERTIES_SCREEN)) {
                $id = (int)request()->get('id');
                $perPage = (int)request()->get('limit');
                $page = (int)request()->get('page');
                $this->migrateStatusRepository->updateMigrateStatus($id, ['status' => 2]);
                MigrationAsyncImageToS3::dispatchAfterResponse($this->compareAssetGeneralRepository, $this->compareGeneralPicRepository, $this->migrateStatusRepository, $perPage, $page, $id);
                return $this->respondWithCustomData(['message' => "Đang đồng bộ hình ảnh!"]);
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

    public function createIndex(): JsonResponse
    {
        try {
                return $this->respondWithCustomData($this->compareAssetGeneralRepository->createIndex());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function list(): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::VIEW_PERMISSION . '_' . ScreensDefault::PROPERTIES_SCREEN)) {
                return $this->respondWithCustomData($this->migrateStatusRepository->findPaging());
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

    public function updateMigrateStatus(): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::PROPERTIES_SCREEN)) {
                MigrationUpdateMigrationStatus::dispatchAfterResponse($this->compareAssetGeneralRepository, $this->migrateStatusRepository);
                return $this->respondWithCustomData(['message' => "Đang đồng bộ trạng thái dữ liệu!"]);
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

}
