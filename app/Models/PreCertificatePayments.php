<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class PreCertificatePayments extends Model
{
    use SoftDeletes;
    protected $table = 'pre_certificate_payments';
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'pre_certificate_id',
        'pay_date',
        'created_by',
        'amount',
        'for_payment_of',
        'certificate_id'
    ];
	
	public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
