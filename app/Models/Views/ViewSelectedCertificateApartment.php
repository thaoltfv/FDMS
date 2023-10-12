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

    public function getResidentialAreaAttribute()
    {
        return 0;
    }

    public function getResidentialUnitPriceAttribute()
    {
        return 0;
    }
    public function getResidentialPriceAttribute()
    {
        return 0;
    }

    public function getAgriculturalAreaAttribute()
    {
        return 0;
    }

    public function getAgriculturalUnitPriceAttribute()
    {
        return 0;
    }
    public function getAgriculturalPriceAttribute()
    {
        return 0;
    }

    public function getAgriculturalArea2Attribute()
    {
        return 0;
    }
    public function getAgriculturalUnitPrice2Attribute()
    {
        return 0;
    }
    public function getAgriculturalPrice2Attribute()
    {
        return 0;
    }
    public function getTangibleTypeAttribute()
    {
        return '';
    }
    public function getTangibleAreaAttribute()
    {
        return 0;
    }
    public function getTangibleUnitPriceAttribute()
    {
        return 0;

    }
    public function getTangibleRemainAttribute()
    {
        return 0;

    }
    public function getOtherTangiblePriceAttribute()
    {
        return 0;
    }

    public function getFirstTangiblePriceAttribute()
    {
        return 0;
    }
}
