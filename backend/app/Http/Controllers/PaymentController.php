<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function success(Request $request) {
        Log::critical($request->all());
        return response()->json();
    }

    public function fail(Request $request) {
        Log::critical($request->all());
        return response()->json();
    }
}
