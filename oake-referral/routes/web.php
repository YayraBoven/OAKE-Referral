<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\NewPatientController;
use App\Http\Controllers\HospitalController;

Route::view('/', 'home')->name('home');

Route::get('/existing', [ReferralController::class, 'showForm'])->name('existing');
Route::post('/existing', [ReferralController::class, 'submit'])->name('existing.submit');

Route::get('/new', [NewPatientController::class, 'showForm'])->name('new');
Route::post('/new', [NewPatientController::class, 'submit'])->name('new.submit');

Route::get('/hospitals', [HospitalController::class, 'index'])->name('hospitals');
Route::view('/insights', 'insights')->name('insights');