<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $orders = History::all();
        return view('riwayat.index', compact('orders'));
    }

    public function show($id)
    {
        $order = History::findOrFail($id);
        return view('riwayat.detail', compact('order'));
    }
}
