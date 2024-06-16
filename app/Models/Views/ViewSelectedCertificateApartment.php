<?php

namespace App\Models\Views;

use App\Enum\ValueDefault;
use App\Models\Certificate;
use App\Models\CertificateApartmentPrice;
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
    protected $appends = [
        'status_text',
    ];

    public function price(): HasMany
    {
        return $this->hasMany(CertificateApartmentPrice::class, 'apartment_asset_id');
    }
    public function getResidentialAreaAttribute()
    {
        // dd($this->price);
        if (isset($this->price)) {
            foreach ($this->price as $detail) {
                if ($detail->slug === 'apartment_area') {
                    return $detail->value;
                }
            }
        }
        return 1;
    }

    public function getResidentialUnitPriceAttribute()
    {
        if (isset($this->price)) {
            foreach ($this->price as $detail) {
                if ($detail->slug === 'apartment_asset_price') {
                    return CommonService::roundPrice($detail->value, 0);
                }
            }
        }
        return 2;
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
