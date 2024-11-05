<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('{id}/leger-print', \App\Livewire\LegerPreview::class)->name('leger-print');
