<?php

namespace App\Console\Commands;

use App\Contracts\CompareGeneralPicRepository;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Storage;

class MigrationAllImageToS3 extends Command

{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration_all_image_to_s3:cron';

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
        Log::info("Migration images is start!");
            $images = $this->compareGeneralPicRepository->findAll();
            foreach ($images as $image) {
                $client = new Client;
                try {
                    $response = $client->request('GET', $image->link);
                    $status = $response->getStatusCode();
                   if($status == 200){
                       try {
                           $old_image = file_get_contents( $image->link);
                           $path =env('STORAGE_IMAGES') .'/'. 'comparison_assets/';
                           $last_part = substr(strrchr($image->link, "."), 1);
                        //    dd($last_part);
                           $name = $path . Uuid::uuid4()->toString() . '.' .$last_part;
                           Storage::put($name, $old_image);
                           $fileUrl = Storage::url($name);
                           $this->compareGeneralPicRepository->updateGeneralPic($image->id,['old_link'=>$fileUrl]);
                       } catch (\Exception $e) {
                           Log::error('Migration images is error with message  ' . $e);
                       }
                   }
                } catch (\Exception $e) {
                    $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                    Log::error('Migration images is error with message  ' . $e);
                }
            }
            Log::info('Migration images is end!');
    }
}
