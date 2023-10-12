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

    protected $appends = [
        'purpose_detail',
        'status_text',
        'residential_area',
        'residential_unit_price',
        'residential_price',
        'location_type'
    ];

    const  RESIDENTIAL_PURPOSE = ['ODT', 'ONT'];

    private array $landPurposes = [];

    private array $landPositionType = [];

    private array $tangibleType = [];

    // private $purpose_detail = '';
    // private $residential_area = 0;
    // private $residential_unit_price = 0;
    // private $residential_price = 0;
    // private $agricultural_area = 0;
    // private $agricultural_unit_price = 0;
    // private $agricultural_price = 0;
    // private $agricultural_area_2 = 0;
    // private $agricultural_unit_price_2 = 0;
    // private $agricultural_price_2 = 0;
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        //Should use a singleton to load all masterdata
        $dictionaryModel = new Dictionary();
        $_landPurposes = $dictionaryModel->query()->where('type', 'LOAI_DAT_CHI_TIET')->where('status', '=', 1)->orderBy('id')->get();
        if (!empty($_landPurposes)) {
            $this->landPurposes = $_landPurposes->mapWithKeys( function($_landPurposes, $key) {
                return [$_landPurposes->id => $_landPurposes->acronym];
            })->toArray();
        }
        $_landPositionType = $dictionaryModel->query()->where('type', 'VI_TRI_DAT')->where('status', '=', 1)->orderBy('id')->get();
        if (!empty($_landPositionType)) {
            $this->landPositionType = $_landPositionType->mapWithKeys( function($_landPositionType, $key) {
                return [$_landPositionType->id => CommonService::mbCaseTitle($_landPositionType->description)];
            })->toArray();
        }
        $_tangibleType = $dictionaryModel->query()->where('type', 'LOAI_NHA')->where('status', '=', 1)->orderBy('id')->get();
        if (!empty($_tangibleType)) {
            $this->tangibleType = $_tangibleType->mapWithKeys( function($_tangibleType, $key) {
                return [$_tangibleType->id => CommonService::mbCaseTitle($_tangibleType->description)];
            })->toArray();
        }

        $this->bootIfNotBooted();

        $this->initializeTraits();

        $this->syncOriginal();

        $this->fill($attributes);
        // if (isset ($this->propertyDetail)) {
        //     $isFirstAgricultural = true;
        //     foreach ($this->propertyDetail as $detail) {
        //         $acronym = $this->getPurposeAcronym($detail->land_type_purpose_id);
        //         if ($this->purpose_detail != '')
        //             $this->purpose_detail .= ' + ';
        //         $this->purpose_detail .= $acronym ;
        //         if ($detail->is_zoning) {
        //             $this->purpose_detail .= '(VPQH: ' . number_format(floatval($detail->planning_area), 2, ',', '.') . ' PHQH:' . number_format((floatval($detail->total_area) - floatval($detail->planning_area)), 2, ',', '.') . ')';
        //         }
        //         $areaModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_area')->first();
        //         $area = 0;
        //         $unit_price = 0;
        //         if (isset($areaModel))
        //             $area = floatval($areaModel->value);
        //         $priceModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_price')->first();
        //         $roundModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_round')->first();
        //         if (isset($priceModel) && isset($roundModel))
        //             $unit_price = CommonService::roundPrice(floatval($priceModel->value), floatval($roundModel->value));

        //         if(in_array($acronym, self::RESIDENTIAL_PURPOSE)) {
        //             $this->residential_area = $area;
        //             $this->residential_unit_price = $unit_price;
        //             $this->residential_price = CommonService::roundPrice($area * $unit_price);
        //         } else {
        //             if($isFirstAgricultural) {
        //                 $this->agricultural_area = $area;
        //                 $this->agricultural_unit_price = $unit_price;
        //                 $this->agricultural_price = CommonService::roundPrice($area * $unit_price);
        //             } else {
        //                 $this->agricultural_area_2 = $area;
        //                 $this->agricultural_unit_price_2 = $unit_price;
        //                 $this->agricultural_price_2 = CommonService::roundPrice($area * $unit_price);
        //             }
        //             $isFirstAgricultural = false;
        //         }
        //     }
        // }
    }

    public function appraiseMethodUsed(): HasMany
    {
        return $this->hasMany(CertificateAssetAppraisalMethods::class, 'appraise_id');
    }

    public function certificateAssetPrice(): HasMany
    {
        return $this->hasMany(CertificateAssetPrice::class, 'appraise_id');
    }

    public function getStatusTextAttribute()
    {
        return isset(ValueDefault::STATUSES[$this->status]) ? ValueDefault::STATUSES[$this->status] : 'Không rõ';
    }

    public function firstTangible(): HasOne
    {
        return $this->hasOne(CertificateAssetTangibleAsset::class, 'appraise_id');
    }

    public function propertyDetail(): HasMany
    {
        return $this->hasMany(CertificateAssetPropertyDetail::class, 'appraise_property_id', 'property_id');
    }

    public function getPurposeDetailAttribute()
    {
        $result = '';
        if (isset ($this->propertyDetail)) {
            foreach ($this->propertyDetail as $detail) {
                if ($result != '')
                    $result .= ' + ';
                $result .= $this->getPurposeAcronym($detail->land_type_purpose_id) ;
                if ($detail->is_zoning) {
                    $result .= '(VPQH: ' . number_format(floatval($detail->planning_area), 2, ',', '.') . ' PHQH:' . number_format((floatval($detail->total_area) - floatval($detail->planning_area)), 2, ',', '.') . ')';
                }
            }
        }
        return $result;
    }
    public function getLocationTypeAttribute()
    {
        $result = 100;
        if (isset ($this->propertyDetail)) {
            foreach ($this->propertyDetail as $detail) {
                if ($result > $detail->position_type_id)
                    $result = $detail->position_type_id;
            }
        }
        if (key_exists($result, $this->landPositionType))
            return $this->landPositionType[$result];
    }
    public function otherAssets(): HasMany
    {
        return $this->hasMany(CertificateAssetOtherAsset::class, 'appraise_id');
    }

    public function getPurposeAcronym($id) {
        if (key_exists($id, $this->landPurposes))
            return $this->landPurposes[$id];
        return '';
    }

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
    public function getResidentialUnitPriceAttribute()
    {
        if (isset ($this->propertyDetail)) {
            foreach ($this->propertyDetail as $detail) {
                $acronym = $this->getPurposeAcronym($detail->land_type_purpose_id);
                if( in_array($acronym, self::RESIDENTIAL_PURPOSE)) {
                    $priceModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_price')->first();
                    $roundModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_round')->first();
                    if (isset($priceModel) && $roundModel)
                        return CommonService::roundPrice(floatval($priceModel->value), floatval($roundModel->value));
                }
            }
        }
        return 0;
    }
    public function getResidentialPriceAttribute()
    {
        return CommonService::roundPrice($this->residential_area * $this->residential_unit_price);
    }
    public function getAgriculturalAreaAttribute()
    {
        if (isset ($this->propertyDetail)) {
            foreach ($this->propertyDetail as $detail) {
                $acronym = $this->getPurposeAcronym($detail->land_type_purpose_id);
                if( !in_array($acronym, self::RESIDENTIAL_PURPOSE)) {
                    $areaModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_area')->first();
                    if (isset($areaModel))
                        return floatval($areaModel->value);
                }
            }
        }
        return 0;
    }
    public function getAgriculturalUnitPriceAttribute()
    {
        if (isset ($this->propertyDetail)) {
            foreach ($this->propertyDetail as $detail) {
                $acronym = $this->getPurposeAcronym($detail->land_type_purpose_id);
                if(!in_array($acronym, self::RESIDENTIAL_PURPOSE)) {
                    $priceModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_price')->first();
                    $roundModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_round')->first();
                    if (isset($priceModel) && $roundModel)
                        return CommonService::roundPrice(floatval($priceModel->value), floatval($roundModel->value));
                }
            }
        }
        return 0;
    }
    public function getAgriculturalPriceAttribute()
    {
        return CommonService::roundPrice($this->agricultural_area * $this->agricultural_unit_price);
    }

    public function getAgriculturalArea2Attribute()
    {
        $isFirstAgricultural = true;
        if (isset ($this->propertyDetail)) {
            foreach ($this->propertyDetail as $detail) {
                $acronym = $this->getPurposeAcronym($detail->land_type_purpose_id);
                if( !in_array($acronym, self::RESIDENTIAL_PURPOSE) && $isFirstAgricultural === true) {
                    $isFirstAgricultural = false;
                } else if (!in_array($acronym, self::RESIDENTIAL_PURPOSE) && $isFirstAgricultural === false) {
                    $areaModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_area')->first();
                    if (isset($areaModel))
                        return floatval($areaModel->value);
                }
            }
        }
        return 0;
    }
    public function getAgriculturalUnitPrice2Attribute()
    {
        $isFirstAgricultural = true;
        if (isset ($this->propertyDetail)) {
            foreach ($this->propertyDetail as $detail) {
                $acronym = $this->getPurposeAcronym($detail->land_type_purpose_id);
                if( !in_array($acronym, self::RESIDENTIAL_PURPOSE) && $isFirstAgricultural === true) {
                    $isFirstAgricultural = false;
                } else if (!in_array($acronym, self::RESIDENTIAL_PURPOSE) && $isFirstAgricultural === false) {
                    $priceModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_price')->first();
                    $roundModel = $this->certificateAssetPrice->where('slug', 'land_asset_purpose_' . $acronym . '_round')->first();
                    if (isset($priceModel) && $roundModel)
                        return CommonService::roundPrice(floatval($priceModel->value), floatval($roundModel->value));
                }
            }
        }
        return 0;
    }
    public function getAgriculturalPrice2Attribute()
    {
        return CommonService::roundPrice($this->agricultural_area_2 * $this->agricultural_unit_price_2);
    }
    public function getTangibleTypeAttribute()
    {
        if (isset($this->firstTangible) && key_exists($this->firstTangible->building_type_id, $this->tangibleType)) {
            return $this->tangibleType[$this->firstTangible->building_type_id];
        }
        return '';
    }
    public function getTangibleAreaAttribute()
    {
        if (isset($this->firstTangible)) {
            return floatval($this->firstTangible->total_construction_base);
        }
        return 0;
    }
    public function getTangibleUnitPriceAttribute()
    {
        $priceMethod = $this->appraiseMethodUsed->where('slug', 'XAC_DINH_DON_GIA_XAY_DUNG')->first();
        return CommonService::getDgxdChoosed($this->firstTangible, $priceMethod);

    }
    public function getTangibleRemainAttribute()
    {
        $remainMethod = $this->appraiseMethodUsed->where('slug', 'XAC_DINH_CHAT_LUONG_CON_LAI')->first();
        return CommonService::getClclChoosed($this->firstTangible, $remainMethod);

    }
    public function getOtherTangiblePriceAttribute()
    {
        return CommonService::roundPrice($this->tangible_price - $this->first_tangible_price);
    }

    public function getFirstTangiblePriceAttribute()
    {
        return CommonService::roundPrice($this->tangible_unit_price * $this->tangible_remain * $this->tangible_area/100);
    }
}
