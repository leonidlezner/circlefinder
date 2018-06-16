<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    public function index()
    {
        return $this->show(auth()->user()->uuid);
    }

    public function show($uuid)
    {
        $user = \App\User::withUuid($uuid)->firstOrFail();
        
        return view('profile.profile.show')->with([
            'item' => $user
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

        return redirect()->route('profile.index')->with([
            'success' => 'Your profile was updated!'
        ]);
    }

    
}
