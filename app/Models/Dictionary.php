<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;

/**
 * Class Province
 * @package App\Models
 */
class Dictionary extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'type',
        'status',
        'acronym',
        'description',
        'useful_year',
        'created_by',
        'dictionary_acronym',
        'name_lv_1',
        'name_lv_2',
        'name_lv_3',
        'name_lv_4'
        // 'description_capitalize',
    ];
    // protected $appends = [
    //     'description_capitalize',
    // ];
    public function childDictionaries(): HasMany
    {
        return $this->hasMany(Dictionary::class, 'dictionary_acronym', 'acronym');
    }
    protected function getDescriptionCapitalizeAttribute()
    {
        return ucfirst(mb_strtolower($this->description));
    }
}
