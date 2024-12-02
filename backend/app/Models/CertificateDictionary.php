<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateDictionary extends Model
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
        'created_by'
    ];
}
