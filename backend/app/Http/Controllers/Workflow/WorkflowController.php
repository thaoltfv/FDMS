<?php

namespace App\Http\Controllers\Workflow;

use App\Contracts\AppraiseRepository;
use App\Contracts\CertificateRepository;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\EloquentUserRepository;
use Illuminate\Http\Request;
use JWTAuth;

class WorkflowController extends Controller
{
    private AppraiseRepository $appraiseRepository;
    private CertificateRepository $certificateRepository;

    public function __construct(AppraiseRepository            $appraiseRepository,
                                CertificateRepository         $certificateRepository

    ){
        $this->appraiseRepository = $appraiseRepository;
        $this->certificateRepository = $certificateRepository;
    }

    public function getWorkflow(Request $request){
        $jwt = JWTAuth::getToken();
        $eloquentUserRepository = new EloquentUserRepository(new User());
        $user = $eloquentUserRepository->validateToken($jwt);
        $request['user']=$user;
        $TSTD =$this->appraiseRepository->findAllAppraises($request->toArray());
        $HSTD =$this->certificateRepository->findAllCerificate($request->toArray());
        $TSTD = ['TSTD'=>$TSTD];
        $HSTD = ['HSTD'=>$HSTD];
        $result=array_merge($TSTD,$HSTD);
        // dd($HSTD);
        return $this->respondWithCustomData($result);
    }

    public function getCertificateWorkFlow(Request $request){

        $HSTD =$this->certificateRepository->getCertificateWorkFlow();
        $result = ['HSTD' => $HSTD];
        // dd($HSTD);
        return $this->respondWithCustomData($result);
    }
}
;
