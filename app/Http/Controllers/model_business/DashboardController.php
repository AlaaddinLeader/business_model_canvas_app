<?php

namespace App\Http\Controllers\model_business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /***
     * Show the dashboard for the authenticated user
     */
    public function showDashboard(){
        return view('pages.dashboard');
    }
}
