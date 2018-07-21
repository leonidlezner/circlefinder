<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    protected $items_per_page = 10;

    public function index()
    {
        $model = \App\Message::orderBy('id', 'desc')->with(['user', 'circle']);
        $items = $model->paginate($this->items_per_page);

        return view('admin.messages.index')->with([
            'items' => $items,
        ]);
    }

    public function show($id, Request $request)
    {
        $item = \App\Message::findOrFail($id);

        return view('admin.messages.show')->with([
            'item' => $item,
        ]);
    }
}
