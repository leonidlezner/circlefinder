<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $items = $user->circles()->paginate(10);

        return view('home')->with([
            'user' => $user,
            'items' => $items,
            'memberships' => $user->memberships()->with('circle')->get(),
        ]);
    }
}
