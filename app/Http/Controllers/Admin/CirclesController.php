<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CirclesController extends Controller
{
    protected $items_per_page = 4;

    public function index()
    {
        $model = \App\Circle::orderBy('id', 'desc');
        $model = $model->with(['memberships', 'users', 'user']);
        $items = $model->paginate($this->items_per_page);
        
        return view('admin.circles.index')->with([
            'items' => $items,
        ]);
    }

    public function show($id, Request $request)
    {
        $item = \App\Circle::findOrFail($id);

        return view('admin.circles.show')->with([
            'item' => $item,
        ]);
    }
}
