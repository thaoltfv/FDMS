<?php

namespace App\Console\Commands;

use App\Models\ApartmentAsset;
use App\Models\Appraise;
use App\Models\RealEstate;
use App\Services\CommonService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Log;

class MigrationFillRealEstates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration-insert-real-estates:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill Real Estates from Appraise and Apartment';

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
        try{
            DB::beginTransaction();         
            $this->insertFromAppraise();
            $maxId = RealEstate::query()->max('id');
            $maxId = intval($maxId) + 1;
            // dd($maxId);
            DB::statement("ALTER SEQUENCE real_estates_id_seq RESTART WITH $maxId");
            $this->insertFromApartmentAsset();
            DB::commit();
            printf("\n Đã hoàn thành");
        }catch(Exception $ex){
            Log::error($ex);
            printf($ex->getMessage());
            DB::rollBack();
        }

    }
    private function insertFromApartmentAsset()
    {
        $select = [
            'id',
            'appraise_asset',
            'coordinates',
            'created_at',
            'updated_at',
            'asset_type_id',
            'status',
            'created_by'
        ];
        $with = [
            'price' => function($query){
                $query->whereIn('slug', ['total_price', 'apartment_area', 'apartment_round_total'])
                    ->select(['id', 'apartment_asset_id', 'slug', 'value']);
            },
        ];
        $datas = ApartmentAsset::query()->with($with)->whereDoesntHave('realEstate')->orderBy('id')->get($select);
        printf('Có ' . count($datas) .' tài sản chung cư cần được thêm.' . "\n");
        if (isset($datas))
        {
            foreach ($datas as $data){
                $total_price = 0;
                $total_area = 0;
                $round_total = 0;
                $round = $data->price->where('slug','apartment_round_total')->first();
                if (isset($round))
                    $round_total = $round->value;
                $price = $data->price->where('slug','total_price')->first();
                if (isset($price))
                    $total_price = CommonService::roundPrice($price->value, $round_total) ;
                $area = $data->price->where('slug','apartment_area')->first();
                if (isset($area))
                    $total_area = $area->value;
                $realEstate = [
                    'asset_type_id' => $data->asset_type_id,
                    'total_price' => $total_price,
                    'total_area' => $total_area,
                    'round_total' => $round_total,
                    'appraise_asset' => $data->appraise_asset,
                    'created_by' => $data->created_by,
                    'coordinates' => $data->coordinates,
                    'front_side' => null,
                    'status' => $data->status,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                ];
                $id = $this->insertRealEstate($realEstate);
                $data->query()->where('id', $data->id)->update(['real_estate_id' => $id , 'updated_at' => $data->updated_at]);
            }
        }
    }

    private function insertFromAppraise()
    {
        $select = [
            'id',
            'appraise_asset',
            'coordinates',
            'created_at',
            'updated_at',
            'asset_type_id',
            'status',
            'created_by'
        ];
        $with = [
            'assetPrice' => function($query){
                $query->whereIn('slug', ['total_asset_price', 'total_asset_area', 'round_appraise_total'])
                    ->select(['id', 'appraise_id', 'slug', 'value']);
            },
            'properties:id,appraise_id,front_side',
        ];
        $datas = Appraise::query()->with($with)->whereDoesntHave('realEstate')->orderBy('id')->get($select);
        printf('Có ' . count($datas) .' tài sản nhà đất cần được thêm' . "\n");
        if (isset($datas))
            foreach ($datas as $data){
                $total_price = 0;
                $total_area = 0;
                $round_total = 0;
                $round = $data->assetPrice->where('slug','round_appraise_total')->first();
                if (isset($round))
                    $round_total = $round->value;
                $price = $data->assetPrice->where('slug','total_asset_price')->first();
                if (isset($price))
                    $total_price = CommonService::roundPrice($price->value, $round_total);
                $area = $data->assetPrice->where('slug','total_asset_area')->first();
                if (isset($area))
                    $total_area = $area->value;
                $frontSide = $data->properties->first();

                $realEstate = [
                    'id' => $data->id,
                    'asset_type_id' => $data->asset_type_id,
                    'total_price' => $total_price,
                    'total_area' => $total_area,
                    'round_total' => $round_total,
                    'appraise_asset' => $data->appraise_asset,
                    'created_by' => $data->created_by,
                    'coordinates' => $data->coordinates,
                    'front_side' => $frontSide->front_side,
                    'status' => $data->status,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                ];
                $realEstateId = $this->insertRealEstate($realEstate);
                $data->query()->where('id', $data->id)->update(['real_estate_id' => $realEstateId, 'updated_at' => $data->updated_at]);
            }
    }
    private function insertRealEstate(array $realEstate)
    {
        $id = RealEstate::query()->insertGetId($realEstate);
        return $id;
    }
}
