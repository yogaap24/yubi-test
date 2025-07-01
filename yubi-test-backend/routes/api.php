<?php

use App\Http\Controllers\Api\SalesOrderController;
use Illuminate\Support\Facades\Route;

Route::apiResource('sales-orders', SalesOrderController::class);