<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModuleController;

Route::post('/modules', [ModuleController::class, 'store']);

Route::get('/modules/{id}/download', [ModuleController::class, 'download']);