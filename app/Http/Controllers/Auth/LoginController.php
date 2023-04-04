<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\RoleRepository;
use App\Contracts\UserRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;


class LoginController extends Controller
{
    use ValidatesRequests;

    private UserRepository $userRepository;
    private RoleRepository $roleRepository;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->middleware('guest')->except('logout');
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    protected function login(LoginRequest $request): JsonResponse
    {
        try {
            $request = $request->toArray();
            $user = $this->userRepository->checkExitUserByEmail($request['email']);
            if ($user) {
                $user->token = $request['token'];
                unset($user['role']);
                $user->save();
                $response['status'] = 200;
                $response['token'] = $request['token'];
                $response['tokenType'] = 'Bearer';
                $response['user_id'] = $user->id;
                $response['user'] = $user;
                $response['permissions'] = $user->getPermissionsViaRoles()->pluck('name')->toArray();
                return $this->respondWithCustomData($response);
            }
            return $this->respondWithCustomData('Token invalidate', 401);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    protected function refreshToken(LoginRequest $request): JsonResponse
    {
        try {
            $request = $request->toArray();
            $user = $this->userRepository->checkExitUserByEmail($request['email']);
            if ($user) {
                $user->token = $request['token'];
                unset($user['role']);
                $user->save();
                $response['status'] = 200;
                $response['token'] = $request['token'];
                $response['tokenType'] = 'Bearer';
                $response['user_id'] = $user->id;
                $response['user'] = $user;
                $response['permissions'] = $user->getPermissionsViaRoles()->pluck('name')->toArray();
                return $this->respondWithCustomData($response);
            }
            return $this->respondWithCustomData('Token invalidate', 401);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $jwt = JWTAuth::getToken();
            $user = User::query()
                ->where('token', '=', $jwt)
                ->first();
            $user->token = null;
            $user->save();
            return $this->respondWithCustomData(['message' => 'Success']);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

}
