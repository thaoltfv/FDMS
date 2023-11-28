<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\CommonService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class Province
 * @package App\Models
 */
class PreCertificate extends Model
{
    use SoftDeletes;

    protected $table = 'preCertificates';
    //protected $dateFormat = 'd-m-Y H:i:s';
    protected $casts = [
        'id' => 'integer',
        'appraiser_confirm_id' => 'integer',
        'appraiser_sale_id' => 'integer',
        'commission_fee' => 'float',
        'document_type' => 'array',
    ];

    protected $fillable = [
        'certificate_id',

        'petitioner_name',
        'petitioner_phone',
        'petitioner_address',
        'petitioner_identity_card',

        // đối tác
        'customer_id',

        // Loại sơ bộ
        'pre_status',

        'appraise_purpose_id',
        'note',

        // nhân viên kinh doanh
        'appraiser_sale_id',

        //quản lý nghiệp vụ (new)
        'business_manager_id',

        // chuyên viên thực hiện
        'appraiser_performance_id',

        // tài liệu đính kèm
        'pre_certificate_file',    

        // tổng giá trị sơ bộ
        'pre_total_asset_price',
        'pre_result_file'
        
        // Lý do hủy sơ bộ
        'cancel_reason',
    ];

    public function getStatusTextAttribute()
    {
		$status = $this->pre_status;
		$statusText = "";
		switch ($status) {
            case 1:
                $statusText = "Yêu cầu sơ bộ";
                break;
			case 2:
				$statusText = "Định giá sơ bộ";
			    break;
			case 3:
				$statusText = "Duyệt giá sơ bộ";
			    break;
			case 4:
				$statusText = "Hoàn thành";
			    break;
			case 5:
				$statusText = "Huỷ";
			    break;
            case 6:
                return 'Hoàn Thành';
                break;
		}
        return $statusText;
    }
	public function getRoundPreCertificateTotalAttribute()
    {
		$roundPreCertificateTotal = 0;
		foreach($this->appraises as $index => $preCertificateAsset) {
			$item = AppraisePrice::where('appraise_id', $preCertificateAsset->appraise_id)->where('slug', 'round_appraise_total')->first();
			$roundAppraiseTotal = isset($item->value) ? (int)$item->value : 0;
			if(!$index) {
				$roundPreCertificateTotal = $roundAppraiseTotal;
			} else {
				if(($roundPreCertificateTotal < 0) || ($roundAppraiseTotal < 0)) {
					if (abs($roundPreCertificateTotal) > abs($roundAppraiseTotal))
						$roundPreCertificateTotal = -abs($roundAppraiseTotal);
					else
						$roundPreCertificateTotal = -abs($roundPreCertificateTotal);
				} else {
					if ($roundPreCertificateTotal > $roundAppraiseTotal) $roundPreCertificateTotal = $roundAppraiseTotal;
				}
			}
		}

		return $roundPreCertificateTotal;
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id', 'id');
    }


    public function appraiser(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'appraiser_id', 'id');
    }

    public function appraiserManager(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'appraiser_manager_id', 'id');
    }

    public function appraiserControl(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'appraiser_control_id', 'id');
    }

    public function appraiserConfirm(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'appraiser_confirm_id', 'id');
    }

	public function appraiserSale(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'appraiser_sale_id', 'id');
    }

	public function appraiserPerform(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'appraiser_perform_id', 'id');
    }

    public function preCertificateApproach(): belongsToMany
    {
        return $this->belongsToMany(AppraiseOtherInformation::class,'certificate_approach','certificate_id','certificate_approach_id');
    }

    public function appraiseMethodUsed(): belongsToMany
    {
        return $this->belongsToMany(AppraiseOtherInformation::class,'certificate_method_used','certificate_id','certificate_method_used_id');
    }

    public function appraiseBasisProperty(): belongsToMany
    {
        return $this->belongsToMany(AppraiseOtherInformation::class,'certificate_basis_property','certificate_id','certificate_basis_property_id');
    }

    public function preCertificatePrinciple(): belongsToMany
    {
        return $this->belongsToMany(AppraiseOtherInformation::class,'certificate_principle','certificate_id','certificate_principle_id');
    }

    public function appraisePurpose(): BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class, 'appraise_purpose_id');
    }

    public function appraises(): belongsToMany
    {
        return $this->belongsToMany(PreCertificateAsset::class,'certificate_has_appraises','certificate_id','appraise_id');
    }

    public function legalDocumentsOnValuation(): belongsToMany
    {
        return $this->belongsToMany(AppraiseLawDocument::class,'certificate_legal_documents_on_valuation','certificate_id','certificate_law_id')->orderBy('position');
    }

    public function legalDocumentsOnConstruction(): belongsToMany
    {
        return $this->belongsToMany(AppraiseLawDocument::class,'certificate_legal_documents_on_construction','certificate_id','certificate_law_id')->orderBy('position');
    }

    public function legalDocumentsOnLand(): belongsToMany
    {
        return $this->belongsToMany(AppraiseLawDocument::class,'certificate_legal_documents_on_land','certificate_id','certificate_law_id')->orderBy('position');
    }

    public function legalDocumentsOnLocal(): belongsToMany
    {
        return $this->belongsToMany(AppraiseLawDocument::class,'certificate_legal_documents_on_local','certificate_id','certificate_law_id')->orderBy('position');
    }

    /* public function constructionCompany(): belongsToMany
    {
        return $this->belongsToMany(AppraisalConstructionCompany::class,'certificate_construction_company','certificate_id','construction_company_id');
    }  */

	public function constructionCompany(): hasMany
    {
        //return $this->belongsTo(PreCertificateAssetConstructionCompany::class,'certificate_asset_comparison_factor','certificate_id');
		return $this->hasMany(PreCertificateAssetConstructionCompany::class,'certificate_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function comparisonFactor(): hasMany
    {
        return $this->hasMany(PreCertificateComparisonFactor::class, 'certificate_id');
    }

	public function otherDocuments(): HasMany
    {
        return $this->hasMany(PreCertificateOtherDocuments::class, 'certificate_id');
    }

	public function assetPrice(): HasMany
    {
        return $this->hasMany(PreCertificatePrice::class, 'certificate_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

	/* public function getTotalAssetPriceAttribute()
    {
		$item = PreCertificatePrice::where('certificate_id', $this->id)->where('slug', 'total_asset_price')->first();
		return isset($item->value) ? $item->value : 0;
    } */

	public function getTotalAssetPriceAttribute()
    {
		$item = PreCertificatePrice::where('certificate_id', $this->id)->where('slug', 'total_asset_price')->first();
		$value = isset($item->value) ? $item->value : 0;
		$preCertificate = new \stdClass();
		$preCertificate->round_certificate_total = $this->getRoundPreCertificateTotalAttribute();
		return CommonService::roundPreCertificatePrice($preCertificate, $value);
    }

    public function getPreCertificateAssetPriceAttribute()
    {
		$item = PreCertificatePrice::where('certificate_id', $this->id)->where('slug', 'total_asset_price')->first();
        return isset($item->value) ? (int)$item->value : 0;
    }
    public function getPreCertificateAssetPriceRoundAttribute()
    {
		$item = PreCertificatePrice::where('certificate_id', $this->id)->where('slug', 'total_asset_price')->first();
        $value =  isset($item->value) ? (int)$item->value : 0;
        $preCertificate = new \stdClass();
        return CommonService::roundPreCertificatePrice($preCertificate, $value);
    }

    public function saleDocuments(): HasMany
    {
        return $this->hasMany(PreCertificateOtherDocuments::class, 'certificate_id');
    }

    public function personalProperties(): BelongsToMany
    {
        return $this->belongsToMany(PreCertificatePersonalProperty::class,'certificate_has_personal_properties','certificate_id','personal_property_id');
    }

    public function getGeneralAssetAttribute()
    {
        $data = [];
        $realEstate = $this->realEstate;
        $personalProperties = $this->personalProperties;
        $stt = 0;
        if (count($realEstate) > 0) {
            foreach ($realEstate as $item) {
                $data[$stt]['id'] = $item->id;
                $data[$stt]['asset_type_id'] = $item->asset_type_id;
                $data[$stt]['general_asset_id'] = $item->real_estate_id;
                $data[$stt]['name'] = $item->appraise_asset;
                $data[$stt]['created_by'] = $item->createdBy;
                $data[$stt]['asset_type'] = $item->assetType;
                $data[$stt]['asset'] = $item->asset;
                $data[$stt]['total_area'] = $item->total_area;
                $data[$stt]['total_price'] = CommonService::roundPrice($item->total_price, $item->round_total);
                $data[$stt]['round_total'] = $item->round_total;
                if (!empty($item->appraises)) {
                    $version = $item->appraises->lastVersion ? $item->appraises->lastVersion->version : '';
                } else {
                    $version = $item->apartment->lastVersion ? $item->apartment->lastVersion->version : '';
                }
                $data[$stt]['version'] = $version;
                $stt++;
            }
        }
        if (count($personalProperties) > 0) {
            foreach ($personalProperties as $item) {
                $data[$stt]['id'] = $item->id;
                $data[$stt]['asset_type_id'] = $item->asset_type_id;
                $data[$stt]['general_asset_id'] = $item->personal_property_id;
                $data[$stt]['name'] = $item->name;
                $data[$stt]['created_by'] = $item->createdBy;
                $data[$stt]['asset_type'] = $item->assetType;
                $data[$stt]['asset'] =null;
                $data[$stt]['total_area'] = $item->total_area;
                $data[$stt]['total_price'] = $item->total_price;
                $data[$stt]['round_total'] = 0;
                $data[$stt]['version'] = '';
                $stt++;
            }
        }
        return $data;
    }
    public function getDetailListIdAttribute()
    {
        $result = [];
        $realEstate = $this->realEstate;
        $personalProperties = $this->personalProperties;
        if (count($realEstate) > 0) {
            $result =array_merge($result, Arr::pluck($realEstate, 'real_estate_id'));
        }
        if (count($personalProperties) > 0) {
            $result = array_merge($result, Arr::pluck($personalProperties, 'personal_property_id'));
        }
        return $result;
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id','id');
    }

    public function apartmentAsset(): belongsToMany
    {
        return $this->belongsToMany(PreCertificateApartment::class, PreCertificateHasApartment::class ,'certificate_id', 'apartment_asset_id');
    }

    public function realEstate(): belongsToMany
    {
        return $this->belongsToMany(PreCertificateRealEstate::class, PreCertificateHasRealEstate::class,'certificate_id', 'real_estate_id')->orderBy('real_estate_id');
    }
}
