<?php

namespace App\Console\Commands;

use App\Contracts\CompareGeneralPicRepository;
use App\Models\AppraisePic;
use App\Models\ApartmentAssetPic;
use App\Models\CertificateApartmentPic;
use App\Models\CertificateAssetPic;
use App\Models\ComparePropertyPic;
use App\Models\CompareTangiblePic;
use App\Models\CustomerPic;
use App\Models\CompareOtherPic;
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

            //apartment_asset_pics
            $query = 'link not ilike ' . "'%fv-trial.s3-ap-southeast-1.amazonaws.com%'";
            $images = ApartmentAssetPic::query()->select()
                    ->whereRaw($query)
                    ->whereNull('deleted_at')
                    ->whereNotNull('link')
                    ->get();
            Log::info("Migration apartment_asset_pics is start!");
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
                            ApartmentAssetPic::query()->where('id', $image->id)->update(['link' => $fileUrl]);;
                        } catch (\Exception $e) {
                            Log::error('Migration apartment_asset_pics is error with message  ' . $e);
                        }
                    }
                    } catch (\Exception $e) {
                        // $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                        Log::error('Migration apartment_asset_pics is error with message  ' . $e);
                    }
                }
            }
            Log::info("Migration apartment_asset_pics is end!");

            //certificate_apartment_pics
            $query = 'link not ilike ' . "'%fv-trial.s3-ap-southeast-1.amazonaws.com%'";
            $images = CertificateApartmentPic::query()->select()
                    ->whereRaw($query)
                    ->whereNull('deleted_at')
                    ->whereNotNull('link')
                    ->get();
            Log::info("Migration certificate_apartment_pics is start!");
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
                            CertificateApartmentPic::query()->where('id', $image->id)->update(['link' => $fileUrl]);;
                        } catch (\Exception $e) {
                            Log::error('Migration certificate_apartment_pics is error with message  ' . $e);
                        }
                    }
                    } catch (\Exception $e) {
                        // $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                        Log::error('Migration certificate_apartment_pics is error with message  ' . $e);
                    }
                }
            }
            Log::info("Migration certificate_apartment_pics is end!");

            //CertificateAssetPic
            $query = 'link not ilike ' . "'%fv-trial.s3-ap-southeast-1.amazonaws.com%'";
            $images = CertificateAssetPic::query()->select()
                    ->whereRaw($query)
                    ->whereNull('deleted_at')
                    ->whereNotNull('link')
                    ->get();
            Log::info("Migration CertificateAssetPic is start!");
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
                            CertificateAssetPic::query()->where('id', $image->id)->update(['link' => $fileUrl]);;
                        } catch (\Exception $e) {
                            Log::error('Migration CertificateAssetPic is error with message  ' . $e);
                        }
                    }
                    } catch (\Exception $e) {
                        // $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                        Log::error('Migration CertificateAssetPic is error with message  ' . $e);
                    }
                }
            }
            Log::info("Migration CertificateAssetPic is end!");

            //ComparePropertyPic
            $query = 'link not ilike ' . "'%fv-trial.s3-ap-southeast-1.amazonaws.com%'";
            $images = ComparePropertyPic::query()->select()
                    ->whereRaw($query)
                    ->whereNull('deleted_at')
                    ->whereNotNull('link')
                    ->get();
            Log::info("Migration ComparePropertyPic is start!");
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
                            ComparePropertyPic::query()->where('id', $image->id)->update(['link' => $fileUrl]);;
                        } catch (\Exception $e) {
                            Log::error('Migration ComparePropertyPic is error with message  ' . $e);
                        }
                    }
                    } catch (\Exception $e) {
                        // $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                        Log::error('Migration ComparePropertyPic is error with message  ' . $e);
                    }
                }
            }
            Log::info("Migration ComparePropertyPic is end!");

            //CompareTangiblePic
            $query = 'link not ilike ' . "'%fv-trial.s3-ap-southeast-1.amazonaws.com%'";
            $images = CompareTangiblePic::query()->select()
                    ->whereRaw($query)
                    ->whereNull('deleted_at')
                    ->whereNotNull('link')
                    ->get();
            Log::info("Migration CompareTangiblePic is start!");
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
                            CompareTangiblePic::query()->where('id', $image->id)->update(['link' => $fileUrl]);;
                        } catch (\Exception $e) {
                            Log::error('Migration CompareTangiblePic is error with message  ' . $e);
                        }
                    }
                    } catch (\Exception $e) {
                        // $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                        Log::error('Migration CompareTangiblePic is error with message  ' . $e);
                    }
                }
            }
            Log::info("Migration CompareTangiblePic is end!");

            //CustomerPic
            $query = 'link not ilike ' . "'%fv-trial.s3-ap-southeast-1.amazonaws.com%'";
            $images = CustomerPic::query()->select()
                    ->whereRaw($query)
                    ->whereNull('deleted_at')
                    ->whereNotNull('link')
                    ->get();
            Log::info("Migration CustomerPic is start!");
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
                            CustomerPic::query()->where('id', $image->id)->update(['link' => $fileUrl]);;
                        } catch (\Exception $e) {
                            Log::error('Migration CustomerPic is error with message  ' . $e);
                        }
                    }
                    } catch (\Exception $e) {
                        // $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                        Log::error('Migration CustomerPic is error with message  ' . $e);
                    }
                }
            }
            Log::info("Migration CustomerPic is end!");

            //CompareOtherPic
            $query = 'link not ilike ' . "'%fv-trial.s3-ap-southeast-1.amazonaws.com%'";
            $images = CompareOtherPic::query()->select()
                    ->whereRaw($query)
                    ->whereNull('deleted_at')
                    ->whereNotNull('link')
                    ->get();
            Log::info("Migration CompareOtherPic is start!");
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
                            CompareOtherPic::query()->where('id', $image->id)->update(['link' => $fileUrl]);;
                        } catch (\Exception $e) {
                            Log::error('Migration CompareOtherPic is error with message  ' . $e);
                        }
                    }
                    } catch (\Exception $e) {
                        // $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                        Log::error('Migration CompareOtherPic is error with message  ' . $e);
                    }
                }
            }
            Log::info("Migration CompareOtherPic is end!");

        Log::info('Migration images is end!');
    }
}
