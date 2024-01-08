<?php

namespace App\Http\Controllers;

use App\Enum\ErrorMessage;
use App\Http\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    use ResponseTrait;

    public function getCertificateWithId($id)
    {
        try {
            $dataQuery = Activity::where('subject_type', 'App\Models\Certificate')->where('subject_id',  $id)->with('causer')->orderBy('id', 'desc')->get();
            if(!isset($dataQuery))
            {
                $data = ['message' => ErrorMessage::LOG_ACTIVITY_NOT_FOUND,'exception' => ''];
                return $this->respondWithErrorData($data);
            }
            return $this->respondWithCustomData($dataQuery, 200);
        } catch (Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
    public function getPreCertificateWithId($id)
    {
        try {
            $dataQuery = Activity::where('subject_type', 'App\Models\PreCertificate')->where('subject_id',  $id)->with('causer')->orderBy('id', 'desc')->get();
            if(!isset($dataQuery))
            {
                $data = ['message' => ErrorMessage::LOG_ACTIVITY_NOT_FOUND,'exception' => ''];
                return $this->respondWithErrorData($data);
            }
            return $this->respondWithCustomData($dataQuery, 200);
        } catch (Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function getAppraiseWithId($id)
    {
        try {
            $dataAppraise = Activity::where('subject_type', 'App\Models\Appraise')->where('subject_id',  $id)->with('causer')->orderBy('id', 'desc')->get();
            if(!isset($dataAppraise))
            {
                $data = ['message' => ErrorMessage::LOG_ACTIVITY_NOT_FOUND,'exception' => ''];
                return $this->respondWithErrorData($data);
            }
            return $this->respondWithCustomData($dataAppraise, 200);
        } catch (Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
    public function getMachineCertificateAssetWithId($id)
    {
        try {
            $dataMachine = Activity::where('subject_type', 'App\Models\MachineCertificateAsset')->where('subject_id',  $id)->with('causer')->orderBy('id', 'desc')->get();
            if(!isset($dataMachine))
            {
                $data = ['message' => ErrorMessage::LOG_ACTIVITY_NOT_FOUND,'exception' => ''];
                return $this->respondWithErrorData($data);
            }
            return $this->respondWithCustomData($dataMachine, 200);
        } catch (Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function getVerhicleCertificateAssetWithId($id)
    {
        try {
            $dataVerhicle = Activity::where('subject_type', 'App\Models\VerhicleCertificateAsset')->where('subject_id',  $id)->with('causer')->orderBy('id', 'desc')->get();
            if(!isset($dataVerhicle))
            {
                $data = ['message' => ErrorMessage::LOG_ACTIVITY_NOT_FOUND,'exception' => ''];
                return $this->respondWithErrorData($data);
            }
            return $this->respondWithCustomData($dataVerhicle, 200);
        } catch (Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function getOtherCertificateAssetWithId($id)
    {
        try {
            $dataOther = Activity::where('subject_type', 'App\Models\OtherCertificateAsset')->where('subject_id',  $id)->with('causer')->orderBy('id', 'desc')->get();
            if(!isset($dataOther))
            {
                $data = ['message' => ErrorMessage::LOG_ACTIVITY_NOT_FOUND,'exception' => ''];
                return $this->respondWithErrorData($data);
            }
            return $this->respondWithCustomData($dataOther, 200);
        } catch (Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function getApartmentAssetWithId($id)
    {
        try {
            $data = Activity::where('subject_type', 'App\Models\ApartmentAsset')->where('subject_id',  $id)->with('causer')->orderBy('id', 'desc')->get();
            if(!isset($data))
            {
                $data = ['message' => ErrorMessage::LOG_ACTIVITY_NOT_FOUND,'exception' => ''];
                return $this->respondWithErrorData($data);
            }
            return $this->respondWithCustomData($data, 200);
        } catch (Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function getCompareWithId($id)
    {
        try {
            $dataAppraise = Activity::where('subject_type', 'App\Models\CompareAssetGeneral')->where('subject_id',  $id)->with('causer')->orderBy('id', 'desc')->get();
            if(!isset($dataAppraise))
            {
                $data = ['message' => ErrorMessage::LOG_ACTIVITY_NOT_FOUND,'exception' => ''];
                return $this->respondWithErrorData($data);
            }
            return $this->respondWithCustomData($dataAppraise, 200);
        } catch (Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
}
