<?php

namespace App\Console\Commands;

use App\Models\CompareAssetGeneral;
use App\Models\CompareAssetVersion;
use App\Models\TestAssetVerion;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Elasticquent\ElasticquentResultCollection;

class MigrationCompareAssetElasticToDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert-compare-asset-elastic-to-database:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert data Compare Asset Version from Elasticsearch to database';

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
            $page = 0;
            $perPage = 500;
            $totalPage = $this->getTotalPage($perPage);
            printf("total page $totalPage \n");
            while ($page < $totalPage) {
                printf ("Page : $page \n");
                $this->storeCompareVersion($page, $perPage);
                $page ++;
            }
            printf("Hoàn thành");
            DB::commit();
        }catch(Exception $e){
            Log::error($e);
            printf("Error :". $e->getMessage());
            DB::rollback();
        }
    }
    private function getTotalPage($perPage)
    {
        $assetTypeId = [37, 38];
        $select = [
            'id',
        ];
        $array['bool']['must'][] = [
            'match' => [
                'status' => '1'
            ]
        ];
        $array['bool']['must'][] = [
            'terms' => [
                'asset_type_id' => $assetTypeId
            ]
        ];
        $sortBy['id'] = ['order' => 'desc'];

        $datas = CompareAssetVersion::searchByQuery($array, null, $select, 1, null, $sortBy);
        if ($datas->totalHits() && is_array($datas->totalHits())){
            $total = ($datas->totalHits())['value'];
        }
        printf("Tổng $total \n");
        $totalPage = intval($total/$perPage) +1;
        return $totalPage;
    }

    private function storeCompareVersion($page , $limit)
    {
        try{
            $assetTypeId = [37, 38];
            $array['bool']['must'][] = [
                'match' => [
                    'status' => '1'
                ]
            ];
            $array['bool']['must'][] = [
                'terms' => [
                    'asset_type_id' => $assetTypeId
                ]
            ];
            $sortBy['id'] = ['order' => 'desc'];
    
            $datas = CompareAssetVersion::searchByQuery($array, null, null, $limit, $page * $limit, $sortBy);
            if (isset($datas)) {
                foreach ($datas as $data) {
                    if (isset($data)) {
                        $versions = $data->version;
                        $lastVersion = end($versions);
                        $version = $lastVersion['version'];
                        $check = ['asset_general_id' => $data->id, 'version' => $version];
                        $data = array_merge($check , ['asset_general_data' => json_encode($data) ]);
                        $dataArr = new CompareAssetVersion($data);
                        CompareAssetVersion::query()->updateOrCreate($check, $dataArr->attributesToArray());
                    }
                }
            }
        }catch(Exception $e){
            dd($e->getMessage());
        }
    } 
}
