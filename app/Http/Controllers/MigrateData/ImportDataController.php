<?php

namespace App\Http\Controllers\MigrateData;

use App\Contracts\MigrateStatusRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ImportDataController extends Controller
{
    private MigrateStatusRepository $migrateStatusRepository;

    public function __construct(MigrateStatusRepository       $migrateStatusRepository)
    {
        $this->migrateStatusRepository = $migrateStatusRepository;
    }

    public function asyncImportStreet(): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::PROPERTIES_SCREEN)) {
                $index = (int)request()->get('index');
                $size = (int)request()->get('size');
                ImportAsyncStreetData::dispatchAfterResponse($this->migrateStatusRepository,$index, $size);
                return $this->respondWithCustomData(['message' => "Import Street Start!"]);
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function asyncImportDistance(): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::PROPERTIES_SCREEN)) {
                ImportAsyncDistanceData::dispatchAfterResponse($this->migrateStatusRepository);
                return $this->respondWithCustomData(['message' => "Import Distance Start!"]);
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function asyncImportUnitPrice(): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::PROPERTIES_SCREEN)) {
                ImportAsyncUnitPriceData::dispatchAfterResponse($this->migrateStatusRepository);
                return $this->respondWithCustomData(['message' => "Import Unit Price Start!"]);
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

}
