<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentDictionary extends Model
{
    protected $casts = [
        'id' => 'integer',
    ];
    protected $fillable = [
        'type',
        'slug',
        'description',
        'value'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
