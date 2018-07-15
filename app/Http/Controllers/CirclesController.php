<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CirclesController extends Controller
{
    public function __construct()
    {
        $this->items_per_page = config('circles.listing.items_per_page');
    }

    public function index()
    {
        $user = auth()->user();
        $model = \App\Circle::orderBy('id', 'desc');
        $model = $model->with(['memberships', 'users', 'user', 'messages']);
        $items = $model->paginate($this->items_per_page);

        return view('circles.index')->with([
            'items' => $items,
            'user' => $user,
        ]);
    }

    public function create()
    {
        $this->authorize('create', \App\Circle::class);
        return view('circles.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Circle::class);

        $this->validate($request, \App\Circle::validationRules());

        $user = auth()->user();

        $item = \App\Circle::createAndModify($user, $request);

        $redirect_route = 'circles.show';

        if ($user->moderator() == false) {
            $redirect_route = 'circles.membership.edit';
        }

        return redirect()->route($redirect_route, $item->uuid)->with([
            'success' => sprintf('%s was created!', (string) $item),
        ]);
    }

    public function show($uuid, Request $request)
    {
        $item = \App\Circle::withUuid($uuid)->with(
            ['users', 'user', 'memberships', 'messages']
        )->firstOrFail();

        $this->authorize('view', $item);

        $user = auth()->user();

        $timeTable = \App\TimeTable::forCircle($item, $user);

        $messages = $item->visibleMessages($user);

        return view('circles.show')->with([
            'item' => $item,
            'user' => $user,
            'membership' => $item->membershipOf($user),
            'timeTable' => $timeTable,
            'messages' => $messages
        ]);
    }

    public function edit($uuid, Request $request)
    {
        $item = \App\Circle::withUuid($uuid)->firstOrFail();

        $this->authorize('update', $item);

        return view('circles.edit')->with([
            'item' => $item,
        ]);
    }

    public function update($uuid, Request $request)
    {
        $item = \App\Circle::withUuid($uuid)->firstOrFail();

        $this->authorize('update', $item);

        $this->validate($request, \App\Circle::validationRules());

        $item->updateAndModify($request);

        return redirect()->route('circles.show', $item->uuid)->with([
            'success' => sprintf('%s was updated!', (string) $item),
        ]);
    }

    public function join($uuid, Request $request)
    {
        $item = \App\Circle::withUuid($uuid)->firstOrFail();

        $user = auth()->user();

        if ($item->joinable($user)) {
            $item->joinWithDefaults($user);
        } else {
            return redirect()->route('circles.show', $item->uuid)->withErrors(
                sprintf('You cannot join %s', (string) $item)
            );
        }

        return redirect()->route('circles.membership.edit', $item->uuid)->with([
            'success' => sprintf('You have joined %s!', (string) $item),
        ]);
    }

    public function remove($uuid, $user_uuid, Request $request)
    {
        $item = \App\Circle::withUuid($uuid)->firstOrFail();
        $user = \App\User::withUuid($user_uuid)->firstOrFail();

        $this->authorize('remove', $item);

        $item->leave($user);

        return redirect()->route('circles.show', $item->uuid)->with([
            'success' => sprintf('%s was removed from %s!', (string) $user, (string) $item),
        ]);
    }

    public function leave($uuid, Request $request)
    {
        $item = \App\Circle::withUuid($uuid)->firstOrFail();

        $user = auth()->user();

        $item->leave($user);

        return redirect()->route('circles.show', $item->uuid)->with([
            'success' => sprintf('You have left %s!', (string) $item),
        ]);
    }

    public function complete($uuid, Request $request)
    {
        $item = \App\Circle::withUuid($uuid)->firstOrFail();

        $this->authorize('complete', $item);

        $item->complete();

        return redirect()->route('circles.show', $item->uuid)->with([
            'success' => sprintf('%s is completed!', (string) $item),
        ]);
    }

    public function uncomplete($uuid, Request $request)
    {
        $item = \App\Circle::withUuid($uuid)->firstOrFail();

        $this->authorize('complete', $item);

        $item->uncomplete();

        return redirect()->route('circles.show', $item->uuid)->with([
            'success' => sprintf('%s is not completed!', (string) $item),
        ]);
    }

    public function destroy($uuid, Request $request)
    {
        $item = \App\Circle::withUuid($uuid)->firstOrFail();

        $this->authorize('delete', $item);

        if ($item->deletable()) {
            $item->delete();
        } else {
            return redirect()->route('circles.show', $item->uuid)->withErrors(
                sprintf('You cannot delete %s', (string) $item)
            );
        }

        return redirect()->route('circles.index')->with([
            'success' => sprintf('%s is deleted!', (string) $item),
        ]);
    }
}
