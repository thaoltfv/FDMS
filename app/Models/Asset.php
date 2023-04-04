<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'asset_type_id',
        'created_by'
    ];

    public function assetType():BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'id','asset_type_id');
    }
    public function otherAssets():HasMany
    {
        return $this->hasMany(OtherCertificateAsset::class,'asset_id','id');
    }
    public function appraises():HasMany
    {
        return $this->hasMany(appraises::class,'asset_id','id');
    }
}
