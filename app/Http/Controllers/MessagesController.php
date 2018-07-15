<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function store($circle_uuid, Request $request)
    {
        $circle = \App\Circle::withUuid($circle_uuid)->firstOrFail();

        $user = auth()->user();

        $this->authorize('create', [\App\Message::class, $circle]);

        $this->validate($request, \App\Message::validationRules());

        $message = $circle->storeMessage($user, $request->body, $request->get('show_to_all', false));

        if ($message) {
            return redirect()->route('circles.show', $circle->uuid)->with([
                'success' => sprintf('Comment was posted!'),
            ]);
        } else {
            return redirect()->route('circles.show', $circle->uuid)->withErrors(
                sprintf('Comment could not be posted!')
            );
        }
    }

    public function update($circle_uuid, $uuid, Request $request)
    {
        $message = \App\Message::withUuid($uuid)->firstOrFail();
        
        $circle = $message->circle;

        $user = auth()->user();
        
        $this->authorize('update', $message);
        
        $this->validate($request, \App\Message::validationRules());

        $message->body = $request->body;

        $message->show_to_all = $request->get('show_to_all', false);
        
        $message->save();
        
        if ($request->ajax()) {
            return response()->json(['status' => 'success']);
        } else {
            return redirect()->route('circles.show', $circle->uuid)->with([
                'success' => sprintf('Comment was updated!'),
            ]);
        }
    }

    public function destroy($circle_uuid, $uuid, Request $request)
    {
        $message = \App\Message::withUuid($uuid)->firstOrFail();

        $circle = $message->circle;

        $this->authorize('delete', $message);
        
        $message->delete();

        return redirect()->route('circles.show', $circle->uuid)->with([
            'success' => sprintf('Comment was deleted!'),
        ]);
    }
}
