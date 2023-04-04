<?php

namespace App\Http\Controllers\MigrateData;

use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\CompareGeneralPicRepository;
use App\Contracts\MigrateStatusRepository;
use App\Models\MigrateStatusDetails;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Storage;

class MigrationAsyncImageToS3 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private CompareAssetGeneralRepository $compareAssetGeneralRepository;
    private CompareGeneralPicRepository $compareGeneralPicRepository;
    private MigrateStatusRepository $migrateStatusRepository;
    private $perPage;
    private $page;
    private $id;

    /**
     * MigrationAsyncImageToS3 constructor.
     * @param CompareGeneralPicRepository $compareGeneralPicRepository
     */
    public function __construct(CompareAssetGeneralRepository $compareAssetGeneralRepository,
                                CompareGeneralPicRepository   $compareGeneralPicRepository, MigrateStatusRepository $migrateStatusRepository, $perPage, $page, $id)
    {
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
        $this->compareGeneralPicRepository = $compareGeneralPicRepository;
        $this->migrateStatusRepository = $migrateStatusRepository;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->id = $id;
    }

    /**
     * @throws GuzzleException
     */
    public function handle()
    {
        Log::info("Migration images in Donava is start!");
        $objects = $this->compareAssetGeneralRepository->findDataPaging($this->perPage, $this->page);
        foreach ($objects as $object) {
            foreach ($object->pic as $image) {
                $client = new Client;
                try {
                    $response = $client->request('GET', $image->old_link);
                    $status = $response->getStatusCode();
                    if ($status == 200) {
                        try {
                            $response = file_get_contents($image->old_link);
                            $path = env('DONAVA_PATH') . env('DONAVA_PRICE_WAREHOUS');
                            $name = $path . Uuid::uuid4()->toString() . '.' . 'jpg';
                            Storage::disk('spaces')->put($name, $response, 'public');
                            $fileUrl = Storage::disk('spaces')->url($name);
                            $this->compareGeneralPicRepository->updateGeneralPic($image->id, ['link' => $fileUrl]);

                        } catch (\Exception $e) {
                            Log::error('Migration images in Donava is error with message  ' . $e);
                        }
                    }
                } catch (\Exception $e) {
                    $this->compareGeneralPicRepository->deleteGeneralPic($image->id);
                }
            }
            MigrateStatusDetails::query()->insert(array(
                'migrate_status_id' => $this->id,
                'asset_id' => $object['id'],
                'status' => 2,
            ));
        }
        $this->migrateStatusRepository->updateMigrateStatus($this->id, ['status' => 3]);
        Log::info('Migration images in Donava is end!');
    }
}
