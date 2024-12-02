<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class PreCertificateConfig extends Model
{
    use SoftDeletes;
    public $incrementing = false;


    protected $casts = [
        'id'        => 'integer',
    ];
    protected $fillable = [
        'name',
        'config',
        'old_config',
    ];
}
