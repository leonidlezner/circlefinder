<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TimezoneController extends Controller
{
    public function index()
    {
        return redirect(route('profile.timezone.edit'));
    }

    public function edit(Request $request)
    {
        $user = auth()->user();

        return view('profile.timezone.edit')->with([
            'item' => $user
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'timezone' => 'required|timezone',
        ]);

        $user = auth()->user();
        $user->timezone = $request->timezone;
        $user->save();

        return redirect()->intended(route('profile.show', $user->uuid))
            ->with("success", "Your timezone was changed to " . $user->timezone);
    }
}
