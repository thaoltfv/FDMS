<?php

namespace App\Http\Controllers;

use App\Http\ResponseTrait;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use ResponseTrait;

    /**
     * Get the validator message.
     *
     * @return array
     */
    protected array $messages= [
        'required' => 'Vui lòng nhập thông tin :attribute.',
        'min' => 'Thông tin :attribute phải lớn hơn :min',
        'max' => 'Thông tin :attribute phải nhỏ hơn :max',
        'numeric' => 'Thông tin :attribute phải là kiểu số',
        'integer' => 'Thông tin :attribute phải là kiểu số',
        'string' => 'Thông tin :attribute phải là kiểu chữ',
        'boolean' => 'Thông tin :attribute phải là true/false',
        'required_if' => 'Vui lòng nhập thông tin :attribute.',
        'in'=>":attribute này không tồn tại",
        'required_unless' => 'Vui lòng nhập thông tin :attribute',
        'required_with' => 'Vui lòng nhập thông tin :attribute',
        ];
    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap(): array
    {
        return [
            'index' => 'viewAny',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'update',
            'update' => 'update',
            'destroy' => 'delete',
        ];
    }

    /**
     * @param null $permission
     * @return bool
     */
    protected function getUserPermission($permission = null): bool
    {
        $jwt = JWTAuth::getToken();
        $user = User::query()
            ->where('token', '=', $jwt)
            ->first();
        $permissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
        if (in_array($permission, $permissions)) {
            return true;
        }
        return false;
    }
}
