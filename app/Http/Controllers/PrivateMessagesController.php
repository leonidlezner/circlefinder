<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrivateMessagesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show message inbox
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function inbox()
    {
        $this->authorize('inbox', App\PrivateMessage::class);

        $user = auth()->user();
        
        $items = App\PrivateMessage::where('conversation', null)
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            })
            ->with('recipient', 'sender')
            ->orderBy('created_at', 'desc')
            ->get();

            /*
        $unreadAmount = $items->reduce(function ($carry, $item) use ($user) {
            if (null === $item->read_at && $user->id == $item->recipient_id) {
                $carry++;
            }
            return $carry;
        }, 0);*/
        $unreadAmount = 0;

        return view('privatemessages.index')->with([
            'user' => $user,
            'unreadAmount' => $unreadAmount,
            'items' => $items
        ]);
    }

    /**
     * Create private message form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($uuid, $replyUuid = null)
    {
        $replyToMessage = null;
        
        $recipient = App\User::where('uuid', $uuid)->firstOrFail();

        if (null != $replyUuid) {
            $replyToMessage = App\PrivateMessage::withUuid($replyUuid)->firstOrFail();
        }

        $this->authorize('create', [\App\PrivateMessage::class, $replyToMessage]);

        return view('privatemessages.create')->with([
            'recipient' => $recipient,
            'replyToMessage' => $replyToMessage
        ]);
    }

    /**
     * Send new private message
     *m
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function send($uuid, Request $request)
    {
        $this->authorize('send', App\PrivateMessage::class);
        
        $user = App\User::withUuid($uuid)->firstOrFail();
        
        $request->request->add([
            'recipient_id' => $user->id,
            'conversation' => null,
        ]);
        
        $this->validate($request, App\PrivateMessage::validationRules());

        App\PrivateMessage::create($request->all());

        return redirect()->route('private_messages.inbox')->with('success', 'Message sent successfully.');
    }

/**
     * Send private message reply
     *m
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sendReply($uuid, $replyUuid, Request $request)
    {
        $replyToMessage = App\PrivateMessage::withUuid($replyUuid)->firstOrFail();
        
        $this->authorize('send', [App\PrivateMessage::class, $replyToMessage]);

        if ($replyToMessage->conversation !== null) {
            $conversation = $replyToMessage->conversation;
        } else {
            $conversation = $replyToMessage->id;
        }

        $request->request->add([
            'recipient_id' => $replyToMessage->user_id,
            'conversation' => $conversation,
        ]);
        
        $this->validate($request, App\PrivateMessage::validationRules());

        App\PrivateMessage::create($request->all());

        return redirect()->route('private_messages.inbox')->with('success', 'Reply sent successfully.');
    }

    /**
     * Read private message
     *
     * @param         $uuid
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function read($uuid, Request $request)
    {
        $conversation = App\PrivateMessage::withUuid($uuid)->firstOrFail();
        
        $this->authorize('read', [App\PrivateMessage::class, $conversation]);

        #TODO: change the state unread of the conversation

        $privateMessages = App\PrivateMessage::where('conversation', $conversation->id)
            ->orWhere(function ($query) use ($conversation) {
                $query->where('conversation', null)->where('id', $conversation->id);
            })
            ->with('recipient', 'sender')
            ->orderBy('created_at', 'asc')
            ->get();

        /*
        if (null === $privateMessage->read_at && Auth::id() !== $privateMessage->user_id) {
            $privateMessage->read_at = new \DateTime();
            $privateMessage->save();
        }*/

        return view('privatemessages.conversation')->with([
            'privateMessages' => $privateMessages,
            'conversation' => $conversation,
            'auth_id' => Auth::id()
        ]);
    }
}
