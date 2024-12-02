<?php

namespace App\Http\Controllers;

use App\Contracts\UserRepository;
use App\Notifications\BroadcastNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return JsonResponse
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();

        $user->unreadNotifications->markAsRead();

        // Cache::tags('users:' . $user->id)->flush();

        return $this->respondWithCustomData(['message' => 'OK'], Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $ids = $request->get('read', []);
        $user = auth()->user();

        $user->unreadNotifications()->whereIn('id', $ids)->update(['read_at' => now()]);

        // Cache::tags('users:' . $user->id)->flush();

        return $this->respondWithCustomData([
            'message' => 'OK',
            'unread' => $user->unreadNotifications->count()
        ], Response::HTTP_OK);
    }

    public function sendNotification(Request $request): JsonResponse
    {
        $msg = $request->get('message', '');
        $count = 0;
        if ($msg != '') {
            $users = $this->userRepository->findAll();
            if (!empty($users)) {
                $count = count($users);
                foreach ($users as $user) {
                    $data = (object)[
                        'message' => $msg,
                    ];
                    $user->notify(new BroadcastNotification($data));
                }
            }
        }
        return new JsonResponse([
            'data' => "Successfully sent to $count users",
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ], 200);
    }
}
