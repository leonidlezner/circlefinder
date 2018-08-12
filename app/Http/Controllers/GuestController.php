<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('circles.index');
        }

        $model = \App\Circle::orderBy('id', 'desc');
        $model = $model->with(['memberships', 'languages']);
        $items = $model->paginate(9);

        return view('guest.index')->with([
            'items' => $items,
        ]);
    }
}
