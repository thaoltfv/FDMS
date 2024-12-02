<?php

namespace App\Console\Commands;

use App\Models\District;
use App\Models\Province;
use App\Models\Street;
use App\Models\Ward;
use DB;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateAddressFasvalueByJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_address_by_json:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Province, districts, wards, Streets, distance';

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
        $json = File::get("database/mocks/donava.min.json");
        $jsonData = json_decode($json, false);
        $datas = $jsonData->Data->Provinces;
        if (!empty($datas)) {
            $this->InsertData($datas);
        }
    }
    private function InsertData($provinces)
    {
        try {
            DB::beginTransaction();
            foreach ($provinces as $province) {
                printf('Province ' . $province->ProvinceName);
                $id = $this->InsertProvince($province->DonavaID, $province->ProvinceName);
                $districts = $province->Districts;
                if (!empty($districts)) {
                    foreach ($districts as $district) {
                        printf("\nDistrict " . $district->DistrictName);
                        $districtId = $this->InsertDistrict($id, $district->DonavaID, $district->DistrictName);
                        $wards = $district->Wards;
                        if (!empty($wards)) {
                            $this->InsertWards($districtId, $wards);
                        }
                        $streets = $district->Streets;
                        if (!empty($streets)) {
                            $this->InsertStreets($districtId, $streets);
                        }
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            printf("\n Lỗi - " . $e->getMessage());
            DB::rollBack();
        }
    }
    private function InsertProvince($id, $name)
    {
        if ($id) {
            Province::query()->where('id', $id)->update(['name' => $name]);
        } else {
            $id = Province::query()->insertGetId(['name' => $name]);
        }
        return $id;
    }
    private function InsertDistrict($provinceId, $id, $name)
    {
        if ($id) {
            District::query()->where(['id' => $id, 'province_id' => $provinceId])->update(['name' => $name]);
        } else {
            $id = District::query()->insertGetId(['name' => $name, 'province_id' => $provinceId]);
        }
        return $id;
    }
    private function InsertWards($districtId, $wards)
    {
        $arr = [];
        $district = District::find($districtId);
        if (isset($district)) {
            foreach ($wards as $ward) {
                printf("\nWard $ward->WardName , id $ward->DonavaID" );
                // dd($ward);
                $wardId = $ward->DonavaID;
                if (!empty($wardId)) {
                    $item = Ward::find($wardId);
                    // dd($item);
                    if (isset($item)) {
                        $item->name = $ward->WardName;
                    }
                    $arr[] = $item;
                } else {
                    $arr[] = new Ward([
                        'name' => $ward->WardName,
                        'province_id' => $district->province_id
                    ]);
                }
            }
            $district->wards()->saveMany($arr);
        }
    }
    private function InsertStreets($districtId, $streets)
    {
        $arr = [];
        $district = District::find($districtId);
        if (isset($district)) {
            foreach ($streets as $street) {
                $streetName = $street->name;
                $streetId = $street->id;
                printf("\nWard $streetName , id $streetId" );
                if (!empty($streetId)) {
                    $item = Street::find($streetId);
                    if (isset($item)) {
                        $item->name = $streetName;
                    }
                    $arr[] = $item;
                } else {
                    $arr[] = new Street([
                        'name' => $streetName,
                        'province_id' => $district->province_id
                    ]);
                }
            }
            $district->streets()->saveMany($arr);
        }
    }
}
