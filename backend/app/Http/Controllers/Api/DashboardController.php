<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboard) {}

    public function stats()
    {
        return response()->json($this->dashboard->stats());
    }
}