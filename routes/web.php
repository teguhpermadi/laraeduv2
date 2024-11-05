<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('{teacherSubject}/leger-print', \App\Livewire\LegerPreview::class)->name('leger-print');
