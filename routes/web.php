<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('{id}/leger-print', \App\Livewire\LegerPreview::class)->name('leger-print');

// report cover
Route::get('{id}/report-cover', [\App\Http\Controllers\ReportController::class, 'getDataCover'])->name('report-cover');
// report cover student
Route::get('{id}/report-cover-student', [\App\Http\Controllers\ReportController::class, 'getDataCoverStudent'])->name('report-cover-student');
// leger extracurricular
Route::get('{extracurricular_id}/leger-extracurricular', \App\Livewire\LegerPreviewExtracurricular::class)->name('leger-extracurricular');

