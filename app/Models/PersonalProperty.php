<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PersonalProperty extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'total_price' => 'float',
    ];
    // protected $hidden = [
    //     'laravel_through_key'
    //  ];
    protected $fillable = [
        'asset_type_id',
        'created_by',
        'name',
        'total_price',
        'status',
        'step',
        'sub_status'
    ];

    public function createdBy():BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function assetType():BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'asset_type_id', 'id');
    }

    public function price():HasOneThrough
    {
        return $this->hasOneThrough(OtherCertificateAssetPrice::class, OtherCertificateAsset::class,'personal_property_id', 'other_asset_id')->select(['unit_price'])
                    ->union($this->hasOneThrough(MachineCertificateAssetPrice::class, MachineCertificateAsset::class,'personal_property_id', 'machine_asset_id')->select(['unit_price',DB::raw('machine_certificate_assets.personal_property_id as laravel_through_key')]))
                    ->union($this->hasOneThrough(VerhicleCertificateAssetPrice::class, VerhicleCertificateAsset::class,'personal_property_id', 'verhicle_asset_id')->select(['unit_price',DB::raw('verhicle_certificate_assets.personal_property_id as laravel_through_key')]));
    }

}
