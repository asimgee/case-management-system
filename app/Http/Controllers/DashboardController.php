<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function cases()
    {
        return view('cases');
    }

    public function clients()
    {
        return view('clients');
    }

    public function hearings()
    {
        return view('hearings');
    }

    public function documents()
    {
        return view('documents');
    }

    public function reports()
    {
        return view('reports');
    }

    public function settings()
    {
        return view('settings');
    }
}