<?php

namespace App\Http\Controllers\Report;

use App\Contracts\CompareAssetGeneralRepository;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComparisonAssetController extends Controller
{
    protected CompareAssetGeneralRepository $compareAssetGeneralRepository;
    public function __construct(CompareAssetGeneralRepository $compareAssetGeneralRepository){
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
    }

    public function getAssetByProvince(request $request){
        $result =  $this->compareAssetGeneralRepository->getTotalAssetByProvince();
        return $this->respondWithCustomData($result);
    }
    public function getAssetByMonthOfYear(request $request){
        $result =  $this->compareAssetGeneralRepository->getTotalAssetByMonthOfYear();
        return $this->respondWithCustomData($result);
    }
    public function countCompareAssetGeneral(Request $request)
    {
        try{
            $result = $this->compareAssetGeneralRepository->countCompareAssetGeneral();
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } catch(Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }

    }
}
