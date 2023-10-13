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
        return 1;
    }

    public function getResidentialUnitPriceAttribute()
    {
        return 2;
    }
    public function getResidentialPriceAttribute()
    {
        return 3;
    }

    public function getAgriculturalAreaAttribute()
    {
        return 4;
    }

    public function getAgriculturalUnitPriceAttribute()
    {
        return 5;
    }
    public function getAgriculturalPriceAttribute()
    {
        return 6;
    }

    public function getAgriculturalArea2Attribute()
    {
        return 7;
    }
    public function getAgriculturalUnitPrice2Attribute()
    {
        return 8;
    }
    public function getAgriculturalPrice2Attribute()
    {
        return 9;
    }
    public function getTangibleTypeAttribute()
    {
        return 10;
    }
    public function getTangibleAreaAttribute()
    {
        return 11;
    }
    public function getTangibleUnitPriceAttribute()
    {
        return 12;

    }
    public function getTangibleRemainAttribute()
    {
        return 13;

    }
    public function getOtherTangiblePriceAttribute()
    {
        return 14;
    }

    public function getFirstTangiblePriceAttribute()
    {
        return 15;
    }
}
