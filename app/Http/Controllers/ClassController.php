<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;

class ClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::all();

        return view('classes.index', compact('classes'));
    }
}