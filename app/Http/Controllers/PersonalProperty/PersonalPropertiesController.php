<?php

namespace App\Http\Controllers\PersonalProperty;

use App\Contracts\PersonalPropertiesRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersonalPropertiesController extends Controller
{
    private PersonalPropertiesRepository $personalProperties;

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
}
