<?php

namespace App\Http\Middleware;

use App\Enum\ErrorMessage;
use App\Http\ResponseTrait;
use App\Models\User;
use App\Repositories\EloquentUserRepository;
use App\Services\Firebase\FirebaseClient;
use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class CheckJWT extends BaseMiddleware
{
    use ResponseTrait;


    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        try {
            $jwt = JWTAuth::getToken();
            if ($jwt) {
                $users = User::query()
                    ->where('token', '=', $jwt)
                    ->first();
                $isExpiredToken = (new FirebaseClient())->isExpiredToken($jwt);
                if ($isExpiredToken) {
                    $data = ['message' => 'This token is expired.'];
                    return $this->respondWithErrorData($data, 401);
                }
                if (!$users) {
                    $data = ['message' => 'There are no users in the system.'];
                    return $this->respondWithErrorData($data, 401);
                }
            } else {
                $data = ['message' => 'Token invalidate'];
                return $this->respondWithErrorData($data, 401);
            }
            \Auth::login($users);
        } catch (Exception $exception) {
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
        return $next($request);
    }
}
