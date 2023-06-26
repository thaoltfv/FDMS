<?php

namespace App\Http\Controllers\Dictionary;

use App\Contracts\DictionaryRepository;
use App\Contracts\UserRepository;
use App\Enum\ErrorMessage;
use App\Enum\PermissionsDefault;
use App\Enum\ScreensDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dictionary\DictionaryRequest;
use App\Models\AppraiserCompany;
use App\Services\Firebase\FirebaseClient;
use Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use phpDocumentor\Reflection\Type;
use Ramsey\Uuid\Uuid;
use Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class DictionaryController extends Controller
{

    private DictionaryRepository $dictionaryRepository;
    private UserRepository $userRepository;


    /**
     * ProvinceController constructor.
     */
    public function __construct(DictionaryRepository $dictionaryRepository,
                                UserRepository       $userRepository)
    {
        $this->dictionaryRepository = $dictionaryRepository;
        $this->userRepository = $userRepository;

    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->dictionaryRepository->findPaging());
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
            return Cache::remember('DICTIONARIES_ALL', 86000, function() {
                return $this->respondWithCustomData($this->dictionaryRepository->findAll());
            });
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }


    /**
     * @param $type
     * @return JsonResponse
     */
    public function show($type): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->dictionaryRepository->findByType($type));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $type
     * @return JsonResponse
     */
    public function findAllByType($type): JsonResponse
    {
        try {
            return Cache::remember('DICTIONARY_' . $type, 86000, function() use ($type) {
                return $this->respondWithCustomData($this->dictionaryRepository->findAllByType($type));
            });
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param DictionaryRequest $request
     * @return JsonResponse
     */
    public function store(DictionaryRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::ADD_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                $jwt = JWTAuth::getToken();
                $user = $this->userRepository->validateToken($jwt);
                $request['created_by'] = $user->name;
                return $this->respondWithCustomData($this->dictionaryRepository->createDictionary($request->toArray()));
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
     * @param DictionaryRequest $request
     * @return JsonResponse
     */
    public function update($id, DictionaryRequest $request): JsonResponse
    {
        try {
            if ($this->getUserPermission(PermissionsDefault::EDIT_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->dictionaryRepository->updateDictionary($id, $request->toArray()));
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
            if ($this->getUserPermission(PermissionsDefault::DELETE_PERMISSION . '_' . ScreensDefault::CATEGORY_SCREEN)) {
                return $this->respondWithCustomData($this->dictionaryRepository->deleteDictionary($id));
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
    public function uploadCompanyLogoImage(Request $request): JsonResponse
    {
        try {
            $company = AppraiserCompany::query()->get('acronym')->first()->acronym;
            $image = $request->file('image');
            $path = env('STORAGE_IMAGES') .'/';
            $name = $path . 'company_logo.png';
            Storage::disk('public')->put($name, file_get_contents($image));
            $fileUrl = Storage::disk('public')->url($name);
            return $this->respondWithCustomData(['link' => $fileUrl, 'picture_type' => $image->extension()]);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::UPLOAD_IMAGE_ERROR, 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadLocalImage(Request $request): JsonResponse
    {
        try {
            $image = $request->file('image');
            $path = env('STORAGE_IMAGES') .'/' .'avatar/';
            $name = $path . Uuid::uuid4()->toString() . '.png';
            Storage::disk('public')->put($name, file_get_contents($image), 'public');
            $fileUrl = Storage::disk('public')->url($name);
            return $this->respondWithCustomData(['link' => $fileUrl, 'picture_type' => $image->extension()]);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::UPLOAD_IMAGE_ERROR, 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
}
