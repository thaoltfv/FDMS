<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppraiseApartmentProperty extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
        'project_id' => 'integer',
        'appraise_id' => 'integer',
        'block_id' => 'integer',
        'floor_id' => 'integer',
        'apartment_id' => 'integer',
        'area' => 'float',
        'bedroom_num' => 'integer',
        'wc_num' => 'integer',
        'direction_id' => 'integer',
        'legal_id' => 'integer',
        'furniture_quality_id' => 'integer',
    ];
    protected $fillable =[
        'appraise_id',
        'project_id',
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
    ];

    protected $hidden =[
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function appraises():BelongsTo
    {
        return $this->belongsTo(Appraise::class, 'appraise_id', 'id');
    }
}
