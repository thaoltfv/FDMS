<?php

namespace App\Models;

use App\Repositories\EloquentCompareAssetGeneralRepository;
use App\Services\ApartmentVersionService;
use App\Services\AppraiseVersionService;
use App\Services\CommonService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class ApartmentAsset extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'asset_type_id',
        'project_id',
        'province_id',
        'district_id',
        'ward_id',
        'street_id',
        'appraise_asset',
        'coordinates',
        'created_by',
        'step',
        'status',
        'real_estate_id',
        'sub_status',
        'full_address',
        'price_estimate_id',
        'apartment_number'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function comparisonFactor(): HasMany
    {
        return $this->hasMany(ApartmentAssetComparisonFactor::class, 'apartment_asset_id')->orderBy('asset_general_id', 'DESC')->orderBy('position');;
    }

    public function apartmentHasAsset(): HasMany
    {
        return $this->hasMany(ApartmentAssetHasAsset::class, 'apartment_asset_id')->orderBy('asset_general_id', 'DESC');
    }

    public function pic(): HasMany
    {
        return $this->hasMany(ApartmentAssetPic::class, 'apartment_asset_id');
    }

    public function assetType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'asset_type_id', 'id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class, 'street_id', 'id');
    }

    public function createBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function apartmentAssetProperties(): BelongsTo
    {
        return $this->belongsTo(ApartmentAssetProperty::class, 'id', 'apartment_asset_id');
    }

    public function law(): HasMany
    {
        return $this->hasMany(ApartmentAssetLaw::class, 'apartment_asset_id');
    }

    public function valueBaseAndApproach(): HasOne
    {
        return $this->hasOne(ApartmentAssetAppraisalBase::class, 'apartment_asset_id', 'id');
    }

    public function appraisal(): HasMany
    {
        return $this->hasMany(ApartmentAssetAppraisalMethod::class, 'apartment_asset_id');
    }

    public function getAppraisalMethodsAttribute()
    {
        if (ApartmentAssetAppraisalMethod::query()->where(['apartment_asset_id' => $this->id])->exists()) {
            $datas = ApartmentAssetAppraisalMethod::query()
                ->where(['apartment_asset_id' => $this->id])
                ->get(['slug', 'slug_value', 'value']);

            $result = null;
            if (isset($datas)) {
                foreach ($datas as $data) {
                    $result[$data->slug]['slug'] = $data->slug;
                    $result[$data->slug]['slug_value'] = $data->slug_value;
                    $result[$data->slug]['value'] = $data->value;
                }
            }
        } else {
            $result1 = [
                'slug_value' => 'trung-binh',
                'value' => null,
            ];
            $result2 = [
                'slug_value' => 'theo-chi-phi-chuyen-mdsd-dat',
                'value' => null,
            ];
            $result3 = [
                'slug_value' => 'theo-gia-dat-qd-ubnd',
                'value' => null,
            ];
            $result = array_merge(
                ['thong_nhat_muc_gia_chi_dan' => $result1],
                ['tinh_gia_dat_hon_hop_con_lai' => $result2],
                ['tinh_gia_dat_vi_pham_quy_hoach' => $result3]
            );
        }

        return $result;
    }

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 1:
                return 'Mới';
                break;
            case 2:
                return 'Đang thẩm định';
                break;
            case 3:
                return 'Đang duyệt';
                break;
            case 4:
                return 'Hoàn thành';
                break;
            case 6:
                return 'Đang kiểm soát';
                break;
            default:
                return 'Hủy';
        }
    }

    public function getAssetsGeneralAttribute()
    {
        $result = [];
        try {
            if (isset($this->id) && !empty($this->id)) {
                $items = ApartmentAssetHasAsset::where('apartment_asset_id', $this->id)->orderBy('asset_general_id', 'DESC')->get();
                $stt = 0;
                $utilities = CommonService::getUtilities();
                foreach ($items as $item) {
                    $compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
                    $result[$stt] = $compareAssetGeneralRepository->findApartmentVersionById($item->asset_general_id, $item->version);
                    $result[$stt]['version'] = $item->version;
                    $asset = $result[$stt]->apartment_specification;
                    if (isset($asset)) {
                        $utilityDesc = [];
                        $assetUti = $asset['utilities'] ?? null;
                        if (isset($assetUti) && !empty($assetUti)) {
                            foreach ($assetUti as $uti) {
                                $des = $utilities->where('acronym', 'ilike', strval($uti))->first();
                                if (isset($des)) {
                                    $utilityDesc[] = $des->description ?? '';
                                }
                            }
                        }
                        $asset['utility_description'] = $utilityDesc;
                    }
                    $result[$stt]->apartment_specification = $asset;
                    $stt++;
                }
            }
        } catch (Exception $ex) {
            Log::error($ex);
            $result = [];
        }

        return $result;
    }

    public function apartmentAdapter(): HasMany
    {
        return $this->hasMany(ApartmentAssetAdapter::class, 'apartment_asset_id')->orderBy('asset_general_id', 'DESC');
    }

    public function otherAssets(): HasMany
    {
        return $this->hasMany(ApartmentAssetOtherAsset::class, 'apartment_asset_id');
    }

    public function price(): HasMany
    {
        return $this->hasMany(ApartmentAssetPrice::class, 'apartment_asset_id');
    }
    public function realEstate(): BelongsTo
    {
        return $this->belongsTo(RealEstate::class, 'real_estate_id', 'id');
    }
    public function version(): HasMany
    {
        return $this->hasMany(ApartmentAssetVersion::class, 'apartment_asset_id');
    }
    public function lastVersion(): HasOne
    {
        return $this->hasOne(ApartmentAssetVersion::class, 'apartment_asset_id')->latest();
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function getMaxVersionAttribute()
    {
        $version = AppraiseVersionService::getVersionApartment($this->id);
        return $version;
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id', 'id');
    }

    public function getAssetGeneralAttribute()
    {
        $result = [];
        try {
            if (isset($this->id) && !empty($this->id)) {
                $items = ApartmentAssetHasAsset::where('apartment_asset_id', $this->id)->orderBy('asset_general_id', 'DESC')->get();
                $stt = 0;
                $utilities = CommonService::getUtilities();
                foreach ($items as $item) {
                    $compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
                    $result[$stt] = $compareAssetGeneralRepository->findApartmentVersionById($item->asset_general_id, $item->version);
                    $result[$stt]['version'] = $item->version;
                    $asset = $result[$stt]->apartment_specification;
                    if (isset($asset)) {
                        $utilityDesc = [];
                        $assetUti = $asset['utilities'] ?? null;
                        if (isset($assetUti) && !empty($assetUti)) {
                            foreach ($assetUti as $uti) {
                                $des = $utilities->where('acronym', 'ilike', strval($uti))->first();
                                if (isset($des)) {
                                    $utilityDesc[] = $des->description ?? '';
                                }
                            }
                        }
                        $asset['utility_description'] = $utilityDesc;
                    }
                    $result[$stt]->apartment_specification = $asset;
                    $i = json_decode(json_encode($result[$stt]), FALSE);
                    $result[$stt] = $i;
                    $stt++;
                }
            }
        } catch (Exception $ex) {
            Log::error($ex);
            $result = [];
        }
        return $result;
    }
}
