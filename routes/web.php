<?php

use App\Http\Controllers\ProcessLegerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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

// report project
Route::get('{id}/report-project', [\App\Http\Controllers\ReportController::class, 'project'])->name('report-project');
// report quran
Route::get('{id}/report-quran', [\App\Http\Controllers\ReportController::class, 'quran'])->name('report-quran');

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

Route::get('/leger-preview-my-grade', \App\Livewire\LegerPreviewMyGrade::class)->name('leger-preview-my-grade');

Route::get('transcript/preview', \App\Livewire\TranscriptPreview::class)->name('transcript-preview');


Route::group(['middleware' => ['role:admin']], function () { 
    // Route untuk ProcessLegerController
    Route::get('/leger/process', [ProcessLegerController::class, 'index'])->name('leger.index');
    Route::post('/leger/process', [ProcessLegerController::class, 'process'])->name('leger.process');
    
    // Route untuk leger process
    Route::post('/leger/set-academic-year', [ProcessLegerController::class, 'setAcademicYear'])->name('leger.set-academic-year');
    Route::get('/leger/get-teacher-subjects', [ProcessLegerController::class, 'getTeacherSubjects'])->name('leger.get-teacher-subjects');
});