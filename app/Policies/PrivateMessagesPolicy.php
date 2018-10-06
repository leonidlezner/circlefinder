<?php

namespace App\Policies;

use App\PrivateMessage;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrivateMessagesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can read the inbox.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function inbox(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can read the sentbox.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function sent(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create the message.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user, $replyToMessage = null)
    {
        if ($replyToMessage) {
            return $replyToMessage->recipient_id == $user->id;
        }

        return true;
    }

    /**
     * Determine whether the user can send the message.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function send(User $user, $replyToMessage = null)
    {
        if ($replyToMessage) {
            return $replyToMessage->recipient_id == $user->id;
        }
                
        return true;
    }

    /**
     * Determine whether the user can read the message.
     *
     * @param  \App\User $user
     * @param PrivateMessage $privateMessage
     * @return mixed
     */
    public function read(User $user, PrivateMessage $privateMessage)
    {
        return $privateMessage->user_id == $user->id || $privateMessage->recipient_id == $user->id;
    }
}
