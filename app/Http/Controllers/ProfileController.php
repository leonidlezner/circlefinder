<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return redirect(route('profile.show', [auth()->user()->uuid]));
    }

    public function show($uuid)
    {
        $user = \App\User::withUuid($uuid)->with(['circles'])->firstOrFail();

        return view('profile.profile.show')->with([
            'item' => $user,
            'circles' => $user->circles,
            'memberships' => $user->memberships()->with('circle')->get(),
            'profiles' => $user->profiles(),
        ]);
    }

    public function edit()
    {
        $user = auth()->user();
        
        return view('profile.profile.edit')->with([
            'item' => $user
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validationRules = \App\User::validationRules(['roles']);
        $validationRules['email'] .= ','.$user->id;

        $this->validate($request, $validationRules);

        $user->update($request->all());

        return redirect()->route('profile.show', $user->uuid)->with([
            'success' => 'Your profile was updated!'
        ]);
    }
}
