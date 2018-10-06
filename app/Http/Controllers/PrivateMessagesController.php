<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

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

        $items = App\PrivateMessage::where('recipient_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();

        $unreadAmount = $items->reduce(function ($carry, $item) {
            if (null === $item->read_at) {
                $carry++;
            }
            return $carry;
        }, 0);

        return view('privatemessages.index')->with([
            'user' => $user,
            'unreadAmount' => $unreadAmount,
            'items' => $items,
            'inbox' => true
        ]);
    }

    /**
     * Show message sent
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sent()
    {
        $this->authorize('sent', App\PrivateMessage::class);

        $user = auth()->user();

        $items = App\PrivateMessage::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();

        $unreadItems = App\PrivateMessage::where('recipient_id', Auth::id())
            ->where('read_at', null)
            ->get();

        return view('privatemessages.index')->with([
            'user' => $user,
            'unreadAmount' => $unreadItems->count(),
            'items' => $items,
            'inbox' => false
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
        $this->authorize('create', App\PrivateMessage::class);

        $replyToMessage = null;
        $recipient = App\User::where('uuid', $uuid)->firstOrFail();
        if (null != $replyUuid) {
            $replyToMessage = App\PrivateMessage::withUuid($replyUuid)->firstOrFail();
        }

        //$this->authorize('create', \App\PrivateMessage::class);
        return view('privatemessages.create')->with([
            'recipient' => $recipient,
            'replyToMessage' => $replyToMessage
        ]);
    }

    /**
     * Send private message
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
        $request->request->add(['recipient_id' => $user->id]);
        $this->validate($request, App\PrivateMessage::validationRules());
        App\PrivateMessage::create($request->all());

        return redirect()->route('private_messages.inbox')->with('success', 'Message sent successfully.');
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
        $privateMessage = App\PrivateMessage::withUuid($uuid)->firstOrFail();
        $this->authorize('read', [App\PrivateMessage::class, $privateMessage]);

        if (null === $privateMessage->read_at && Auth::id() !== $privateMessage->user_id) {
            $privateMessage->read_at = new \DateTime();
            $privateMessage->save();
        }

        return view('privatemessages.message')->with([
            'privateMessage' => $privateMessage,
            'auth_id' => Auth::id()
        ]);
    }
}
