<?php

namespace App\Models;

use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CompareProperty;

/**
 * Class Province
 * @package App\Models
 */
class CompareAssetGeneral extends Model
{
    use SoftDeletes;
    use ElasticquentTrait;

    //protected $dateFormat = 'd-m-Y H:i:s';
    protected $casts = [
        'id' => 'integer',
        'total_area' => 'float',
        'total_amount' => 'float',
        'total_construction_area' => 'float',
    ];

    protected $fillable = [
        'input_source',
        'asset_type_id',
        'status',
        'province_id',
        'district_id',
        'ward_id',
        'street_id',
        'distance_id',
        'full_address',
        'land_no',
        'doc_no',
        'coordinates',
        'source_id',
        'topographic',
        'public_date',
        'contact_person',
        'contact_phone',
        'transaction_type_id',
        'adjust_percent',
        'convert_fee_total',
        'total_area',
        'total_amount',
        'total_area_amount',
        'total_estimate_amount',
        'adjust_amount',
        'total_order_amount',
        'total_land_unit_price',
        'total_construction_area',
        'total_construction_amount',
        'average_land_unit_price',
        'total_raw_amount',
        'created_by',
        'apartment_id',
        'max_value_description',
        'note',
        'migrate_status',
        'project_id',
        'block_id',
        'floor_id',

    ];

    function getTypeName()
    {
        return '_doc';
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
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

    public function distance(): BelongsTo
    {
        return $this->belongsTo(Distance::class, 'distance_id', 'id');
    }

    public function assetType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'asset_type_id', 'id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'source_id', 'id');
    }

    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'transaction_type_id', 'id');
    }

    public function topographicData(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'topographic', 'id');
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class, 'apartment_id', 'id');
    }

    public function blockSpecification(): HasMany
    {
        return $this->hasMany(BlockSpecification::class, 'asset_general_id');
    }

    public function roomDetails(): HasMany
    {
        return $this->hasMany(RoomDetail::class, 'asset_general_id');
    }

    public function pic(): HasMany
    {
        return $this->hasMany(CompareGeneralPic::class, 'asset_general_id');
    }

    public function version(): HasMany
    {
        return $this->hasMany(CompareAssetVersion::class, 'asset_general_id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(CompareProperty::class, 'asset_general_id');
    }

    public function tangibleAssets(): HasMany
    {
        return $this->hasMany(CompareTangibleAsset::class, 'asset_general_id');
    }

    public function otherAssets(): HasMany
    {
        return $this->hasMany(CompareOtherAsset::class, 'asset_general_id');
    }

    public function appraiseHasAsset(): HasMany
    {
        return $this->HasMany(AppraiseHasAsset::class, 'asset_general_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id', 'id');
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class, 'floor_id', 'id');
    }
    public function apartmentSpecification(): HasOne
    {
        return $this->hasOne(ApartmentSpecification::class, 'asset_general_id', 'id');
    }

    public function getAreaTotalAttribute()
    {
        $value = $this->total_area;
        $roomDetail = RoomDetail::query()->where('asset_general_id', $this->id)->first();
        if (!empty($roomDetail)) {
            $value = $roomDetail->area;
        }
        return $value;
    }
    public function getFrontSideTextAttribute()
    {
        $value = 'Không biết';
        $item = CompareProperty::where('asset_general_id', $this->id)->first();
        // dd($item->front_side);
        if ($item)
            $value = $item->front_side == 1 ? 'Mặt tiền' : 'Hẻm';
        // dd($value);
        return $value;
    }
    public function getLandTypeTextAttribute()
    {
        $value = 'Không biết';
        $item = CompareProperty::where('asset_general_id', $this->id)->first();
        if ($item)
            $value = $item->landType->description;
        return $value;
    }
    // public function getFrontSideTextAttribute()
    // {
    //     $value = 'Không biết';
    //     if ($this->properties && count($this->properties) > 0)
    //         $value = $this->properties->first()->front_side ? 'Mặt tiền' : 'Hẻm';
    //     return $value;
    // }
    // public function getLandTypeTextAttribute()
    // {
    //     $value = 'Không biết';
    //     if ($this->properties && count($this->properties) > 0)
    //         $value = $this->properties->first()->landType->description;
    //     return $value;
    // }
}
