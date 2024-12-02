<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraiseUnitArea extends Model
{
    use SoftDeletes;
    protected $table = 'appraise_unit_area';
    protected $casts = [
        'id' => 'integer',
		'violation_asset_area' => 'float',
    ];

    protected $fillable = [
        'appraise_id',
        'asset_general_id',
        'land_type_id',
        'violation_asset_area',
        'position_type_id',
        'created_by',
    ];

    public function landTypeData(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'land_type_id','id');
    }
	
	public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
