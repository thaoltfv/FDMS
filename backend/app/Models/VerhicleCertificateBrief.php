<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerhicleCertificateBrief extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
        'manufacturer_year' => 'integer',
        'using_year' => 'integer',
    ];
    protected $fillable = [
        'personal_property_id',
        'asset_type_id',
        'name',
        'description',
        'status',
        'transport_id',
        'vehicle_id',
        'manufacturer_id',
        'manufacturer_country_id',
        'model',
        'fuel_id',
        'manufacturer_year',
        'using_year',
    ];
    public function manufacturer()
    {
        return $this->belongsTo(Dictionary::class,'manufacturer_id','id');
    }
    public function manufacturerCountry()
    {
        return $this->belongsTo(Dictionary::class,'manufacturer_country_id','id');
    }
    public function fuel()
    {
        return $this->belongsTo(Dictionary::class,'fuel_id','id');
    }
    public function verhicle()
    {
        return $this->belongsTo(Dictionary::class,'vehicle_id','id');
    }
    public function transport()
    {
        return $this->belongsTo(Dictionary::class,'transport_id','id');
    }
    public function law():HasMany
    {
        return $this->hasMany(VerhicleCertificateBriefLaw::class,'verhicle_asset_id','id');
    }

    public function assetType():BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'asset_type_id','id');
    }

    public function price():HasOne
    {
        return $this->hasOne(VerhicleCertificateBriefPrice::class, 'verhicle_asset_id', 'id');
    }

    public function otherInfomation():HasOne
    {
        return $this->hasOne(VerhicleCertificateBriefLawInfo::class, 'verhicle_asset_id', 'id');
    }

    public function getDocumentDescriptionAttribute()
    {
        return 'Các hồ sơ, tài liệu về tài sản do khách hàng cung cấp là đầy đủ và tin cậy';
    }
    public function getStatusTextAttribute()
    {
        $status = $this->status;
		$statusText = "";
		switch ($status) {
            case 1:
                $statusText = "Mới";
                break;
			case 2:
				$statusText = "Đang thực hiện";
			    break;
			case 3:
				$statusText = "Đang Duyệt";
			    break;
			case 4:
				$statusText = "Hoàn Thành";
			    break;
			case 5:
				$statusText = "Huỷ";
			    break;
		}
        return $statusText;
    }
}
