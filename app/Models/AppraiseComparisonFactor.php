<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraiseComparisonFactor extends Model
{
    protected $table = 'appraise_comparison_factor';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'description' => 'string',
    ];

    protected $fillable = [
        'appraise_id',
        'asset_general_id',
        'status',
        'type',
        'appraise_title',
        'asset_title',
        'description',
        'adjust_percent',
        'name',
        'position',
        'adjust_coefficient'
    ];

    // protected $appends = [
    //     'description_capitalize',
    //     'appraise_title_capitalize',
    //     'asset_title_capitalize',
    // ];

    // protected function getDescriptionAttribute(){
    //     $lowwer = mb_strtolower($this->description) ;
    //     return ucfirst($lowwer);
    // }
    protected function getDescriptionCapitalizeAttribute(){
        return ucfirst(mb_strtolower($this->description));
    }
    protected function getAppraiseTitleCapitalizeAttribute(){
        return ucfirst(mb_strtolower($this->appraise_title));
    }
    protected function getAssetTitleCapitalizeAttribute(){
        return ucfirst(mb_strtolower($this->asset_title));
    }
}
