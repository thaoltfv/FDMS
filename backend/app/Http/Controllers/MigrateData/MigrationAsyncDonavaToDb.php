<?php

namespace App\Http\Controllers\MigrateData;

use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\DictionaryRepository;
use App\Contracts\DistrictRepository;
use App\Contracts\DonavaOldEstatesRepository;
use App\Contracts\DonavaOldUserRepository;
use App\Contracts\MigrateStatusRepository;
use App\Contracts\ProvinceRepository;
use App\Contracts\StreetRepository;
use App\Contracts\UserRepository;
use App\Contracts\WardRepository;
use App\Enum\CompareMaterData;
use App\Models\MigrateStatusDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MigrationAsyncDonavaToDb implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private DonavaOldEstatesRepository $donavaOldEstates;
    private DonavaOldUserRepository $donavaOldUserRepository;
    private UserRepository $userRepository;
    private CompareAssetGeneralRepository $compareAssetGeneralRepository;
    private DictionaryRepository $dictionaryRepository;
    private ProvinceRepository $provinceRepository;
    private DistrictRepository $districtRepository;
    private WardRepository $wardRepository;
    private StreetRepository $streetRepository;
    private MigrateStatusRepository $migrateStatusRepository;
    private $perPage;
    private $page;

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
     * @param MigrateStatusRepository $migrateStatusRepository
     * @param $perPage
     * @param $page
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
                                MigrateStatusRepository       $migrateStatusRepository,
                                                              $perPage,
                                                              $page
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
        $this->migrateStatusRepository = $migrateStatusRepository;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    /**
     *
     */
    public function handle()
    {
        Log::info("Migration estates in Donava is start!");
        try {
            $estates = $this->donavaOldEstates->findPaging($this->perPage, $this->page);
            Log::info("Migration projects in Donava is size!" . sizeof($estates));
            $migrateStatusId = $this->migrateStatusRepository->createMigrateStatus([
                'limit' => $this->perPage,
                'page' => $this->page,
                'status' => 0,
                'type' => 4,
                'total_records' => sizeof($estates),
            ]);
            foreach ($estates as $estatedata) {
                $creator = $this->donavaOldUserRepository->findById($estatedata->creator_id);
                $createdBy = $this->userRepository->findUserByEmail($creator->email);
                $estate = $estatedata->toArray();
                $size = str_replace([','], '.', mb_strtolower($estate['size']));
                $size = (explode('x', str_replace(['m', ' '], '', mb_strtolower($size))));
                $status = 3;
                if (isset($size[0]) && (float)$size[0] != 0 && isset($size[1])
                    && (float)$size[1] != 0 && isset($estate['es_value'][0]['market_price']) && $estate['es_value'][0]['market_price'] != 0) {
                    $status = 1;
                }
                $max = $this->findMaxValue($estate['es_land']);

                $propertyDetail = [];
                $convertFeeTotal = 0;
                $totalConstructionArea = 0;
                if ($estate['es_land']) {
                    foreach ($estate['es_land'] as $esLand) {
                        $convertFee = $this->convertFree($max, $esLand);
                        $convertFeeTotal += $convertFee;
                        $totalConstructionArea += $esLand['acreage'];
                        $landTypePurpose = null;
                        $estimationValue = 0;
                        if (isset($esLand['land_type']['name'])) {
                            $landTypePurpose = $this->dictionaryRepository->findByName($esLand['land_type']['name']);
                            $landTypePurpose = $landTypePurpose->id ?? null;
                        }

                        foreach ($estate['es_land_average'] as $esLandAverage) {
                            if ($esLandAverage['land_type'] == $esLand['land_type']['id']) {
                                $estimationValue = $esLandAverage['average_price'];
                            }
                        }
                        $positionTypeId = null;
                        if (isset($esLand['location']) && in_array($esLand['location'], [1, 2, 3, 4])) {
                            $positionTypeId = $this->dictionaryRepository->findByName(CompareMaterData::POSITION_TYPE[$esLand['location']]);
                            $positionTypeId = $positionTypeId->id ?? null;
                        }
                        $propertyDetail[] = [
                            'land_type_purpose' => $landTypePurpose,
                            'estimation_value' => isset($esLand['price']) ? (int)$esLand['price'] : 0,
                            'position_type_id' => $positionTypeId,
                            'total_area' => isset($esLand['acreage']) ? (float)$esLand['acreage'] : 0,
                            'price_land' => (int)$estimationValue,
                            'convert_fee' => $convertFee,
                            'circular_unit_price' => isset($esLand['decision_price']) ? (int)$esLand['decision_price'] : 0,
                            'k_rate' => isset($esLand['discount_percent']) ? (int)$esLand['discount_percent'] : 0,
                        ];
                    }
                }
                $tangibleAssets = [];
                if ($estate['es_build']) {
                    foreach ($estate['es_build'] as $esBuild) {
                        $buildingCategory = null;
                        if (isset($esBuild['rank']) && in_array($esBuild['rank'], [1, 2, 3, 4, 5])) {
                            $buildingCategory = $this->dictionaryRepository->findByName(CompareMaterData::BUILDING_CATEGORY[$esBuild['rank']]);
                            $buildingCategory = $buildingCategory->id ?? null;
                        }
                        $rate = null;
                        if (isset($esBuild['rating']) && in_array($esBuild['rating'], [1, 2, 3, 4])) {
                            $rate = $this->dictionaryRepository->findDictionary('HANG_NHA', $esBuild['rating']);
                            $rate = $rate->id ?? null;
                        }
                        $structure = null;
                        if (isset($esBuild['structure']) && in_array($esBuild['structure'], [1, 2])) {
                            $structure = $this->dictionaryRepository->findDictionary('CAU_TRUC_BIET_THU', CompareMaterData::STRUCTURE[$esBuild['structure']]);
                            $structure = $structure->id ?? null;
                        }
                        $crane = null;
                        if (isset($esBuild['cranes']) && in_array($esBuild['cranes'], [1, 2, 3, 4])) {
                            $crane = $this->dictionaryRepository->findDictionary('CAU_TRUC_NHA_XUONG', $esBuild['cranes']);
                            $crane = $crane->id ?? null;
                        }
                        $aperture = null;
                        if (isset($esBuild['aperture']) && in_array($esBuild['aperture'], [12, 15, 18, 24, 30])) {
                            $aperture = $this->dictionaryRepository->findDictionary('KHAU_DO', $esBuild['aperture']);
                            $aperture = $aperture->id ?? null;
                        }
                        $factoryType = null;
                        if (isset($esBuild['type_factory']) && in_array($esBuild['type_factory'], [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13])) {
                            $factoryType = $this->dictionaryRepository->findDictionary('LOAI_NHA_MAY', CompareMaterData::FACTORY_TYPE[$esBuild['type_factory']]);
                            $factoryType = $factoryType->id ?? null;
                        }

                        $buildingType = null;
                        if (isset($esBuild['type']) && in_array($esBuild['type'], [1, 3, 4])) {
                            $buildingType = $this->dictionaryRepository->findByName(CompareMaterData::BUILDING_TYPE[$esBuild['type']]);
                            $buildingType = $buildingType->id ?? null;
                        }
                        $tangibleAssets[] = [
                            'building_type_id' => $buildingType,
                            'building_category_id' => $buildingCategory,
                            'total_construction_area' => isset($esBuild['acreage_build']) ? (int)$esBuild['acreage_build'] : 0,
                            'floor' => isset($esBuild['floor']) ? (int)$esBuild['floor'] : 0,
                            'gpxd' => null,
                            'remaining_quality' => isset($esBuild['quality_remaining']) ? (int)$esBuild['quality_remaining'] : 0,
                            'estimation_value' => isset($esBuild['price']) ? (int)$esBuild['price'] : 0,
                            'total_construction_base' => isset($esBuild['acreage_floor_build']) ? (int)$esBuild['acreage_floor_build'] : 0,
                            'unit_price_m2' => isset($esBuild['price']) ? (int)$esBuild['price'] : 0,
                            'plot_num' => isset($esBuild['floor']) ? (int)$esBuild['floor'] : 0,
                            'start_using_year' => null,
                            'rate' => $rate,
                            'structure' => $structure,
                            'crane' => $crane,
                            'aperture' => $aperture,
                            'factory_type' => $factoryType,
                        ];
                    }
                }

                $pic = [];
                if ($estate['es_image']) {
                    foreach ($estate['es_image'] as $esImage) {
                        $oldLink = '';
                        if (isset($esImage['image'])) {
                            if (mb_substr($esImage['image'], 0, 1) == '/') {
                                $oldLink = substr($esImage['image'], 1);
                            } else {
                                $oldLink = $esImage['image'];
                            }
                        }
                        $pic[] = [
                            'old_link' => 'https://bando.donava.vn/' . $oldLink,
                            'picture_type' => null,
                        ];
                    }
                }

                $compareTurningTime = [];
                if ($estate['es_roundabout']) {
                    foreach ($estate['es_roundabout'] as $esRoundabout) {
                        switch (isset($esRoundabout['status']) ? mb_strtolower($esRoundabout['status']) : null) {
                            case "nhựa":
                                $materialId = $this->dictionaryRepository->findByName('ĐƯỜNG NHỰA');
                                break;
                            case "bê tông":
                                $materialId = $this->dictionaryRepository->findByName('ĐƯỜNG BÊ TÔNG');
                                break;
                            case "đá":
                                $materialId = $this->dictionaryRepository->findByName('ĐƯỜNG CẤP PHỐI, ĐƯỜNG ĐÁ');
                                break;
                            case "đất":
                                $materialId = $this->dictionaryRepository->findByName('ĐƯỜNG ĐẤT');
                                break;
                            default:
                                $materialId = null;
                        }
                        $materialId = $materialId->id ?? null;


                        $compareTurningTime[] = [
                            'turning' => isset($esRoundabout['index']) ? (int)$esRoundabout['index'] : 0,
                            'description' => isset($esRoundabout['status']) ? (int)$esRoundabout['status'] : " ",
                            'main_road_length' => isset($esRoundabout['width']) ? (int)$esRoundabout['width'] : 0,
                            'material_id' => $materialId,
                            'is_near_main_road' => false,
                            'is_alley_with_connection' => false,
                        ];
                    }
                }

                $comparePropertyDoc = [];
                $comparePropertyDoc[] = [
                    'doc_num' => $estate['map_number'] ? (int)$estate['map_number'] : null,
                    'plot_num' => $estate['parcel_land_number'] ? (int)$estate['parcel_land_number'] : null,
                ];
                $properties = [];
                $properties [] = [
                    'land_no' => $estate['parcel_land_number'] ?? null,
                    'doc_no' => $estate['map_number'] ?? "",
                    'coordinates' => $estate['lat'] . ',' . $estate['lng'] ?? null,
                    'legal_id' => null,
                    'asset_general_land_sum_area' => (int)$estate['acreage'] ?? 0,
                    'front_side' => $estate['position_id'] == 1 ? 1 : 0,
                    'individual_road' => null,
                    'zoning_id' => null,
                    'land_type_id' => $estate['land_type'] ?? null,
                    'asset_general_value_sum_area' => isset($estate['es_land_average'][0]['average_price']) ? (int)$estate['es_land_average'][0]['average_price'] : 0,
                    'front_side_width' => isset($size[1]) ? (float)$size[1] : 0,
                    'insight_width' => isset($size[0]) ? (float)$size[0] : 0,
                    'size_description' => $estate['size'],
                    'land_shape_id' => null,
                    'main_road_length' => null,
                    'material_id' => null,
                    'business_id' => null,
                    'electric_water_id' => null,
                    'social_security_id' => null,
                    'feng_shui_id' => null,
                    'paymen_method_id' => null,
                    'description' => $estate['description'] ?? " ",
                    'compare_property_turning_time' => $compareTurningTime,
                    'property_detail' => $propertyDetail,
                    'compare_property_doc' => $comparePropertyDoc,
                ];

                $blockSpecification = [];
                $roomDetail = [];
                $apartmentId = null;
                if ($estate['es_apartment']) {
                    foreach ($estate['es_apartment'] as $esApartment) {
                        if (isset($esApartment['apartment_id']) && in_array($esApartment['apartment_id'], [3, 4, 5, 6, 7, 8, 9])) {
                            $apartmentId = $esApartment['apartment_id'] - 2;
                        }
                        $blockSpecification [] = [
                            'block_list_id' => null,
                            'built_year' => null,
                            'total_floor' => $esApartment['total_floor'] ?? null,
                            'basement_floor' => $esApartment['basement'] ?? null,
                            'commercial_floor' => $esApartment['commerce_floor'] ?? null,
                            'living_floor' => $esApartment['total_floor'] - $esApartment['commerce_floor'] - $esApartment['basement'],
                            'lift_number' => $esApartment['elevator'] ?? null,
                            'other_utilities' => null,
                        ];
                        $roomDetail [] = [
                            'block_list_id' => null,
                            'furniture_quality_id' => null,
                            'two_sides_room' => null,
                            'area' => (float)$estate['acreage'] ?? 0,
                            'bedroom_num' => $esApartment['bedrooms'] ?? null,
                            'room_num' => $esApartment['code'] ?? null,
                            'floor' => $esApartment['floor'] ?? null,
                            'wc_num' => $esApartment['restrooms'] ?? null,
                            'unit_price' => $esApartment['total_floor'] ?? null,
                        ];
                    }
                }


                $topographic = null;
                if (isset($estate['es_shape']['name'])) {
                    $topographic = $this->dictionaryRepository->findByName($estate['es_shape']['name']);
                    $topographic = $topographic->id ?? null;
                }

                $transactionType = null;
                if (isset($estate['es_trade_type']['name'])) {
                    $transactionType = $this->dictionaryRepository->findByName(CompareMaterData::TRADE_TYPE[$estate['es_trade_type']['id']]);
                    $transactionType = $transactionType->id ?? null;
                }

                $assetType = null;
                if (isset($estate['estates_type'])) {
                    $assetType = $this->dictionaryRepository->findByName($estate['estates_type'] == 1 ? 'ĐẤT CÓ NHÀ' : 'CHUNG CƯ');
                    $assetType = $assetType->id ?? null;
                }

                $fullAddress = $this->getAddress(($estate['province_id'] && $estate['province_id'] != 0) ? $estate['province_id'] : null,
                    ($estate['district_id'] && $estate['district_id'] != 0) ? $estate['district_id'] : null,
                    ($estate['wards_id'] && $estate['wards_id'] != 0) ? $estate['wards_id'] : null,
                    ($estate['street_id'] && $estate['street_id'] != 0) ? $estate['street_id'] : null);
                $insertEstates = [
                    'input_source' => 'DONAVA',
                    'asset_type_id' => $assetType,
                    'status' => $status,
                    'province_id' => ($estate['province_id'] && $estate['province_id'] != 0) ? $estate['province_id'] : null,
                    'district_id' => ($estate['district_id'] && $estate['district_id'] != 0) ? $estate['district_id'] : null,
                    'ward_id' => ($estate['wards_id'] && $estate['wards_id'] != 0) ? $estate['wards_id'] : null,
                    'street_id' => ($estate['street_id'] && $estate['street_id'] != 0) ? $estate['street_id'] : null,
                    'distance_id' => null,
                    'full_address' => $fullAddress ?? $estate['address'],
                    'land_no' => $estate['parcel_land_number'] ?? null,
                    'doc_no' => $estate['map_number'] ?? "",
                    'coordinates' => $estate['lat'] . ',' . $estate['lng'] ?? null,
                    'source_id' => 40,
                    'topographic' => $topographic,
                    'public_date' => ($estate['date_trade'] && $estate['date_trade'] != "0000-00-00") ? date("d-m-Y", strtotime($estate['date_trade'])) : null,
                    'contact_person' => $estate['contact_name'] ?? null,
                    'contact_phone' => $estate['contact_phone'] ?? null,
                    'transaction_type_id' => $transactionType ?? null,
                    'total_area' => (float)$estate['acreage'] ?? 0,
                    'total_amount' => isset($estate['es_value'][0]['market_price']) ? (int)($estate['es_value'][0]['market_price']) : 0,
                    'convert_fee_total' => $convertFeeTotal,
                    'adjust_amount' => isset($estate['es_value'][0]['discount_percent_value']) ? (isset($estate['es_value'][0]['discount_percent']) > 0 ? (int)$estate['es_value'][0]['discount_percent_value'] : 0 - (int)$estate['es_value'][0]['discount_percent_value']) : 0,
                    'adjust_percent' => isset($estate['es_value'][0]['discount_percent']) ? (int)$estate['es_value'][0]['discount_percent'] : 0,
                    'total_order_amount' => isset($estate['es_value'][0]['total_price_orther']) ? (int)$estate['es_value'][0]['total_price_orther'] : 0,
                    'total_land_unit_price' => (isset($estate['es_value'][0]['price_lands'])) ? (int)($estate['es_value'][0]['price_lands']) : 0,
                    'total_area_amount' => isset($estate['es_value'][0]['market_price']) ? (int)($estate['es_value'][0]['market_price']) : 0,
                    'total_estimate_amount' => isset($estate['es_value'][0]['price_estates']) ? (int)$estate['es_value'][0]['price_estates'] : 0,
                    'total_construction_area' => $totalConstructionArea,
                    'total_construction_amount' => isset($estate['es_value'][0]['price_builds']) ? (int)$estate['es_value'][0]['price_builds'] : 0,
                    'total_raw_amount' => ((isset($estate['es_value'][0]['price_estates']) ? (int)$estate['es_value'][0]['price_estates'] : 0) - (isset($estate['es_value'][0]['price_builds']) ? (int)$estate['es_value'][0]['price_builds'] : 0) - (isset($estate['es_value'][0]['total_price_orther']) ? (int)$estate['es_value'][0]['total_price_orther'] : 0)),
                    'average_land_unit_price' => (int)(($estate['acreage'] && $estate['acreage'] != 0) ? (isset($estate['es_value'][0]['market_price']) ? ((int)($estate['es_value'][0]['market_price']) / (float)$estate['acreage']) : 0) : 0),
                    'created_by' => $createdBy->id ?? "",
                    'pic' => $pic,
                    'apartment_id' => $apartmentId,
                    'properties' => $properties,
                    'tangible_assets' => $tangibleAssets,
                    'block_specification' => $blockSpecification,
                    'room_details' => $roomDetail,
                ];
                $this->compareAssetGeneralRepository->saveCompareAssetGeneral($insertEstates);
                MigrateStatusDetails::query()->insert(array(
                    'migrate_status_id' => $migrateStatusId,
                    'asset_id' => $estate['id'],
                    'status' => 1,
                ));

            }
            $this->migrateStatusRepository->updateMigrateStatus($migrateStatusId, ['status' => 1]);
            Log::info('Migration estates in Donava is end!');
        } catch (\Exception $e) {
            Log::error('Migration estates in Donava is error with message  ' . $e);
        }
    }

    public function findMaxValue($data): int
    {
        $max = 0;
        foreach ($data as $value) {
            if ((int)$value['decision_price'] > $max) {
                $max = (int)$value['decision_price'];
            }
        }
        return $max;
    }

    /**
     * @param $max
     * @param $data
     * @return int
     */
    public function convertFree($max, $data): int
    {
        return (int)(($max - (int)$data['decision_price']) * (float)$data['acreage']);
    }

    /**
     * @param null $province_id
     * @param null $district_id
     * @param null $wards_id
     * @param null $street_id
     * @return string
     */
    public function getAddress($province_id = null, $district_id = null, $wards_id = null, $street_id = null): string
    {
        if ($province_id) {
            $province = $this->provinceRepository->findById($province_id);
        }
        if ($district_id) {
            $district = $this->districtRepository->findById($district_id);
        }
        if ($wards_id) {
            $wards = $this->wardRepository->findById($wards_id);
        }
        if ($street_id) {
            $street = $this->streetRepository->findById($street_id);
        }
        return (isset($street->name) ? $street->name . ', ' : '') . (isset($wards->name) ? $wards->name . ', ' : '') . (isset($district->name) ? $district->name . ', ' : '') . ($province->name ?? '');

    }
}
