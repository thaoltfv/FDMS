<?php

namespace App\Http\Controllers\UnitPrice;

use App\Contracts\DictionaryRepository;
use App\Contracts\DistanceRepository;
use App\Contracts\DistrictRepository;
use App\Contracts\ProvinceRepository;
use App\Contracts\StreetRepository;
use App\Contracts\UnitPriceRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dictionary\DictionaryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UnitPriceController extends Controller
{

    private UnitPriceRepository $unitPriceRepository;
    private ProvinceRepository $provinceRepository;
    private StreetRepository $streetRepository;
    private DistrictRepository $districtRepository;
    private DistanceRepository $distanceRepository;
    private DictionaryRepository $dictionaryRepository;

    /**
     * ProvinceController constructor.
     */
    public function __construct(UnitPriceRepository  $unitPriceRepository,
                                ProvinceRepository   $provinceRepository,
                                StreetRepository     $streetRepository,
                                DistrictRepository   $districtRepository,
                                DistanceRepository   $distanceRepository,
                                DictionaryRepository $dictionaryRepository)
    {
        $this->unitPriceRepository = $unitPriceRepository;
        $this->provinceRepository = $provinceRepository;
        $this->streetRepository = $streetRepository;
        $this->districtRepository = $districtRepository;
        $this->distanceRepository = $distanceRepository;
        $this->dictionaryRepository = $dictionaryRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->unitPriceRepository->findPaging());
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
            return $this->respondWithCustomData($this->unitPriceRepository->findAll());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }


    /**
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::VIEW_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->unitPriceRepository->findUnitPrice());
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
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->unitPriceRepository->createUnitPrice($request->toArray()));
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
    public function findById($id): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::VIEW_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                $result = $this->unitPriceRepository->findById($id);
                $province = $result->province;
                $district = $result->district;
                $street = $result->street;
                $distance = $result->distance;
                $landType = $result->land_type;

                $district = trim(str_replace(['thị xã', 'thị trấn', 'thành phố', 'quận', 'huyện'], '', strtolower($district)));
                $result['provinces'] = null;
                $result['districts'] = null;
                $result['streets'] = null;
                $result['distances'] = null;
                $result['land_types'] = null;

                $result['provinces'] = $this->provinceRepository->findByName($province);
                if ($result['provinces'] != null) {
                    $result['districts'] = $this->districtRepository->findByName($district, $result['provinces']->id);
                }
                if ($result['districts'] != null && $street) {
                    $result['streets'] = $this->streetRepository->findByName($street, $result['districts']->id);
                }
                if ($result['distance'] != null && $distance) {
                    $result['distances'] = $this->distanceRepository->findByName($distance, $result['streets']->id);
                }
                if ($landType) {
                    $result['land_types'] = $this->dictionaryRepository->findByName($landType);
                }
                return $this->respondWithCustomData($result);
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
     * @param DictionaryRequest $request
     * @return JsonResponse
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->unitPriceRepository->updateUnitPrice($id, $request->toArray()));
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
            if ($this->getUserPermission(PermissionsDefault::DELETE_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->unitPriceRepository->deleteUnitPrice($id));
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
