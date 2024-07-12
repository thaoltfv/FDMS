<?php

namespace App\Services\Firebase;

use App\Http\ResponseTrait;
use App\Repositories\EloquentUserRepository;
use App\Models\User;
use App\Services\CommonService;
use Firebase\Auth\Token\Exception\ExpiredToken;
use Firebase\Auth\Token\Exception\InvalidSignature;
use Firebase\Auth\Token\Exception\InvalidToken;
use Firebase\Auth\Token\Exception\IssuedInTheFuture;
use Firebase\Auth\Token\Exception\UnknownKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Kreait\Firebase\Exception\Auth\RevokedIdToken;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;
use \Kreait\Firebase\Auth\SignIn\FailedToSignIn;
use \Kreait\Firebase\Exception\InvalidArgumentException;
use \Kreait\Firebase\Exception\Auth\InvalidPassword;
use Tymon\JWTAuth\Facades\JWTAuth;


class FirebaseClient
{
    use ResponseTrait;

    private Factory $factory;

    /**
     * FirebaseClient constructor.
     */
    public function __construct()
    {
        $this->setFirebaseClient();
    }

    /**
     * @return FirebaseClient
     */
    public function setFirebaseClient(): FirebaseClient
    {
        $this->factory = (new Factory)->withServiceAccount(base_path() . '/firebase_credentials.json');
        return $this;
    }

    /**
     * @return Factory
     */
    public function getFirebaseClient(): Factory
    {
        return $this->factory;
    }

    /**
     * @param $objects
     * @return array
     */
    public function authenticate($objects): array
    {
        try {
            $auth = $this->getFirebaseClient()->createAuth();
            $signIsinResult = $auth->signInWithEmailAndPassword($objects['email'], $objects['password']);
            return [
                'status' => 200,
                'token' => $signIsinResult->idToken(),
                'refreshToken' => $signIsinResult->refreshToken(),
                'tokenType' => 'Bearer',
                'expiresIn' => $signIsinResult->ttl(),
            ];
        } catch (InvalidPassword | InvalidArgumentException | FailedToSignIn $exception) {
            return [
                'status' => 400,
                'message' => $exception->getMessage()
            ];
        }
    }

    /**
     * @param $objects
     * @return array
     */
    public function refreshToken($objects): array
    {
        try {
            $auth = $this->getFirebaseClient()->createAuth();
            $signIsinResult = $auth->signInWithRefreshToken($objects['refreshToken']);
            return [
                'status' => 200,
                'token' => $signIsinResult->idToken(),
                'refreshToken' => $signIsinResult->refreshToken(),
                'tokenType' => 'Bearer',
                'expiresIn' => $signIsinResult->ttl(),
            ];
        } catch (InvalidPassword | InvalidArgumentException | FailedToSignIn $exception) {
            return [
                'status' => 400,
                'message' => $exception->getMessage()
            ];
        }
    }

    /**
     * @param $token
     * @return array|JsonResponse
     * @throws AuthException
     * @throws FirebaseException
     */
    public function verifyIdToken($token)
    {
        try {
            $auth = $this->getFirebaseClient()->createAuth();
            $verifiedIdToken = $auth->verifyIdToken($token, true);
            $uid = $verifiedIdToken->claims()->get('sub');
            $user = $auth->getUser($uid);
            return (array)$user;
        } catch (ExpiredToken | IssuedInTheFuture | InvalidSignature | UnknownKey | InvalidToken | RevokedIdToken $exception) {
            return $this->respondWithCustomData(['message' => $exception->getMessage()], 401);
        }
    }

    /**
     * @param $token
     * @return bool|JsonResponse
     */
    public function isExpiredToken($token)
    {
        try {
            $auth = $this->getFirebaseClient()->createAuth();
            $verifiedIdToken = $auth->parseToken($token);
            return $verifiedIdToken->isExpired(Carbon::now());
        } catch (ExpiredToken | IssuedInTheFuture | InvalidSignature | UnknownKey | InvalidToken | RevokedIdToken $exception) {
            return $this->respondWithCustomData(['message' => $exception->getMessage()], 401);
        }
    }

    public function disableUser($email)
    {
        try {
            $auth = $this->getFirebaseClient()->createAuth();
            $user = $auth->getUserByEmail($email);
            $uid = $user->uid;
            $user = $auth->disableUser($uid);
            return (array)$user;
        } catch (AuthException | FirebaseException $exception) {
            return $this->respondWithCustomData(['message' => $exception->getMessage()], 401);
        }
    }

    public function enableUser($email)
    {
        try {
            $auth = $this->getFirebaseClient()->createAuth();
            $user = $auth->getUserByEmail($email);
            $uid = $user->uid;
            $user = $auth->enableUser($uid);
            return (array)$user;
        } catch (AuthException | FirebaseException $exception) {
            return $this->respondWithCustomData(['message' => $exception->getMessage()], 401);
        }
    }

    /**
     * @return JsonResponse
     * @throws AuthException
     * @throws FirebaseException
     */
    public function logOut(): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();
            $auth = $this->getFirebaseClient()->createAuth();
            $verifiedIdToken = $auth->verifyIdToken($token);
            $uid = $verifiedIdToken->claims()->get('sub');
            $auth->revokeRefreshTokens($uid);
            return $this->respondWithCustomData(['message' => 'Success']);
        } catch (ExpiredToken | IssuedInTheFuture | InvalidSignature | UnknownKey | InvalidToken | RevokedIdToken $exception) {
            return $this->respondWithCustomData(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getUser(): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();
            $auth = $this->getFirebaseClient()->createAuth();
            $verifiedIdToken = $auth->verifyIdToken($token);
            $uid = $verifiedIdToken->claims()->get('sub');
            $user = $auth->getUsers([$uid]);
            return $this->respondWithCustomData($user);
        } catch (ExpiredToken | FirebaseException | InvalidSignature | UnknownKey | InvalidToken | RevokedIdToken $exception) {
            return $this->respondWithCustomData(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * @param $email
     * @param $password
     * @return JsonResponse
     */
    public function createUser($email, $password): JsonResponse
    {
        try {
            $auth = $this->getFirebaseClient()->createAuth();
            $user = $auth->createUserWithEmailAndPassword($email, $password);
            $data = [
                'subject' => 'TẠO MỚI TÀI KHOẢN THÀNH CÔNG',
                'message' => 'Tài khoản của bạn trên phần mềm quản lý doanh nghiệp thẩm định giá nova.fastvalue.vn đã được khởi tạo thành công.',
                'email' => $email,
                'new_password' => $password,
                'is_create' => true,
                'is_reset_pass' => false,
            ];
            CommonService::callNotificationReset([], $data);
            return $this->respondWithCustomData($user);
        } catch (FirebaseException | AuthException | UnknownKey | InvalidToken | RevokedIdToken $exception) {
            return $this->respondWithCustomData(['message' => $exception->getMessage()], 400);
        }
    }

    public function changeUserPassword($newPassword): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();
            $auth = $this->getFirebaseClient()->createAuth();
            $verifiedIdToken = $auth->verifyIdToken($token);
            $uid = $verifiedIdToken->claims()->get('sub');
            $user = $auth->changeUserPassword($uid, $newPassword);
            return $this->respondWithCustomData($user);
        } catch (FirebaseException | AuthException | UnknownKey | InvalidToken | RevokedIdToken $exception) {
            return $this->respondWithCustomData(['message' => $exception->getMessage()], 400);
        }
    }

    // public function resetUserPassword($email,$defaultPassword): JsonResponse
    // {
    //     try {
    //         $auth = $this->getFirebaseClient()->createAuth();
    //         $user = $auth->getUserByEmail($email);
    //         $uid = $user->uid;
    //         $user = $auth->changeUserPassword($uid, $defaultPassword);
    //         return $this->respondWithCustomData($user);
    //     } catch ( FirebaseException | AuthException | UnknownKey | InvalidToken | RevokedIdToken $exception) {
    //         return $this->respondWithCustomData(['message' => $exception->getMessage()], 400);
    //     }
    // }
    public function resetUserPassword($id, $defaultPassword): JsonResponse
    {
        try {
            $loginUser = CommonService::getUser();
            $getUser = User::query()->where('id', $id)->first();
            if (isset($getUser)) {
                $email = $getUser->email;
                $auth = $this->getFirebaseClient()->createAuth();
                $user = $auth->getUserByEmail($email);
                $uid = $user->uid;
                $user = $auth->changeUserPassword($uid, $defaultPassword);
                $eloquenUser = new EloquentUserRepository(new User());
                $userSend = $eloquenUser->getUser($id);
                $data = [
                    'subject' => 'CẤP LẠI MẬT KHẨU',
                    'message' => 'Tài khoản ' . $email . ' trên hệ thống FastValue đã được đặt lại mật khẩu thành "' . $defaultPassword . '". Vui lòng đăng nhập vào hệ thống và tiến hành đổi lại mật khẩu mới để đảm bảo an toàn.',
                    'email' => $email,
                    'new_password' => $defaultPassword,
                    'is_create' => false,
                    'is_reset_pass' => true,
                ];

                CommonService::callNotificationReset([$userSend], $data);
                return $this->respondWithCustomData($user);
            }
        } catch (FirebaseException | AuthException | UnknownKey | InvalidToken | RevokedIdToken $exception) {
            return $this->respondWithCustomData(['message' => $exception->getMessage()], 400);
        }
    }
}
