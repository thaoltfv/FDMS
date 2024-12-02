<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $is_active
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User permission($permissions)
 * @method static Builder|User query()
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User whereAntiPhishingCode($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailTokenConfirmation($value)
 * @method static Builder|User whereEmailTokenDisableAccount($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereGoogle2faEnable($value)
 * @method static Builder|User whereGoogle2faSecret($value)
 * @method static Builder|User whereGoogle2faUrl($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIsActive($value)
 * @method static Builder|User whereLocale($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    use HasRoles;
    use Notifiable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
    ];

    protected $hidden = [
        'branch_id',
        'role',
        'deleted_at',
        'token'
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'birthday',
        'image',
        'branch_id',
        'mailing_address',
        'appraisers_number',
        'role',
        'token',
        'created_at',
        'customer_group_id',
        'is_guest',
        'name_lv_1',
        'name_lv_2',
        'name_lv_3',
        'name_lv_4',
        'first_id',
        'second_id',
        'third_id',
        'fourth_id',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function appraiser(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'id', 'user_id');
    }
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'customer_group_id', 'id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
