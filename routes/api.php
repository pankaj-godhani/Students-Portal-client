<?php

use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportTemplateController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StudentController;
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

Route::post('login', [HomeController::class, 'login']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('logout', [HomeController::class, 'logout'])->name('logout');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('students', [StudentController::class, 'index']);
    Route::post('students', [StudentController::class, 'store']);

    Route::post('/sessions', [SessionController::class, 'store']);
    Route::post('/sessions/{studentSession}/rate', [SessionController::class, 'rate']);
    Route::get('/students/{student}/sessions', [StudentController::class, 'sessions']);
    Route::post('/upload-docx', [FileUploadController::class, 'upload']);
    Route::post('/report-template', [ReportTemplateController::class, 'store']);
    Route::get('/report-template/{id}', [ReportTemplateController::class, 'show']);
    Route::get('/report-templates', [ReportTemplateController::class, 'getTemplates']);
    Route::post('/generate-report', [ReportTemplateController::class, 'generateReport']);

});
