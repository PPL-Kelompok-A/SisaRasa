<?php

namespace App\Http\Controllers;
use App\Models\Food;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $foods = Food::all();
        return view('daftarmenu.menu', compact('foods'));
    }

    public function show($id)
    {
        $food = Food::findOrFail($id);
        return view('daftarmenu.show', compact('food'));
    }
}
