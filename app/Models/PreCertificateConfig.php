<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreCertificateConfig extends Model
{

    public $incrementing = false;


    protected $casts = [
        'id'        => 'integer',
    ];
    protected $fillable = [
        'name',
        'config',
    ];

}
