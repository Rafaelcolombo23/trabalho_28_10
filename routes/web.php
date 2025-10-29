<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TarefaController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('categorias', CategoriaController::class)
    ->only(['index', 'store']);
Route::resource('tarefas', TarefaController::class);
Route::patch('tarefas/{tarefa}/toggle-complete', [TarefaController::class, 'toggleComplete'])
    ->name('tarefas.toggle-complete');



