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
class Certificate extends Model
{
    use SoftDeletes;

    protected $table = 'certificates';
    //protected $dateFormat = 'd-m-Y H:i:s';
    protected $casts = [
        'id' => 'integer',
        'appraiser_confirm_id' => 'integer',
        'appraiser_sale_id' => 'integer',
        'commission_fee' => 'float',
        'document_type' => 'array',
    ];

    protected $fillable = [
        'status',
        'ticket_num',
        'document_num',
        'document_date',
        'appraise_date',
        'certificate_num',
        'certificate_date',
        'petitioner_name',
        'petitioner_phone',
        'petitioner_address',
        'appraiser_id',
        'appraiser_manager_id',
        'appraiser_control_id',
        'appraiser_confirm_id',
        'document_description',
        'appraise_purpose_id',
        'service_fee',
        'appraiser_sale_id',
        'appraiser_perform_id',
        'commission_fee',
        'customer_id',
        'created_by',
        'updated_at',
        'document_type',
        'branch_id',
        'petitioner_identity_card',
        'sub_status',
        'status_updated_at',
        'status_expired_at',
        'note',
        'administrative_id',
        'business_manager_id',
    ];

    public function getStatusTextAttribute()
    {
        $status = $this->status;
        $statusText = "";
        switch ($status) {
            case 1:
                $statusText = "Mới";
                break;
            case 2:
                $statusText = "Đang Thẩm Định";
                break;
            case 3:
                $statusText = "Đang Duyệt";
                break;
            case 4:
                $statusText = "Hoàn Thành";
                break;
            case 6:
                return 'Đang Kiểm Soát';
                break;
            case 5:
                $statusText = "Huỷ";
                break;
            case 7:
                $statusText = "Duyệt phát hành";
                break;
            case 8:
                $statusText = "In hồ sơ";
                break;
            case 9:
                $statusText = "Bàn giao khách hàng";
                break;
        }
        return $statusText;
    }
    public function getRoundCertificateTotalAttribute()
    {
        $roundCertificateTotal = 0;
        foreach ($this->appraises as $index => $certificateAsset) {
            $item = AppraisePrice::where('appraise_id', $certificateAsset->appraise_id)->where('slug', 'round_appraise_total')->first();
            $roundAppraiseTotal = isset($item->value) ? (int)$item->value : 0;
            if (!$index) {
                $roundCertificateTotal = $roundAppraiseTotal;
            } else {
                if (($roundCertificateTotal < 0) || ($roundAppraiseTotal < 0)) {
                    if (abs($roundCertificateTotal) > abs($roundAppraiseTotal))
                        $roundCertificateTotal = -abs($roundAppraiseTotal);
                    else
                        $roundCertificateTotal = -abs($roundCertificateTotal);
                } else {
                    if ($roundCertificateTotal > $roundAppraiseTotal) $roundCertificateTotal = $roundAppraiseTotal;
                }
            }
        }

        return $roundCertificateTotal;
    }

    public function appraiser(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'appraiser_id', 'id');
    }

    public function appraiserManager(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'appraiser_manager_id', 'id');
    }
    public function appraiserBusinessManager(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'business_manager_id', 'id');
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

    public function certificateApproach(): belongsToMany
    {
        return $this->belongsToMany(AppraiseOtherInformation::class, 'certificate_approach', 'certificate_id', 'certificate_approach_id');
    }

    public function appraiseMethodUsed(): belongsToMany
    {
        return $this->belongsToMany(AppraiseOtherInformation::class, 'certificate_method_used', 'certificate_id', 'certificate_method_used_id');
    }

    public function appraiseBasisProperty(): belongsToMany
    {
        return $this->belongsToMany(AppraiseOtherInformation::class, 'certificate_basis_property', 'certificate_id', 'certificate_basis_property_id');
    }

    public function certificatePrinciple(): belongsToMany
    {
        return $this->belongsToMany(AppraiseOtherInformation::class, 'certificate_principle', 'certificate_id', 'certificate_principle_id');
    }

    public function appraisePurpose(): BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class, 'appraise_purpose_id');
    }

    public function preType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'pre_type_id', 'id');
    }
    public function administrative(): BelongsTo
    {

        return $this->belongsTo(Appraiser::class, 'administrative_id', 'id');
    }
    public function appraises(): belongsToMany
    {
        return $this->belongsToMany(CertificateAsset::class, 'certificate_has_appraises', 'certificate_id', 'appraise_id');
    }

    public function legalDocumentsOnValuation(): belongsToMany
    {
        return $this->belongsToMany(AppraiseLawDocument::class, 'certificate_legal_documents_on_valuation', 'certificate_id', 'certificate_law_id')->orderBy('position');
    }

    public function legalDocumentsOnConstruction(): belongsToMany
    {
        return $this->belongsToMany(AppraiseLawDocument::class, 'certificate_legal_documents_on_construction', 'certificate_id', 'certificate_law_id')->orderBy('position');
    }

    public function legalDocumentsOnLand(): belongsToMany
    {
        return $this->belongsToMany(AppraiseLawDocument::class, 'certificate_legal_documents_on_land', 'certificate_id', 'certificate_law_id')->orderBy('position');
    }

    public function legalDocumentsOnLocal(): belongsToMany
    {
        return $this->belongsToMany(AppraiseLawDocument::class, 'certificate_legal_documents_on_local', 'certificate_id', 'certificate_law_id')->orderBy('position');
    }

    /* public function constructionCompany(): belongsToMany
    {
        return $this->belongsToMany(AppraisalConstructionCompany::class,'certificate_construction_company','certificate_id','construction_company_id');
    }  */

    public function constructionCompany(): hasMany
    {
        //return $this->belongsTo(CertificateAssetConstructionCompany::class,'certificate_asset_comparison_factor','certificate_id');
        return $this->hasMany(CertificateAssetConstructionCompany::class, 'certificate_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function comparisonFactor(): hasMany
    {
        return $this->hasMany(CertificateComparisonFactor::class, 'certificate_id');
    }

    public function otherDocuments(): HasMany
    {
        return $this->hasMany(CertificateOtherDocuments::class, 'certificate_id');
    }

    public function assetPrice(): HasMany
    {
        return $this->hasMany(CertificatePrice::class, 'certificate_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /* public function getTotalAssetPriceAttribute()
    {
		$item = CertificatePrice::where('certificate_id', $this->id)->where('slug', 'total_asset_price')->first();
		return isset($item->value) ? $item->value : 0;
    } */

    public function getTotalAssetPriceAttribute()
    {
        $item = CertificatePrice::where('certificate_id', $this->id)->where('slug', 'total_asset_price')->first();
        $value = isset($item->value) ? $item->value : 0;
        $certificate = new \stdClass();
        $certificate->round_certificate_total = $this->getRoundCertificateTotalAttribute();
        return CommonService::roundCertificatePrice($certificate, $value);
    }

    public function getCertificateAssetPriceAttribute()
    {
        $item = CertificatePrice::where('certificate_id', $this->id)->where('slug', 'total_asset_price')->first();
        return isset($item->value) ? (int)$item->value : 0;
    }
    public function getCertificateAssetPriceRoundAttribute()
    {
        $item = CertificatePrice::where('certificate_id', $this->id)->where('slug', 'total_asset_price')->first();
        $value =  isset($item->value) ? (int)$item->value : 0;
        $certificate = new \stdClass();
        return CommonService::roundCertificatePrice($certificate, $value);
    }

    public function saleDocuments(): HasMany
    {
        return $this->hasMany(CertificateOtherDocuments::class, 'certificate_id');
    }

    public function personalProperties(): BelongsToMany
    {
        return $this->belongsToMany(CertificatePersonalProperty::class, 'certificate_has_personal_properties', 'certificate_id', 'personal_property_id');
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
                $data[$stt]['asset'] = null;
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
            $result = array_merge($result, Arr::pluck($realEstate, 'real_estate_id'));
        }
        if (count($personalProperties) > 0) {
            $result = array_merge($result, Arr::pluck($personalProperties, 'personal_property_id'));
        }
        return $result;
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function apartmentAsset(): belongsToMany
    {
        return $this->belongsToMany(CertificateApartment::class, CertificateHasApartment::class, 'certificate_id', 'apartment_asset_id');
    }
    // public function apartmentAssetPrint(): belongsToMany
    // {
    //     return $this->belongsToMany(ApartmentAsset::class, 'certificate_id');
    //     return $this->belongsToMany(CertificateAsset::class, 'certificate_has_appraises', 'certificate_id', 'appraise_id');
    // }

    public function apartmentAssetPrint(): HasMany
    {
        return $this->hasMany(ApartmentAsset::class, 'certificate_id');
    }
    public function realEstate(): belongsToMany
    {
        return $this->belongsToMany(CertificateRealEstate::class, CertificateHasRealEstate::class, 'certificate_id', 'real_estate_id')->orderBy('real_estate_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PreCertificatePayments::class, 'certificate_id');
    }
    public function exportDocuments(): HasMany
    {
        return $this->hasMany(PreCertificateExportDocuments::class, 'certificate_id');
    }
}
