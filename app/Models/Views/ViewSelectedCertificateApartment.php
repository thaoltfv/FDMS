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
        return '';
    }

    public function getResidentialUnitPriceAttribute()
    {
        return '';
    }
    public function getResidentialPriceAttribute()
    {
        return '';
    }

    public function getAgriculturalAreaAttribute()
    {
        return '';
    }

    public function getAgriculturalUnitPriceAttribute()
    {
        return '';
    }
    public function getAgriculturalPriceAttribute()
    {
        return '';
    }

    public function getAgriculturalArea2Attribute()
    {
        return '';
    }
    public function getAgriculturalUnitPrice2Attribute()
    {
        return '';
    }
    public function getAgriculturalPrice2Attribute()
    {
        return '';
    }
    public function getTangibleTypeAttribute()
    {
        return '';
    }
    public function getTangibleAreaAttribute()
    {
        return '';
    }
    public function getTangibleUnitPriceAttribute()
    {
        return '';

    }
    public function getTangibleRemainAttribute()
    {
        return '';

    }
    public function getOtherTangiblePriceAttribute()
    {
        return '';
    }

    public function getFirstTangiblePriceAttribute()
    {
        return '';
    }
}
