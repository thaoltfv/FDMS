<?php

namespace App\Console\Commands;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\CompareAssetGeneral;
use App\Models\Dictionary;
use App\Models\District;
use App\Models\Floor;
use App\Models\Project;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportApartmentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_apartment_data:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // DB::transaction(function(){
        DB::table('projects')->truncate();
        DB::table('blocks')->truncate();
        DB::table('floors')->truncate();
        DB::table('apartments')->truncate();

        $json = File::get("database/mocks/projects_lv3_minimal.json");
        $datas = json_decode($json, false);
        if (isset($datas)) {
            $errorData = [];
            $stt = 0;
            $total = count($datas);
            $rankData = [];
            $dic = Dictionary::query()->where(['type' => 'HANG_CHUNG_CU'])->get()->toArray();

            $dicRankAcronym = array_reduce($dic, function ($result, $rank) {
                $result[mb_strtolower($rank['description'])] = $rank['acronym'];
                return $result;
            });
            $dicRankID = array_reduce($dic, function ($result, $rank) {
                $result[mb_strtolower($rank['description'])] = $rank['id'];
                return $result;
            });
            $this->output->progressStart(count($datas));

            foreach ($datas as $data) {
                $project = [];
                $insights = $data->insights;
                $buildings = $data->buildings;
                $stt++;
                if (!isset($insights->province) || !isset($insights->district) || !isset($insights->ward)) {
                    $errorData[$data->name]['name'] = $data->name;
                    $errorData[$data->name]['province'] = $insights->province ?? null;
                    $errorData[$data->name]['district'] = $insights->district ?? null;
                    $errorData[$data->name]['ward'] = $insights->ward ?? null;
                    $errorData[$data->name]['province_id'] = null;
                    $errorData[$data->name]['district_id'] = null;
                    $errorData[$data->name]['ward_id'] = null;
                    continue;
                }
                $strProvince = ltrim(substr($insights->province, 3, strlen($insights->province) - 3));
                $strDistrict = ltrim(substr($insights->district, 3, strlen($insights->district) - 3));
                $strWard = ltrim(substr($insights->ward, 3, strlen($insights->ward) - 3));
                $province = Province::query()->where('name',  'ilike',  '%' . $strProvince)->first(['id', 'name']);
                if (!isset($province)) {
                    $errorData[$data->name]['name'] = $data->name;
                    $errorData[$data->name]['province'] = $insights->province;
                    $errorData[$data->name]['district'] = $insights->district;
                    $errorData[$data->name]['ward'] = $insights->ward;
                    $errorData[$data->name]['province_id'] = null;
                    $errorData[$data->name]['district_id'] = null;
                    $errorData[$data->name]['ward_id'] = null;
                    continue;
                }
                $strDistrict = (intval($strDistrict) != 0) ? intval($strDistrict) : $strDistrict;
                $strWard = (intval($strWard) != 0) ? intval($strWard) : $strWard;
                $province_id = $province->id;
                $district = District::query()->where('name', 'ilike', '%' . $strDistrict)->where('province_id', $province->id)->first(['id', 'name']);
                $district_id = isset($district) ? $district->id : null;
                $ward = Ward::query()->where('name', 'ilike', '%' . $strWard)->where(['province_id' => $province->id, 'district_id' => $district_id])->first(['id', 'name']);
                $ward_id = isset($ward) ? $ward->id : null;

                $total_property = array_sum(array_column($buildings, 'nb_apartment')); //$insights->total_property??0;
                if (!isset($insights->lat_cdnt) || !isset($insights->long_cdnt))
                    $location = null;
                else
                    $location = $insights->lat_cdnt . ', ' . $insights->long_cdnt;

                ////rank
                $strRank = mb_strtolower($insights->rank ?? '');
                $rankData = explode(',', $strRank);
                $arrRank = [];
                $rankId = 999999;

                foreach ($rankData as $rank) {
                    if (array_key_exists($rank, $dicRankAcronym))
                        $arrRank[] = $dicRankAcronym[$rank];
                    if (array_key_exists($rank, $dicRankID))
                        if ($dicRankID[$rank] < $rankId) {
                            $rankId = $dicRankID[$rank];
                        }
                }
                $rankId = $rankId == 999999 ? null : $rankId;

                //swim dens
                $swim = explode('/', $insights->swim_dens ?? '0');
                $nb_swim = 0;
                if ($swim[0] != 0) {
                    $nb_swim = intval(substr($swim[0], 0, strlen($swim[0]) - strlen('bể bơi')));
                }
                if ($nb_swim > 0) {
                    $utilities = ['ho_boi'];
                } else {
                    $utilities = null;
                }
                $total_blocks = $insights->nb_block;
                $project['name'] = $data->name;
                $project['province_id'] = $province_id;
                $project['district_id'] = $district_id;
                $project['ward_id'] = $ward_id;
                $project['total_apartments'] = $total_property;
                $project['coordinates'] = $location;
                $project['rank'] = $arrRank;
                $project['total_blocks'] = $total_blocks;
                $project['basement'] = $insights->number_basement ?? null;
                $project['elevator'] = $insights->number_ele ?? null;
                $project['nb_swim_dens'] = $nb_swim ?? 0;
                $project['utilities'] = $utilities ?? null;
                $project['handover_year'] = $insights->handover_date_from ?? null;
                $project['status'] = true;
                $handover_year = isset($insights->handover_date_from) ? substr($insights->handover_date_from, 0, 4) : null;
                $project['handover_year'] = $handover_year;
                $projectData = new Project($project);
                $projectCreated = Project::query()->updateOrcreate(['name' => $data->name, 'province_id' => $province_id, 'district_id' => $district_id, 'ward_id' => $ward_id], $projectData->attributesToArray());
                $projectId = $projectCreated->id;
                foreach ($buildings as $building) {
                    $nb_apar_per_floor = $building->nb_apar_per_floor ?? 0;
                    $nb_floor = $building->nb_floor ?? 0;
                    $block = [];
                    $block['name'] = $building->name;
                    $block['project_id'] = $projectId;
                    $block['status'] = true;
                    $floors = $building->floors;
                    $lastFloor = intval($floors[count($floors) - 1]->name) ?? 0;
                    $first_floor = intval($floors[0]->name) ?? 0;
                    $block['total_floors'] = $lastFloor;
                    $block['first_floor'] = $first_floor;
                    $block['last_floor'] = $lastFloor;
                    $block['apartments_per_floor'] = $nb_apar_per_floor ?? 0;
                    $block['rank_id'] = $rankId;
                    $block['nb_basement'] =  $insights->number_basement[0] ?? 0;
                    $block['nb_elevator'] =  $insights->number_ele[0] ?? 0;
                    $block['nb_living_floor'] = $nb_floor ?? 0;
                    $block['total_apartments'] = $building->nb_apartment ?? 0;
                    $block['handover_year'] = $handover_year;
                    $blockData = new Block($block);
                    $blockCreated = Block::query()->updateOrcreate(['name' => $building->name, 'project_id' => $projectId], $blockData->attributesToArray());
                    $blockId = $blockCreated->id;
                    foreach ($floors as $floor) {
                        $floorData = [];
                        $floorName = $floor->name;
                        $floorData['name'] = $floorName;
                        $floorData['block_id'] = $blockId;
                        $floorData['status'] = true;
                        $floorData1 = new Floor($floorData);
                        Floor::query()->updateOrcreate(['name' => $floorName, 'block_id' => $blockId], $floorData1->attributesToArray());
                        // $floorId = $floorCreated->id;
                        // $apartment = [];
                        // for ($i = 1; $i <= $nb_apar_per_floor; $i++) {
                        //     $apartment = [];
                        //     $name =  $floorName . '.' . substr('0' . $i, strlen($i) - 1, 2);
                        //     $apartment['name'] = $name ?? '';
                        //     $apartment['floor_id'] = $floorId;
                        //     $apartment['status'] = true;
                        //     $apartmentData = new Apartment($apartment);
                        //     $apartmentCreated = Apartment::query()->updateOrcreate(['name' => $name, 'floor_id' => $floorId], $apartmentData->attributesToArray());
                        // }
                    }
                }
                $this->output->progressAdvance();
            }
            if (count($errorData) > 0) {
                Storage::disk('public')->put('errorList.json', json_encode($errorData, JSON_UNESCAPED_UNICODE));
            }
        }
        $this->output->progressFinish();
        // });
    }
}
