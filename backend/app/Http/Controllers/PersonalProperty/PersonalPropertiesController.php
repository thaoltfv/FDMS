<?php

namespace App\Http\Controllers\PersonalProperty;

use App\Contracts\PersonalPropertiesRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CommonService;
use App\Enum\ErrorMessage;
use App\Services\Excel\ExportCertificatePersonalProperty;

class PersonalPropertiesController extends Controller
{
    private PersonalPropertiesRepository $personalProperties;

    private array $permissionExport =['EXPORT_CERTIFICATE_ASSET'];

    public function __construct(PersonalPropertiesRepository $personalProperties)
    {
        $this->personalProperties = $personalProperties;
    }
    public function findPaging()
    {
        $result =  $this->personalProperties->findPaging();
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData($result);
    }

    public function getDataByIdAssetType(Request $request)
    {
        $result =  $this->personalProperties->findOneByIdAssetType();
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData($result);
    }

    public function exportPersonalProperty(Request $request)
    {
        if(! CommonService::checkUserPermission($this->permissionExport)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::PERSONAL_CHECK_EXPORT ,'exception' =>''], 403);
        }
        $result = $this->personalProperties->exportPersonalProperty();
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData((new ExportCertificatePersonalProperty())->exportAsset($result));
    }
}
