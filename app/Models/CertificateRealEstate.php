<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CertificateRealEstate extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
        'total_price' => 'integer',
        'total_area' => 'float',
    ];
    protected $fillable = [
        'asset_type_id',
        'appraise_asset',
        'total_area',
        'total_price',
        'created_by',
        'coordinates',
        'front_side',
        'status',
        'round_total',
        'real_estate_id',
        'created_at',
        'updated_at',
        'full_address'
    ];

    public function createdBy():BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function assetType():BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'asset_type_id', 'id');
    }

    public function hasRealEstate():BelongsTo
    {
        return $this->belongsTo(CertificateHasRealEstate::class,'id' ,'real_estate_id');
    }
    
    public function apartmentAsssets():BelongsTo
    {
        return $this->belongsTo(CertificateApartment::class, 'id', 'real_estate_id');
    }

    public function asset()
    {
        $apartment = $this->belongsTo(CertificateApartment::class, 'id', 'real_estate_id')->select(['id', 'real_estate_id', 'asset_type_id', DB::raw("apartment_asset_id as asset_id")]);
        $appraise = $this->belongsTo(CertificateAsset::class, 'id', 'real_estate_id')->select(['id', 'real_estate_id', 'asset_type_id',  DB::raw("appraise_id as asset_id")]);
        return $appraise->unionAll($apartment);
    }

    public function appraises():HasOne
    {
        return $this->hasOne(CertificateAsset::class, 'real_estate_id');
    }
    public function apartment():HasOne
    {
        return $this->hasOne(CertificateApartment::class, 'real_estate_id');
    }
}
