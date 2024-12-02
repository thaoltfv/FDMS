<?php

namespace App\Console\Commands;

use App\Contracts\CompareGeneralPicRepository;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Storage;

class MigrationAsyncImageToS3 extends Command

{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration_image_to_s3:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private CompareGeneralPicRepository $compareGeneralPicRepository;

    /**
     * Create a new command instance.
     *
     * @param CompareGeneralPicRepository $compareGeneralPicRepository
     */

    public function __construct(CompareGeneralPicRepository $compareGeneralPicRepository)
    {
        parent::__construct();
        $this->compareGeneralPicRepository = $compareGeneralPicRepository;
    }

    /**
     *
     */

    public function handle()
    {
        Log::info("Migration images in Donava is start!");
            $images = $this->compareGeneralPicRepository->findAll();
            foreach ($images as $image) {
                $client = new Client;
                try {
                    $response = $client->request('GET', $image->old_link);
                    $status = $response->getStatusCode();
                   if($status == 200){
                       try {
                           $response = file_get_contents( $image->old_link);
                           $path = env('DONAVA_PATH') . env('DONAVA_PRICE_WAREHOUS');
                           $name = $path . Uuid::uuid4()->toString() . '.' .'jpg';
                           Storage::disk('spaces')->put($name, $response, 'public');
                           $fileUrl = Storage::disk('spaces')->url($name);
                           $this->compareGeneralPicRepository->updateGeneralPic($image->id,['link'=>$fileUrl]);
                       } catch (\Exception $e) {
                           Log::error('Migration images in Donava is error with message  ' . $e);
                       }
                   }
                } catch (\Exception $e) {
                    $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                    Log::error('Migration images in Donava is error with message  ' . $e);
                }
            }
            Log::info('Migration images in Donava is end!');
    }
}
