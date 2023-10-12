<?php

namespace App\Models\Views;

use App\Enum\ValueDefault;
use App\Models\Certificate;
use App\Models\CertificateAssetAppraisalMethods;
use App\Models\CertificateAssetOtherAsset;
use App\Models\CertificateAssetPrice;
use App\Models\CertificateAssetPropertyDetail;
use App\Models\CertificateAssetTangibleAsset;
use App\Models\Dictionary;
use App\Services\CommonService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Province
 * @package App\Models
 */
class ViewSelectedCertificateApartment extends Model
{
    protected $casts = [
        'id' => 'integer',
    ];

    const  RESIDENTIAL_PURPOSE = ['ODT', 'ONT'];

    public function getResidentialAreaAttribute()
    {
        if (isset ($this->propertyDetail)) {
            foreach ($this->propertyDetail as $detail) {
                $acronym = $this->getPurposeAcronym($detail->land_type_purpose_id);
                if( in_array($acronym, self::RESIDENTIAL_PURPOSE)) {
                    $areaModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_area')->first();
                    if (isset($areaModel))
                        return floatval($areaModel->value);
                }
            }
        }
        return 0;
    }

}
