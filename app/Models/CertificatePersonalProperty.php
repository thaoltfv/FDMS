<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CertificatePersonalProperty extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];
    protected $fillable = [
        'asset_type_id',
        'created_by',
        'name',
        'total_price',
        'status',
        'step',
        'personal_property_id',
        'created_at',
        'updated_at',
    ];
    public function assetType():BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'asset_type_id','id');
    }
    public function createdBy():BelongsTo
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function hasPersonal():BelongsTo
    {
        return $this->belongsTo(CertificateHasPersonalProperty::class,'id' ,'personal_property_id');
    }
    // public function asset():BelongsTo
    // {
    //     $select = ['id' , 'personal_property_id'];
    //     $other = $this->belongsTo(OtherCertificateBrief::class, 'id', 'general_asset_id')->select($select);
    //     $machine = $this->belongsTo(MachineCertificateBrief::class, 'id', 'general_asset_id')->select($select);
    //     $verhicle = $this->belongsTo(VerhicleCertificateBrief::class, 'id', 'general_asset_id')->select($select);
    //     return $this->belongsTo(VerhicleCertificateBrief::class, 'id', 'personal_property_id')->select($select);
    //     // return $other->unionAll($machine)->unionAll($verhicle);
    // }
}
