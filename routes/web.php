<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('{id}/leger-print', \App\Livewire\LegerPreview::class)->name('leger-print');
Route::get('{id}/leger-quran-print', \App\Livewire\LegerPreviewQuran::class)->name('leger-quran-print');

// report cover
Route::get('{id}/report-cover', [\App\Http\Controllers\ReportController::class, 'getDataCover'])->name('report-cover');
// report cover student
Route::get('{id}/report-cover-student', [\App\Http\Controllers\ReportController::class, 'getDataCoverStudent'])->name('report-cover-student');

// report half semester
Route::get('{id}/report-half-semester', [\App\Http\Controllers\ReportController::class, 'halfSemester'])->name('report-half-semester');
// report full semester
Route::get('{id}/report-full-semester', [\App\Http\Controllers\ReportController::class, 'fullSemester'])->name('report-full-semester');

// leger extracurricular
Route::get('{extracurricular_id}/leger-extracurricular', \App\Livewire\LegerPreviewExtracurricular::class)->name('leger-extracurricular');

// tes report
Route::get('/report-cover', function(){
    return view('components.reports.report-cover');
});

Route::get('/school-cover', function(){
    return view('components.reports.school-cover');
});

Route::get('/student-identity', function(){
    return view('components.reports.student-identity');
});
