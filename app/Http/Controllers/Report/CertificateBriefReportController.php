<?php

namespace App\Http\Controllers\Report;

use App\Contracts\ViewCertificateBrieftRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CertificateBriefReportController extends Controller
{
    protected ViewCertificateBrieftRepository $viewBrief;
    public function __construct(ViewCertificateBrieftRepository $viewBrief)
    {
        $this->viewBrief = $viewBrief;
    }

    public function countBrieftStatus(Request $request)
    {
        $result = $this->viewBrief->countBrieftStatus();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function countBrieftStatusExpired(Request $request)
    {
        $result = $this->viewBrief->countBrieftStatusExpired();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }

    public function countBrieftStatusByMonth(Request $request)
    {
        $result = $this->viewBrief->countBrieftStatusByMonth();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }

    public function countBrieftStatusByAppraiser(Request $request)
    {
        $result = $this->viewBrief->countBrieftStatusByAppraiser();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function countBrieftBacklog(Request $request)
    {
        $result = $this->viewBrief->countBrieftBacklog();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function countBriefInProcessing(Request $request)
    {
        $result = $this->viewBrief->countBriefInProcessing();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function countBriefInProcessingPreCertificate(Request $request)
    {
        $result = $this->viewBrief->countBriefInProcessingPreCertificate();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function countBriefInProcessingCertificate(Request $request)
    {
        $result = $this->viewBrief->countBriefInProcessingCertificate();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function countBriefFinishByQuarters(Request $request)
    {
        $result = $this->viewBrief->countBriefFinishByQuarters();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function countBriefCancelByQuarters(Request $request)
    {
        $result = $this->viewBrief->countBriefCancelByQuarters();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function totalBriefBranchRevenue(Request $request)
    {
        $result = $this->viewBrief->totalBriefBranchRevenue();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function totalBriefBranchDebt(Request $request)
    {
        $result = $this->viewBrief->totalBriefBranchDebt();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
    public function countBriefFinishByMonth()
    {
        $result = $this->viewBrief->countBriefFinishByMonth();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }

    public function countBriefCancelByMonth(Request $request)
    {
        $result = $this->viewBrief->countBriefCancelByMonth();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
}
