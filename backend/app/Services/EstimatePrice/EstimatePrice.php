<?php

namespace App\Services\EstimatePrice;

use App\Contracts\BuildingPriceRepository;
use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\DictionaryRepository;
use App\Contracts\EstimatePriceLogRepository;
use App\Contracts\EstimatePriceRepository;
use App\Contracts\UserRepository;
use App\Enum\EstimateAssetDefault;
use App\Http\ResponseTrait;
use App\Services\Firebase\FirebaseClient;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Tymon\JWTAuth\Facades\JWTAuth;

class EstimatePrice
{
    use ResponseTrait;

    private CompareAssetGeneralRepository $compareAssetGeneralRepository;
    private EstimatePriceRepository $estimatePriceRepository;
    private UserRepository $userRepository;
    private DictionaryRepository $dictionaryRepository;
    private BuildingPriceRepository $buildingPriceRepository;
    private EstimatePriceLogRepository $estimatePriceLogRepository;


    /**
     * FirebaseClient constructor.
     */
    public function __construct(CompareAssetGeneralRepository $compareAssetGeneralRepository,
                                UserRepository                $userRepository,
                                DictionaryRepository          $dictionaryRepository,
                                BuildingPriceRepository       $buildingPriceRepository,
                                EstimatePriceLogRepository    $estimatePriceLogRepository,
                                EstimatePriceRepository       $estimatePriceRepository)
    {
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
        $this->userRepository = $userRepository;
        $this->dictionaryRepository = $dictionaryRepository;
        $this->buildingPriceRepository = $buildingPriceRepository;
        $this->estimatePriceLogRepository = $estimatePriceLogRepository;
        $this->estimatePriceRepository = $estimatePriceRepository;
    }


    /**
     * @param $request
     * @return array
     */
    public function estimatePriceLandAssets($request): array
    {
        $totalPrice = 0;
        $result = [];
        $recognizedLandError = [];
        $unRecognizedLandError = [];
        if ($request['estimate_type'] == EstimateAssetDefault::ESTIMATE_APARTMENT_TYPE) {
            $aparment = $this->estimatePriceRepository->estimateApartmentAsset($request);
            $result['result']['total_price'] =  $aparment['apartment']['estimate_price'] ?? 0;
            $result['result']['unit_price'] = $aparment['apartment']['average_unit_price']?? 0;
            $result['result']['status'] = $aparment['status'] ?? 0;
            $result['result']['error_message'] = $aparment['error_message'] ?? null;
            $result['reliability'] = $aparment['reliability'] ?? null;
            $result['assets'] = $aparment['assets'] ?? null;
            $result['steps'] = $aparment['steps'] ?? null;
            $result['error_message'] = $aparment['error_message'] ?? null;

        } else {
            $defaultLandTypeUbanPurpose = ($this->dictionaryRepository->findByName(EstimateAssetDefault::DEFAULT_UBAN_LAND_TYPE))->id;
            $defaultLandTypeCountryPurpose = ($this->dictionaryRepository->findByName(EstimateAssetDefault::DEFAULT_COUNTRY_LAND_TYPE))->id;
            $recognized = $this->estimatePriceRepository->estimateRecognizedFrontSiteAsset($request);
            if (isset($recognized['recognized'])) {
                $result['recognized'] = $recognized['recognized'] ?? null;
            }
            if (in_array($request['district_id'], EstimateAssetDefault::URBAN_ASSET)) {
                if ($request['front_side'] == 1) {
                    $unrecognized = ($this->estimatePriceRepository->estimateUnrecognizedFrontSiteAsset($request, $defaultLandTypeUbanPurpose));
                } else {
                    $unrecognized = $this->estimatePriceRepository->estimateUnrecognizedUnfrontSiteAsset($request);
                }
            } else {
                if ($request['front_side'] == 1) {
                    $unrecognized = $this->estimatePriceRepository->estimateUnrecognizedFrontSiteAsset($request, $defaultLandTypeCountryPurpose);
                } else {
                    $unrecognized = $this->estimatePriceRepository->estimateUnrecognizedUnfrontSiteAsset($request);
                }
            }
            if (isset($unrecognized['unrecognized'])) {
                $result['unrecognized'] = $unrecognized['unrecognized'] ?? null;
                $result['reliability'] = $unrecognized['reliability'] ?? null;
                $result['assets'] = $unrecognized['assets'] ?? null;
                $result['status'] = $unrecognized['status'] ?? null;
                $result['error_message'] = $unrecognized['error_message'] ?? null;
                $result['steps'] = $unrecognized['steps'] ?? null;

            }


            if ($request['building']) {
                $value = [];
                foreach ($request['building'] as $building) {
                    $averageBuildingUnitPrice = round($this->buildingPriceRepository->getAverageBuildEstimatePrice($building),-5);

                    $value['building'][] = [
                        'building_category' => $building['building_category'],
                        'remaining_quality' => $building['remaining_quality'],
                        'area' => $building['area'],
                        'average_building_unit_price' => $averageBuildingUnitPrice,
                        'estimate_price' => round($averageBuildingUnitPrice * $building['area'] * ($building['remaining_quality'] / 100),-5),
                    ];
                    $result['steps'] = $building['steps'] ?? null;
                }
                $result['building'] = $value['building'];
            }
            $status = 1;
            $error_message = null;
            if (isset($recognized['recognized'])) {
                $error_message = ($recognized['error_message'] != null) ? $recognized['error_message'] : $error_message;
                foreach ($recognized['recognized'] as $key=>$value) {
                    $result['recognized'][$key]['average_land_unit_price']=$value['average_land_unit_price'];
                    $result['recognized'][$key]['estimate_price']=$value['estimate_price'];
                    $totalPrice += $value['estimate_price'];
                    if ($value['estimate_price'] ==0){
                        $status =0;
                        $recognizedLandError[]=$value['land_type_purpose_name'];
                    }
                }
            }
            if (isset($unrecognized['unrecognized'])) {
                $error_message = ($unrecognized['error_message'] != null) ? $unrecognized['error_message'] : $error_message;
                foreach ($unrecognized['unrecognized'] as $key=>$value) {
                    $result['unrecognized'][$key]['average_land_unit_price']=$value['average_land_unit_price'];
                    $result['unrecognized'][$key]['estimate_price']=$value['estimate_price'];
                    $totalPrice +=$value['estimate_price'];
                    if ($value['estimate_price'] ==0){
                        $status =0;
                        $unRecognizedLandError[]=$value['land_type_purpose_name'];
                    }
                }
            }
            if (isset($result['building'])) {
                foreach ($result['building'] as $key=>$value) {
                    $result['building'][$key]['average_building_unit_price']=$value['average_building_unit_price'];
                    $result['building'][$key]['estimate_price']=$value['estimate_price'];
                    $totalPrice += $value['estimate_price'];
                }
            }
            if ((isset($result['unrecognized']) && count($unRecognizedLandError) < count($result['unrecognized'])) || (isset($result['recognized']) &&count($recognizedLandError)<count($result['recognized']))){
                if (count($unRecognizedLandError)!=0 && count($recognizedLandError)!=0){
                    $error_message = str_replace('[1]',implode(', ', $unRecognizedLandError),EstimateAssetDefault::ERROR_MESSAGE_COMBO);
                    $error_message = str_replace('[2]',implode(', ', $recognizedLandError),$error_message);
                }elseif (count($unRecognizedLandError)!=0 ){
                    $error_message = str_replace('[1]',implode(', ', $unRecognizedLandError),EstimateAssetDefault::ERROR_MESSAGE_UNRECOGNIZED);
                }elseif (count($recognizedLandError)!=0){
                    $error_message = str_replace('[1]',implode(', ', $recognizedLandError),EstimateAssetDefault::ERROR_MESSAGE_RECOGNIZED);
                }
            }else{
                $error_message = EstimateAssetDefault::ERROR_MESSAGE;
            }


            $result['result'] = [
                'total_price' => $totalPrice,
                'status' => $status,
                'error_message' => $error_message,
            ];
        }

        $jwt = JWTAuth::getToken();
        $user = $this->userRepository->validateToken($jwt);
        $estimateLog = [
            'input' => json_encode($request),
            'output' => json_encode($result),
            'status' => $result['result']['status'],
            'type' => 'CREATE',
            'user' => $user->name ?? null,
        ];
        $logId = $this->estimatePriceLogRepository->createLog($estimateLog);
        $result['id']=$logId;

        return $result;
    }

}
