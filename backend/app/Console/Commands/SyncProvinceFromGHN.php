<?php

namespace App\Console\Commands;

use App\Contracts\ProvinceRepository;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Log;

class SyncProvinceFromGHN extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:syncProvinceFromGHN';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Province from Giao Hang Nhanh';
    protected ProvinceRepository $provinceRepository;

    /**
     * Create a new command instance.
     *
     * @param ProvinceRepository $provinceRepository
     */
    public function __construct(ProvinceRepository $provinceRepository)
    {
        parent::__construct();
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        Log::info("Sync Province from Giao Hang Nhanh start!");
        DB::beginTransaction();
        try {
            $client = new Client([
                'headers' =>[
                'Content-Type' => 'application/json',
                'Token' => config('services.ghn.token')
                    ]
            ]);
            $response = $client->get(config('services.ghn.baseUrl').'/shiip/public-api/master-data/province');

            if($response->getStatusCode() == 200) {
                $data = $this->provinceRepository->findProvince();
                dd($this->provinceRepository->deleteAll());
                $this->provinceRepository->deleteAll();
                $provinces =  json_decode($response->getBody()->getContents())->data;

                foreach ($provinces as $province){


                }
            }
            Log::info("Sync Province from Giao Hang Nhanh success!");
        }catch(GuzzleException $e)
        {
            Log::error("Sync Province from Giao Hang Nhanh fail!");
        }
    }
}
