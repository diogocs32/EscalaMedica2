<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlantonisταController;
use App\Http\Controllers\CidadeController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\AlocacaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EscalaPadraoController;
use App\Http\Controllers\EscalaPublicadaController;
use App\Http\Controllers\PlantonistaEscalaController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Rotas para Plantonistas
Route::resource('plantonistas', PlantonisταController::class);

// Rotas para Cidades
Route::resource('cidades', CidadeController::class);

// Rotas para Unidades
Route::resource('unidades', UnidadeController::class);

// Rotas para Setores
Route::resource('setores', SetorController::class)->parameters(['setores' => 'setor']);

// Rotas para Turnos
Route::resource('turnos', TurnoController::class);

// Rotas legadas de Vagas (deprecadas) - redirecionam para Escala Padrão
Route::match(['get', 'post'], '/unidades/{unidade}/vagas', function ($unidade) {
    return redirect()->route('escalas-padrao.index', ['unidade' => $unidade])
        ->with('info', 'A tela de Vagas foi substituída pela Escala Padrão.');
})->name('vagas.index');
Route::get('/unidades/{unidade}/vagas/create', function ($unidade) {
    return redirect()->route('escalas-padrao.index', ['unidade' => $unidade])
        ->with('info', 'A criação de Vagas foi substituída pela Escala Padrão.');
})->name('vagas.create');
Route::post('/unidades/{unidade}/vagas', function ($unidade) {
    return redirect()->route('escalas-padrao.index', ['unidade' => $unidade])
        ->with('info', 'A criação de Vagas foi substituída pela Escala Padrão.');
})->name('vagas.store');
Route::get('/unidades/{unidade}/vagas/{vaga}', function ($unidade, $vaga) {
    return redirect()->route('escalas-padrao.index', ['unidade' => $unidade])
        ->with('info', 'A visualização de Vagas foi substituída pela Escala Padrão.');
})->name('vagas.show');
Route::get('/unidades/{unidade}/vagas/{vaga}/edit', function ($unidade, $vaga) {
    return redirect()->route('escalas-padrao.index', ['unidade' => $unidade])
        ->with('info', 'A edição de Vagas foi substituída pela Escala Padrão.');
})->name('vagas.edit');
Route::put('/unidades/{unidade}/vagas/{vaga}', function ($unidade, $vaga) {
    return redirect()->route('escalas-padrao.index', ['unidade' => $unidade])
        ->with('info', 'A atualização de Vagas foi substituída pela Escala Padrão.');
})->name('vagas.update');
Route::delete('/unidades/{unidade}/vagas/{vaga}', function ($unidade, $vaga) {
    return redirect()->route('escalas-padrao.index', ['unidade' => $unidade])
        ->with('info', 'A exclusão de Vagas foi substituída pela Escala Padrão.');
})->name('vagas.destroy');
Route::post('/unidades/{unidade}/vagas/clone', function ($unidade) {
    return redirect()->route('escalas-padrao.index', ['unidade' => $unidade])
        ->with('info', 'O clone de Vagas foi substituído pela Escala Padrão.');
})->name('vagas.clone');

// Rotas para Escala Padrão
// Resumo geral (todas as unidades)
Route::get('/schedule-patterns', [EscalaPadraoController::class, 'resumoGeral'])->name('schedule-patterns');
Route::get('/schedule-patterns/{unidade}/schedule', [EscalaPadraoController::class, 'planilha'])->name('schedule-patterns.schedule');

Route::get('/unidades/{unidade}/escala-padrao', [EscalaPadraoController::class, 'index'])->name('escalas-padrao.index');
Route::get('/unidades/{unidade}/escala-padrao/create', [EscalaPadraoController::class, 'create'])->name('escalas-padrao.create');
Route::post('/unidades/{unidade}/escala-padrao', [EscalaPadraoController::class, 'store'])->name('escalas-padrao.store');
Route::get('/unidades/{unidade}/escala-padrao/{semana}/{dia}/edit', [EscalaPadraoController::class, 'editDia'])->name('escalas-padrao.edit-dia');
Route::post('/unidades/{unidade}/escala-padrao/{semana}/{dia}/configuracao', [EscalaPadraoController::class, 'storeConfiguracao'])->name('escalas-padrao.store-configuracao');
Route::delete('/unidades/{unidade}/escala-padrao/configuracao/{configuracao}', [EscalaPadraoController::class, 'destroyConfiguracao'])->name('escalas-padrao.destroy-configuracao');
Route::post('/unidades/{unidade}/escala-padrao/{semana}/{dia}/copiar', [EscalaPadraoController::class, 'copiarDia'])->name('escalas-padrao.copiar-dia');
Route::put('/unidades/{unidade}/escala-padrao/configuracao/{configuracao}', [EscalaPadraoController::class, 'updateConfiguracao'])->name('escalas-padrao.update-configuracao');
Route::post('/escalas-padrao/{unidade}/publicar', [EscalaPadraoController::class, 'publicar'])->name('escalas-padrao.publicar');

// Rotas para Alocações
Route::resource('alocacoes', AlocacaoController::class);

// Escalas Publicadas - Edição mensal por dia do mês
Route::get('/escalas-publicadas/{escalaPublicada}/edit', [EscalaPublicadaController::class, 'edit'])
    ->name('escalas-publicadas.edit');
// Escalas Publicadas - Edição rápida com dropdown
Route::get('/escalas-publicadas/{escalaPublicada}/edit-rapido', [EscalaPublicadaController::class, 'editRapido'])
    ->name('escalas-publicadas.edit-rapido');
Route::delete('/escalas-publicadas/{escalaPublicada}', [EscalaPublicadaController::class, 'destroy'])
    ->name('escalas-publicadas.destroy');
Route::put('/escalas-publicadas/alocacoes/{alocacaoPublicada}', [EscalaPublicadaController::class, 'updateAlocacao'])
    ->name('escalas-publicadas.alocacoes.update');

// Adicionar novo slot/vaga em uma célula específica
Route::post('/escalas-publicadas/{escalaPublicada}/slots/add', [EscalaPublicadaController::class, 'addSlot'])
    ->name('escalas-publicadas.slots.add');

// Escalas Publicadas - Calendário (consulta)
Route::get('/escalas-publicadas/calendario', [EscalaPublicadaController::class, 'calendar'])
    ->name('escalas-publicadas.calendar');
// API de eventos para o calendário (FullCalendar)
Route::get('/api/escalas-publicadas/events', [EscalaPublicadaController::class, 'events'])
    ->name('api.escalas-publicadas.events')
    ->middleware('api');
// API para buscar escala publicada por ano/mês
Route::get('/api/escalas-publicadas/buscar', [EscalaPublicadaController::class, 'buscarPorMes'])
    ->name('api.escalas-publicadas.buscar')
    ->middleware('api');

// Escalas do Plantonista - Visualização consolidada
Route::get('/escalas/plantonista', [PlantonistaEscalaController::class, 'index'])
    ->name('plantonista.escalas');

// API para Atribuição Rápida (sem middleware CSRF)
Route::get('/api/plantonistas-ativos', [PlantonisταController::class, 'apiAtivos'])
    ->name('api.plantonistas.ativos')
    ->middleware('api');

// API para Alocações Template (Escala Padrão) - sem middleware CSRF
Route::get('/api/escalas-padrao/{unidade}/alocacoes', [EscalaPadraoController::class, 'getAlocacoes'])
    ->name('api.escalas-padrao.alocacoes')
    ->middleware('api');
Route::post('/api/escalas-padrao/{unidade}/alocacoes', [EscalaPadraoController::class, 'storeAlocacao'])
    ->name('api.escalas-padrao.store-alocacao')
    ->middleware('api');

// API para Clonar configurações de um dia para múltiplos destinos (lote)
Route::post('/api/escalas-padrao/{unidade}/clonar-dia', [EscalaPadraoController::class, 'clonarDiaLote'])
    ->name('api.escalas-padrao.clonar-dia')
    ->middleware('api');

// API para Clonar semana inteira para múltiplas semanas de destino (lote)
Route::post('/api/escalas-padrao/{unidade}/clonar-semana', [EscalaPadraoController::class, 'clonarSemanaLote'])
    ->name('api.escalas-padrao.clonar-dia')
    ->middleware('api');
