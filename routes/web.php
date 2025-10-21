<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\AlocacaoController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Rotas para Setores
Route::resource('setores', SetorController::class);

// Rotas para Turnos
Route::resource('turnos', TurnoController::class);

// Rotas para Alocações
Route::resource('alocacoes', AlocacaoController::class);
