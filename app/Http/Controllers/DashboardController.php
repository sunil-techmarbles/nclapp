<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Load the view after login
    public function index()
    {
    	return view('admin.dashboard.dashboard');
    }

    public function itamgDashboardSection()
    {
		return view('admin.dashboard.itamgDashboard');
    }

    public function RefurbConnectDashboard()
    {
    	return view('admin.dashboard.RefurbConnectDashboard');
    }
}
