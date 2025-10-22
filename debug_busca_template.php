<?php

require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG DETALHADO DA PUBLICAÇÃO ===\n\n";

$escalaPadrao = App\Models\EscalaPadrao::where('unidade_id', 1)->first();
echo "Escala Padrão ID: {$escalaPadrao->id}\n";
echo "Vigência: {$escalaPadrao->vigencia_inicio}\n\n";

// Simular 1 de dezembro de 2025
$ano = 2025;
$mes = 12;
$dia = 1;

$dataAtual = \Carbon\Carbon::create($ano, $mes, $dia)->startOfDay();
$vigenciaInicio = \Carbon\Carbon::parse($escalaPadrao->vigencia_inicio)->startOfDay();

echo "Data a processar: {$dataAtual}\n";
echo "Vigência início: {$vigenciaInicio}\n\n";

// Calcular semana
$diasDesdeVigencia = $dataAtual->diffInDays($vigenciaInicio, false);
echo "Dias desde vigência (signed): $diasDesdeVigencia\n";

$numeroDaSemana = (int)(floor($diasDesdeVigencia / 7) % 5) + 1;
echo "Número da semana calculado: $numeroDaSemana\n";

if ($numeroDaSemana <= 0) {
    $numeroDaSemana = 5 + ($numeroDaSemana % 5);
    echo "Número da semana ajustado: $numeroDaSemana\n";
}

// Nome do dia
$dias = [
    0 => 'domingo',
    1 => 'segunda',
    2 => 'terca',
    3 => 'quarta',
    4 => 'quinta',
    5 => 'sexta',
    6 => 'sabado',
];
$nomeDiaSemana = $dias[$dataAtual->dayOfWeek];
echo "Nome do dia: $nomeDiaSemana (dayOfWeek: {$dataAtual->dayOfWeek})\n\n";

// Buscar alocações template COM OS CRITÉRIOS EXATOS DO CÓDIGO
echo "=== BUSCANDO ALOCAÇÕES TEMPLATE ===\n";
echo "Critérios: escala_padrao_id={$escalaPadrao->id}, semana=$numeroDaSemana, dia=$nomeDiaSemana\n\n";

$alocacoesTemplate = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->where('semana', $numeroDaSemana)
    ->where('dia', $nomeDiaSemana)
    ->orderBy('turno_id')
    ->orderBy('setor_id')
    ->orderBy('slot')
    ->get();

echo "Total de alocações template encontradas: " . $alocacoesTemplate->count() . "\n\n";

if ($alocacoesTemplate->count() > 0) {
    echo "Primeiras 5 alocações:\n";
    foreach ($alocacoesTemplate->take(5) as $aloc) {
        $plantonista = $aloc->plantonista_id ? "Médico {$aloc->plantonista_id}" : "NULL";
        echo "  - Turno {$aloc->turno_id}, Setor {$aloc->setor_id}, Slot {$aloc->slot}: $plantonista\n";
    }
} else {
    echo "❌ NENHUMA alocação template encontrada!\n\n";

    // Vamos ver o que TEM na tabela
    echo "=== VERIFICANDO O QUE EXISTE NA TABELA ===\n";
    $sample = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
        ->limit(10)
        ->get();

    echo "Primeiras 10 alocações da tabela (SEM filtro de semana/dia):\n";
    foreach ($sample as $aloc) {
        echo "  - Semana: '{$aloc->semana}', Dia: '{$aloc->dia}', Turno {$aloc->turno_id}, Setor {$aloc->setor_id}\n";
    }

    echo "\n";

    // Ver valores DISTINTOS de semana e dia
    $semanas = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
        ->distinct()
        ->pluck('semana');
    echo "Valores de 'semana' na tabela: " . $semanas->implode(', ') . "\n";

    $dias = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
        ->distinct()
        ->pluck('dia');
    echo "Valores de 'dia' na tabela: " . $dias->implode(', ') . "\n";
}
