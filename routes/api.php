<?php

use App\Http\Controllers\Api\ApiChartController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\BodEventController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/chart-data', [ApiChartController::class, 'chartData']);
Route::middleware(['role:Superadmin'])->group(function () {
    Route::get('/dashboard/total-team-chart-data', [DashboardController::class, 'getTotalTeamChartData'])->name('getTotalTeamChartData');
    Route::get('/dashboard/total-benefit-chart-data', [DashboardController::class, 'showTotalBenefitChartData'])->name('showTotalBenefitChartData');
    Route::get('/dashboard/total-potential-benefit-chart-data', [DashboardController::class, 'showTotalPotentialBenefitChartData'])->name('showTotalPotentialBenefitChartData');
    Route::get('/dashboard/financial-benefit', [DashboardController::class, 'getFinancialBenefitsByCompany']);
});




Route::get('/comments/by-paper', [CommentController::class, 'getCommentsByPaper']);
