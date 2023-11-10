<?php

namespace App\Console\Commands;

use App\Contracts\CompareGeneralPicRepository;
use App\Models\AppraisePic;
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
            //compareGeneralPicRepository
            $images = $this->compareGeneralPicRepository->findImage();
            Log::info("Migration compareGeneralPicRepository is start!");
            if ($images) {
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
                            $this->compareGeneralPicRepository->updateGeneralPic($image->id,['link'=>$fileUrl]);
                        } catch (\Exception $e) {
                            Log::error('Migration compareGeneralPicRepository is error with message  ' . $e);
                        }
                    }
                    } catch (\Exception $e) {
                        $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                        Log::error('Migration compareGeneralPicRepository is error with message  ' . $e);
                    }
                }
            }
            Log::info("Migration compareGeneralPicRepository is end!");
            //appraise_pics
            $query = 'link not ilike ' . "'%fv-trial.s3-ap-southeast-1.amazonaws.com%'";
            $images = AppraisePic::query()->select()
                    ->whereRaw($query)
                    ->whereNull('deleted_at')
                    ->whereNotNull('link')
                    ->get();
            Log::info("Migration appraise_pics is start!");
            if ($images) {
                foreach ($images as $image) {
                    $client = new Client;
                    try {
                        $response = $client->request('GET', $image->link);
                        $status = $response->getStatusCode();
                    if($status == 200){
                        try {
                            $old_image = file_get_contents( $image->link);
                            $check_dic_certi = substr($image->link, strpos($image->link,"certification_assets"), 20);
                            $check_dic_compa = substr($image->link, strpos($image->link,"comparison_assets"), 17);
                            if ($check_dic_certi == 'certification_assets') {
                                $path =env('STORAGE_IMAGES') .'/'. 'certification_assets/';
                            } else if ($check_dic_compa == 'comparison_assets') {
                                $path =env('STORAGE_IMAGES') .'/'. 'comparison_assets/';
                            } else {
                                $path =env('STORAGE_IMAGES') .'/'. 'other/';
                            }
                            
                            $last_part = substr(strrchr($image->link, "."), 1);
                            //    dd($last_part);
                            $name = $path . Uuid::uuid4()->toString() . '.' .$last_part;
                            Storage::put($name, $old_image);
                            $fileUrl = Storage::url($name);
                            AppraisePic::query()->where('id', $image->id)->update(['link' => $fileUrl]);;
                        } catch (\Exception $e) {
                            Log::error('Migration appraise_pics is error with message  ' . $e);
                        }
                    }
                    } catch (\Exception $e) {
                        // $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                        Log::error('Migration appraise_pics is error with message  ' . $e);
                    }
                }
            }
            Log::info("Migration appraise_pics is end!");

        Log::info('Migration images is end!');
    }
}
