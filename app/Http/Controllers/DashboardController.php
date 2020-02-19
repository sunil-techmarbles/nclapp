<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Load the view after login
    public function index()
    {
    	return view('admin.dashboard');
    }
}
