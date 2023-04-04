<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateApartmentPic extends Model
{
    use SoftDeletes;
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $casts =[
        'id' => 'integer',
    ];
    protected $fillable = [
        'apartment_asset_id',
        'link',
        'type_id',
        'description'
    ];
    public function picType():BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'type_id', 'id');
    }
}
