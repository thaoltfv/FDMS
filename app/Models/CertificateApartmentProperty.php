<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateApartmentProperty extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'apartment_asset_id' => 'integer',
        'block_id' => 'integer',
        'floor_id' => 'integer',
        'apartment_id' => 'integer',
        'area' => 'float',
        'bedroom_num' => 'integer',
        'wc_num' => 'integer',
        'direction_id' => 'integer',
        'legal_id' => 'integer',
        'furniture_quality_id' => 'integer',
        'utilities' => 'array',
    ];
    protected $fillable =[
        'apartment_asset_id',
        'block_id',
        'floor_id',
        'area',
        'apartment_id',
        'bedroom_num',
        'wc_num',
        'direction_id',
        'legal_id',
        'furniture_quality_id',
        'description',
        'utilities',
        'handover_year',
        'apartment_name',
        'full_name'
    ];

    protected $hidden =[
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function block():BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id','id');
    }

    public function floor():BelongsTo
    {
        return $this->belongsTo(Floor::class, 'floor_id','id');
    }

    public function apartment():BelongsTo
    {
        return $this->belongsTo(Apartment::class, 'apartment_id','id');
    }

    public function direction():BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'direction_id','id');
    }

    public function legal():BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'legal_id','id');
    }

    public function furnitureQuality():BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'furniture_quality_id','id');
    }
    public function apartmentAsset():BelongsTo
    {
        return $this->belongsTo(CertificateApartment::class, 'apartment_asset_id', 'id');
    }

}
