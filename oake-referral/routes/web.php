<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/existing', 'existing')->name('existing');
Route::view('/new', 'new')->name('new');
Route::view('/hospitals', 'hospitals')->name('hospitals');
Route::view('/insights', 'insights')->name('insights');

