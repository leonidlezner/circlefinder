<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function show($id)
    {
        $user = auth()->user();
        $url = route('index');

        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        switch ($notification->type) {
            case 'App\Notifications\UserJoinedCircle':
            case 'App\Notifications\CircleCompleted':
            case 'App\Notifications\NewMessageInCircle':
                $url = array_get($notification, 'data.circle_url');
                break;
            default:
                continue;
        }

        $notification->markAsRead();

        return redirect($url);
    }
}
