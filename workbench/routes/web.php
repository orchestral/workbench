<?php

use Illuminate\Support\Facades\Route;

Route::get('failed', fn () => throw new RuntimeException('Bad route!'));

Route::get('/', fn () => view('welcome'))->name('welcome');
