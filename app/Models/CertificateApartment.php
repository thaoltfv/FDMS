<?php

namespace App\Models;

use App\Repositories\EloquentCompareAssetGeneralRepository;
use App\Services\CommonService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class CertificateApartment extends Model
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
        'apartment_asset_id',
        'real_estate_id',
        'full_address'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function apartmentAssetProperties(): HasOne
    {
        return $this->hasOne(CertificateApartmentProperty::class, 'apartment_asset_id');
    }
    public function law(): HasMany
    {
        return $this->hasMany(CertificateApartmentLaw::class, 'apartment_asset_id');
    }
    public function apartmentAppraisalBase(): HasOne
    {
        return $this->hasOne(CertificateApartmentAppraisalBase::class, 'apartment_asset_id');
    }
    public function price(): HasMany
    {
        return $this->hasMany(CertificateApartmentPrice::class, 'apartment_asset_id');
    }
    public function getAssetGeneralAttribute()
    {
        $result = [];
        try {
            if (isset($this->id) && !empty($this->id)) {
                $items = CertificateApartmentHasAsset::where('apartment_asset_id', $this->id)->get();
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
                        if (isset($assetUti) && ! empty($assetUti)) {
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
    public function pic(): HasMany
    {
        return $this->hasMany(CertificateApartmentPic::class, 'apartment_asset_id');
    }
    public function comparisonFactor(): HasMany
    {
        return $this->hasMany(CertificateApartmentComparisonFactor::class, 'apartment_asset_id')->orderBy('asset_general_id', 'DESC')->orderBy('position');;
    }
    public function appraisal(): HasMany
    {
        return $this->hasMany(CertificateApartmentAppraisalMethod::class, 'apartment_asset_id');
    }

    public function getAppraisalMethodsAttribute()
    {
        if (CertificateApartmentAppraisalMethod::query()->where(['apartment_asset_id' => $this->id])->exists()) {
            $datas = CertificateApartmentAppraisalMethod::query()
                ->where(['apartment_asset_id' => $this->id])
                ->get(['slug', 'slug_value', 'value', 'description']);

            $result = null;
            if (isset($datas)) {
                foreach ($datas as $data) {
                    $result[$data->slug]['slug'] = $data->slug;
                    $result[$data->slug]['slug_value'] = $data->slug_value;
                    $result[$data->slug]['value'] = $data->value;
                    $result[$data->slug]['description'] = $data->description;
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
    public function apartmentHasAssets(): HasMany
    {
        return $this->hasMany(CertificateApartmentHasAsset::class, 'apartment_asset_id')->orderBy('asset_general_id', 'DESC');
    }
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
    public function apartmentAdapter(): HasMany
    {
        return $this->hasMany(CertificateApartmentAdapter::class, 'apartment_asset_id')->orderBy('asset_general_id', 'DESC');
    }
    public function version(): HasMany
    {
        return $this->hasMany(CertificateApartmentVersion::class, 'apartment_asset_id');
    }
    public function lastVersion(): HasOne
    {
        return $this->hasOne(CertificateApartmentVersion::class, 'apartment_asset_id')->latest();
    }
    public function assetPrice(): HasMany
    {
        return $this->hasMany(CertificateApartmentPrice::class, 'apartment_asset_id');
    }
}
