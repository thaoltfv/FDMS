<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Support\TwoFactorAuthenticator;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'email'               => $this->email,
            'name'                => $this->name,
            'isActive'            => $this->is_active,
            'emailVerifiedAt'     => $this->email_verified_at,
            'locale'              => $this->locale,
            'createdAt'           => $this->created_at->format('Y-m-d\TH:i:s'),
            'updatedAt'           => $this->updated_at->format('Y-m-d\TH:i:s'),

        ];
    }
}
