<?php

namespace App\Repositories;

use App\Contracts\PreCertificateConfigRepository;
use App\Enum\ValueDefault;
use App\Models\PreCertificateConfig;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Enum\ErrorMessage;
use Illuminate\Support\Facades\Log;
class EloquentPreCertificateConfigRepository extends EloquentRepository implements PreCertificateConfigRepository
{
    private string $defaultSort = 'name';


    public function findAll()
    {
        return $this->model->query()
            ->select()
            ->orderByDesc($this->defaultSort)
            ->get();
    }
    public function findByName($name)
    {
        return $this->model->query()
            ->where('name', $name)
            ->get();
    }
   
    public function updateConfig($name,$request)
    {
        return DB::transaction(function () use ($name,$request) {
            try {
                    return $this->model->query()
                        ->where('name', $name)
                        ->update(['config' => json_encode($request)]);
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }
    
}
