<?php

namespace App\Http\Controllers\Report;

use App\Contracts\AppraiseRepository;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CertificateAssetReportController extends Controller
{
    protected AppraiseRepository $appraise;
    public function __construct(AppraiseRepository $appraise){
        $this->appraise = $appraise;
    }

    public function countAppraiseAsset(Request $request)
    {
        try {
            $result = $this->appraise->countAppraiseAsset();
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
