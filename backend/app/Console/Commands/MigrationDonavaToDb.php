<?php

namespace App\Console\Commands;

use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\DictionaryRepository;
use App\Contracts\DistrictRepository;
use App\Contracts\DonavaOldEstatesRepository;
use App\Contracts\DonavaOldUserRepository;
use App\Contracts\ProvinceRepository;
use App\Contracts\StreetRepository;
use App\Contracts\UserRepository;
use App\Contracts\WardRepository;
use App\Enum\CompareMaterData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MigrationDonavaToDb extends Command

{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration_donava_to_db:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private DonavaOldEstatesRepository $donavaOldEstates;
    private DonavaOldUserRepository $donavaOldUserRepository;

    private UserRepository $userRepository;
    private CompareAssetGeneralRepository $compareAssetGeneralRepository;
    private DictionaryRepository $dictionaryRepository;
    private ProvinceRepository $provinceRepository;
    private DistrictRepository $districtRepository;
    private WardRepository $wardRepository;
    private StreetRepository $streetRepository;

    /**
     * MigrationDonavaToDb constructor.
     * @param DonavaOldEstatesRepository $donavaOldEstates
     * @param DonavaOldUserRepository $donavaOldUserRepository
     * @param CompareAssetGeneralRepository $compareAssetGeneralRepository
     * @param DictionaryRepository $dictionaryRepository
     * @param UserRepository $userRepository
     * @param ProvinceRepository $provinceRepository
     * @param DistrictRepository $districtRepository
     * @param WardRepository $wardRepository
     * @param StreetRepository $streetRepository
     */

    public function __construct(DonavaOldEstatesRepository $donavaOldEstates,
                                DonavaOldUserRepository $donavaOldUserRepository,
                                CompareAssetGeneralRepository $compareAssetGeneralRepository,
                                DictionaryRepository $dictionaryRepository,
                                UserRepository $userRepository,
                                ProvinceRepository $provinceRepository,
                                DistrictRepository $districtRepository,
                                WardRepository $wardRepository,
                                StreetRepository $streetRepository
    )
    {
        parent::__construct();
        $this->donavaOldEstates = $donavaOldEstates;
        $this->donavaOldUserRepository = $donavaOldUserRepository;
        $this->userRepository = $userRepository;
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
        $this->dictionaryRepository = $dictionaryRepository;
        $this->provinceRepository = $provinceRepository;
        $this->districtRepository = $districtRepository;
        $this->wardRepository = $wardRepository;
        $this->streetRepository = $streetRepository;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function handle()
    {
        Log::info("Migration estates in Donava is start!");
        try {
            $estates = $this->donavaOldEstates->findAll();
            Log::info("Migration projects in Donava is size!" . sizeof($estates));
            foreach ($estates as $estatedata) {

                $creator = $this->donavaOldUserRepository->findById($estatedata->creator_id);
                $createdBy = $this->userRepository->findUserByEmail($creator->email);
                $estate = $estatedata->toArray();
                $size = str_replace([','], '.', mb_strtolower($estate['size']));
                $size = (explode('x', str_replace(['m', ' '], '', mb_strtolower($size))));

                if (isset($size[0]) && (float)$size[0] != 0 && isset($size[1]) && (float)$size[1] != 0) {
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
                            if (isset($esLand['land_type']['name'])) {
                                $landTypePurpose = $this->dictionaryRepository->findByName($esLand['land_type']['name']);
                                $landTypePurpose = $landTypePurpose->id ?? null;
                            }

                            $propertyDetail[] = [
                                'land_type_purpose' => $landTypePurpose,
                                'estimation_value' => isset($esLand['price']) ? (int)$esLand['price'] : 0,
                                'position_type_id' => null,
                                'total_area' => isset($esLand['acreage']) ? (float)$esLand['acreage'] : 0,
                                'price_land' => isset($esLand['decision_price']) ? (int)$esLand['decision_price'] : 0,
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
                            if (isset($esBuild['rank'])&&in_array($esBuild['rank'],[1,2,3,4])) {
                                $buildingCategory = $this->dictionaryRepository->findByName(CompareMaterData::BUILDING_CATEGORY[$esBuild['rank']]);
                                $buildingCategory = $buildingCategory->id ?? null;
                            }

                            $buildingType = null;
                            if (isset($esBuild['type'])&&in_array($esBuild['type'],[1,3,4])) {
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
                                'unit_price_m2' => 0,
                                'plot_num' => null,
                                'start_using_year' => null,
                            ];
                        }
                    }

                    $pic = [];
                    if ($estate['es_image']) {
                        foreach ($estate['es_image'] as $esImage) {
                            $oldLink = '';
                            if(isset($esImage['image'])){
                                if (mb_substr($esImage['image'], 0, 1) =='/'){
                                    $oldLink= substr($esImage['image'], 1);
                                }else{
                                    $oldLink=$esImage['image'];
                                }
                            }
                            $pic[] = [
                                'old_link' => 'https://bando.donava.vn/' .$oldLink,
                                'picture_type' => null,
                            ];
                        }
                    }

                    $compareTurningTime = [];
                    if ($estate['es_roundabout']) {
                        foreach ($estate['es_roundabout'] as $esRoundabout) {
                            $compareTurningTime[] = [
                                'turning' => isset($esRoundabout['index']) ? (int)$esRoundabout['index'] : 0,
                                'description' => isset($esRoundabout['status']) ? (int)$esRoundabout['status'] : " ",
                                'main_road_length' => isset($esRoundabout['width']) ? (int)$esRoundabout['width'] : 0,
                                'material_id' => null,
                                'is_near_main_road' => false,
                                'is_alley_with_connection' => false,
                            ];
                        }
                    }

                    $comparePropertyDoc = [];
                    $comparePropertyDoc[]=[
                        'doc_num'=> $estate['map_number'] ?(int)$estate['map_number'] :null,
                        'plot_num'=> $estate['parcel_land_number'] ?(int)$estate['parcel_land_number']: null,
                    ];
                    $properties = [];
                    $properties [] = [
                        'land_no' => $estate['parcel_land_number'] ?? null,
                        'doc_no' => $estate['map_number'] ?? "",
                        'coordinates' => $estate['lat'] . ',' . $estate['lng'] ?? null,
                        'legal_id' => null,
                        'asset_general_land_sum_area' => (int)$estate['acreage'] ?? 0,
                        'front_side' => $estate['position_id'] ?? 0,
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
                    $insertEstates = [];
                    $insertEstates = [
                        'input_source' => 'DONAVA',
                        'asset_type_id' => $assetType,
                        'status' => 1,
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
                        'adjust_amount' => isset($estate['es_value'][0]['discount_percent_value']) ? (int)$estate['es_value'][0]['discount_percent_value'] : 0,
                        'adjust_percent' => isset($estate['es_value'][0]['discount_percent']) ? (int)$estate['es_value'][0]['discount_percent'] : 0,
                        'total_order_amount' => isset($estate['es_value'][0]['total_price_orther']) ? (int)$estate['es_value'][0]['total_price_orther'] : 0,
                        'total_land_unit_price' => 0,
                        'total_area_amount' => isset($estate['es_value'][0]['market_price']) ? (int)($estate['es_value'][0]['market_price']) : 0,
                        'total_estimate_amount' => isset($estate['es_value'][0]['price_estates']) ? (int)$estate['es_value'][0]['price_estates'] : 0,
                        'total_construction_area' => $totalConstructionArea,
                        'total_construction_amount' => isset($estate['es_value'][0]['price_builds']) ? (int)$estate['es_value'][0]['price_builds'] : 0,
                        'total_raw_amount' => ((isset($estate['es_value'][0]['price_estates']) ? (int)$estate['es_value'][0]['price_estates'] : 0) - (isset($estate['es_value'][0]['price_builds']) ? (int)$estate['es_value'][0]['price_builds'] : 0) - (isset($estate['es_value'][0]['total_price_orther']) ? (int)$estate['es_value'][0]['total_price_orther'] : 0)),
                        'average_land_unit_price' => (int)(($estate['acreage'] && $estate['acreage'] != 0) ? (isset($estate['es_value'][0]['market_price']) ? ((int)($estate['es_value'][0]['market_price']) / (float)$estate['acreage']) : 0) : 0),
                        'created_by' => $createdBy->id ?? "",
                        'pic' => $pic,
                        'properties' => $properties,
                        'tangible_assets' => $tangibleAssets,
                    ];
                    $this->compareAssetGeneralRepository->saveCompareAssetGeneral($insertEstates);
                }

                Log::info("Migration projects in Donava insert data!");
                Log::info('Migration estates in Donava is end!');
            }

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
    public function getAddress($province_id = null, $district_id = null, $wards_id = null, $street_id = null)
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
