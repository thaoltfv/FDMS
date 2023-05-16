<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RealEstate extends Model
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
        'sub_status',
        'planning_info',
        'planning_source',
        'contact_person',
        'contact_phone'
    ];

    public function createdBy():BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function assetType():BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'asset_type_id', 'id');
    }

    public function appraises():HasOne
    {
        return $this->hasOne(Appraise::class, 'real_estate_id','id');
    }

    public function apartment():HasOne
    {
        return $this->hasOne(ApartmentAsset::class, 'real_estate_id','id');
    }

    public function asset():HasOne
    {
        $select = ['id','real_estate_id'];
        $apartments =  $this->hasOne(ApartmentAsset::class, 'real_estate_id', 'id')->select( $select);
        $appraises =  $this->hasOne(Appraise::class, 'id')->select( $select);
        return $apartments->unionAll($appraises);
    }

    public function assetUpdate():HasOne
    {
        $select = ['id','real_estate_id','created_at', 'updated_at','status'];
        $apartments =  $this->hasOne(ApartmentAsset::class, 'real_estate_id','id')->select( $select);
        $appraises =  $this->hasOne(Appraise::class, 'real_estate_id','id')->select( $select);
        return $apartments->unionAll($appraises);
    }
    public function assetFull():HasOne
    {
        $with = [
            'createdBy:id,name',
            'province:id,name',
            'district:id,name',
            'ward:id,name',
            'street:id,name'
        ];
        $select = ['id','full_address','real_estate_id','province_id','district_id','ward_id','street_id'];
        $apartments =  $this->hasOne(ApartmentAsset::class, 'real_estate_id','id')->with($with)->select( $select);
        $appraises =  $this->hasOne(Appraise::class, 'real_estate_id','id')->with($with)->select( $select);
        return $apartments->unionAll($appraises);
    }
    public function getLastVersionAttribute()
    {
        $apartment = $this->apartment;
        $appraise = $this->appraises;
        $version = '';
        if (!empty($apartment)) {
            $version = $apartment->lastVersion->version;
        }
        if (!empty($appraise)) {
            $version = $appraise->lastVersion->version;
        }
        return $version;
    }
    public function certificate():BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id', 'id');
    }
    public function getTotalConstructionBaseAttribute()
    {
        if ($this->appraises)
            return $this->appraises->total_construction_base;
        return 0; // or some other default value
    }
}
