<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes for countries and plans
Route::get('/countries', [ApiController::class, 'countries']);
Route::get('/countries/{slug}/plans', [ApiController::class, 'countryPlans']);
Route::get('/regions', [ApiController::class, 'regions']);
Route::get('/regions/{slug}/plans', [ApiController::class, 'regionPlans']);
Route::get('/search/countries', [ApiController::class, 'searchCountries']);
Route::get('/plans', [ApiController::class, 'allPlans']);
Route::get('/plans/{id}', [ApiController::class, 'planDetails']);
