<?php

namespace App\Http\Controllers\User;

use App\Contracts\RoleRepository;
use App\Contracts\UserRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\RoleDefault;
use App\Enum\ScreensDefault;
use App\Enum\ValueDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Requests\User\UserRequest;
use App\Services\Firebase\FirebaseClient;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

class UserController extends Controller
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;

    /**
     * UserController constructor.
     */
    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->userRepository->findPaging());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->userRepository->getUser($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @return JsonResponse
     */
    public function findAll(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->userRepository->findAll());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function profile($id): JsonResponse
    {
        try {
            $user = $this->userRepository->getUser($id);
            $data = array();
            $data['user'] = $user;
            $data['permissions'] = $user->getPermissionsViaRoles()->pluck('name')->toArray();
            $data['unreadNotifications'] = $user->unreadNotifications->count();
            return $this->respondWithCustomData($data);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
    public function getUnreadNotifications($id): JsonResponse
    {
        try {
            $user = $this->userRepository->getUser($id);
            $unreadNotificationsCount = $user->unreadNotifications->count();
            return $this->respondWithCustomData(['unreadNotifications' => $unreadNotificationsCount]);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
    /**
     * @param $id
     * @return JsonResponse
     */
    public function notifications($id): JsonResponse
    {
        try {
            $user = $this->userRepository->getUser($id);
            $data = array();
            $data['notifications'] = $user->notifications;
            return $this->respondWithCustomData($data);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::USER_SCREEN)) {
                $data = $request->toArray();
                $user = $this->userRepository->createUser($data);
                (new FirebaseClient())->createUser($request['email'], $request['password']);
                return $this->respondWithCustomData($user);
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function update($id, UserRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::USER_SCREEN)) {
                $data = $request->toArray();
                return $this->respondWithCustomData($this->userRepository->updateUser($id, $data, $data['role']));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::DELETE_PERMISSION . '_' . ScreensDefault::USER_SCREEN)) {
                $user = $this->userRepository->getUser($id);
                $result = (new FirebaseClient())->disableUser($user->email);
                return $this->respondWithCustomData($this->userRepository->deleteUser($id));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeUserInExcel(Request $request): JsonResponse
    {
        try {
            $defaultPassword = 'ThamDinh' . ucfirst(env('DOCUMENT_MODULE', '')) . '@' .  Carbon::now()->format('Y');
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::USER_SCREEN)) {
                if ($request->hasFile('import_file')) {
                    $path = $request->file('import_file')->getRealPath();
                    $data = (new FastExcel())->import($path);
                    if (!empty($data) && $data->count()) {
                        foreach ($data->toArray() as $key => $value) {
                            if (!empty($value)) {
                                $password = ($value['Password'] && $value['Password'] != '') ? $value['Password'] : $defaultPassword;
                                $user = [
                                    'id' => Uuid::uuid4()->toString(),
                                    'email' => $value['Email'],
                                    'name' => ($value['Last name'] ?? '') . ' ' . ($value['First name'] ?? ''),
                                    'phone' => $value['Phone number'] ?? '',
                                    'address' => $value['Address'] ?? '',
                                    'mailing_address' => $value['mailing_address'] ?? '',
                                    'role' => RoleDefault::USER['role'],
                                ];
                                $exitUser = $this->userRepository->checkExitUserByEmail($user['email']);
                                if (!$exitUser) {
                                    (new FirebaseClient())->createUser($user['email'], $password);
                                    $this->userRepository->createUser($user);
                                } else {
                                    (new FirebaseClient())->createUser($user['email'], $password);
                                    $this->userRepository->updateUser($exitUser->id, $user, RoleDefault::USER['role']);
                                }
                            }
                        }
                    }
                }
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }

            return $this->respondWithCustomData($this->userRepository->findPaging());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changeUserPassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::USER_SCREEN)) {
                if ($request->confirm_new_password == $request->new_password) {
                    return (new FirebaseClient())->changeUserPassword($request->new_password);
                } else {
                    $data = ['message' => ErrorMessage::CHANGE_PASSWORD_ERROR];
                    return $this->respondWithErrorData($data);
                }
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetUserPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $defaultPassword = 'ThamDinh' . ucfirst(env('DOCUMENT_MODULE', '')) . '@' .  Carbon::now()->format('Y');
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::USER_SCREEN)) {
                return (new FirebaseClient())->resetUserPassword($request->email, $defaultPassword);
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /** 
     * @param $id
     * @return JsonResponse
     */
    public function deactiveUser($id): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::DELETE_PERMISSION . '_' . ScreensDefault::USER_SCREEN)) {
                $user = $this->userRepository->getUser($id);
                $result = (new FirebaseClient())->disableUser($user->email);
                return $this->respondWithCustomData($this->userRepository->deactiveUser($id));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /** 
     * @param $id
     * @return JsonResponse
     */
    public function activeUser($id): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::DELETE_PERMISSION . '_' . ScreensDefault::USER_SCREEN)) {
                $user = $this->userRepository->getUser($id);
                $result = (new FirebaseClient())->enableUser($user->email);
                return $this->respondWithCustomData($this->userRepository->activeUser($id));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /** 
     * @param $id
     * @return JsonResponse
     */
    public function isntLegalUser($id): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::DELETE_PERMISSION . '_' . ScreensDefault::USER_SCREEN)) {
                return $this->respondWithCustomData($this->userRepository->isntLegalUser($id));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /** 
     * @param $id
     * @return JsonResponse
     */
    public function isLegalUser($id): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::DELETE_PERMISSION . '_' . ScreensDefault::USER_SCREEN)) {
                return $this->respondWithCustomData($this->userRepository->isLegalUser($id));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /** 
     * @param $id
     * @return JsonResponse
     */
    public function resetUserPasswordNew($id): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::DELETE_PERMISSION . '_' . ScreensDefault::USER_SCREEN)) {
                return $this->respondWithCustomData($this->userRepository->resetUserPasswordNew($id));
            } else {
                $data = ['message' => ErrorMessage::PERMISSION_ERROR];
                return $this->respondWithErrorData($data, 403);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
}
