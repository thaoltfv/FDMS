<?php

namespace App\Http\Controllers\Address;

use App\Contracts\DistrictRepository;
use App\Contracts\ProvinceRepository;
use App\Contracts\StreetRepository;
use App\Contracts\WardRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\AddressRequest;
use App\Http\Requests\Address\FindStreetRequest;
use App\Http\Requests\Address\ProvinceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{

    private ProvinceRepository $provinceRepository;
    private StreetRepository $streetRepository;
    private DistrictRepository $districtRepository;
    private WardRepository $wardRepository;


    /**
     * @param ProvinceRepository $provinceRepository
     * @param StreetRepository $streetRepository
     * @param DistrictRepository $districtRepository
     */
    public function __construct(ProvinceRepository $provinceRepository,
                                StreetRepository   $streetRepository,
                                DistrictRepository $districtRepository,
                                WardRepository     $wardRepository)
    {
        $this->provinceRepository = $provinceRepository;
        $this->streetRepository = $streetRepository;
        $this->districtRepository = $districtRepository;
        $this->wardRepository = $wardRepository;
    }

    /**
     * @param AddressRequest $request
     * @return JsonResponse
     */
    public function findAddress(AddressRequest $request): JsonResponse
    {
        try {
            $province = $request->province;
            $district = $request->district;
            $wards = $request->wards;

            $district = trim(str_replace(['thị xã', 'thị trấn', 'thành phố', 'quận', 'huyện'], '', mb_strtolower($district)));
            $result['province'] = null;
            $result['district'] = null;
            $result['ward'] = null;
            
            $result['province'] = $this->provinceRepository->findByName($province);
            if ($result['province'] != null) {
                $result['district'] = $this->districtRepository->findByName($district, $result['province']->id);
            }
            if ($result['district'] != null && $wards) {
                $result['ward'] = [];
                foreach ($wards as $ward) {
                    $ward = trim(str_replace(['xã', 'phường'], '', mb_strtolower($ward)));
                    $result['ward'] = $this->wardRepository->findByName($ward, $result['district']->id);
                    if ($result['ward'] != null) {
                        break;
                    }
                }
            }
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function findStreet(FindStreetRequest $request): JsonResponse
    {
        try {
            $districtId = $request->district_id;
            $street = $request->street;
            $result['street'] = null;
            if ($districtId != null && $street) {
                $result['street'] = $this->streetRepository->findByName($street, $districtId);
            }
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

}
