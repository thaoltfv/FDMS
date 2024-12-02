<?php

namespace App\Models;

use App\Repositories\EloquentCompareAssetGeneralRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraiseHasAsset  extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'asset_general_id',
        'asset_property_detail_id',
        'appraise_id',
        'appraise_price',
        'asset_price',
        'version',
    ];

    public function assetGeneral()
    {
        return $this->belongsTo(CompareAssetGeneral::class,'asset_general_id','id');
    }
    public function getAssetGeneralAttribute()
    {
        $result = [];
        if (isset($this->id)&&!empty($this->id) && isset($this->version)&&!empty($this->version)) {
            $compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
            $item = $compareAssetGeneralRepository->findVersionById_v2($this->asset_general_id, $this->version);
            $result = $item;

        }
        return $result;
    }
}
