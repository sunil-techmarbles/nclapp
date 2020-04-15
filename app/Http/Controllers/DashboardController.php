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
        $action = 'itamgconnect';
		return view('admin.dashboard.itamgDashboard', compact('action'));
    }

    public function RefurbConnectDashboard()
    {
        $action = 'refurbconnect';
    	return view('admin.dashboard.RefurbConnectDashboard', compact('action'));
    }
}
