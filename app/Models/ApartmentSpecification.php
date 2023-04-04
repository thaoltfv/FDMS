<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApartmentSpecification extends Model
{
    use SoftDeletes;
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'id' => 'integer',
        'utilities' => 'array',
    ];
    protected $fillable =[
        'asset_general_id',
        'name',
        'project_id',
        'total_floors',
        'nb_living_floor',
        'total_apartments',
        'first_floor',
        'last_floor',
        'apartments_per_floor',
        'rank_id',
        'nb_basement',
        'nb_elevator',
        'handover_year',
        'utilities',
        'apartment_name'
    ];
    public function rank():BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'rank_id', 'id');
    }
}
