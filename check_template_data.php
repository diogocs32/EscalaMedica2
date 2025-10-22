<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "===== DADOS DAS ALOCAÇÕES TEMPLATE =====\n\n";

// Buscar alocações da segunda-feira semana 1
echo "SEGUNDA-FEIRA SEMANA 1 - Turno Manhã (ID:1), Setor Teleconsulta (ID:1):\n";
$alocacoes = DB::table('alocacoes_template')
    ->where('semana', 1)
    ->where('dia', 'segunda')
    ->where('turno_id', 1)
    ->where('setor_id', 1)
    ->orderBy('slot')
    ->get();

foreach ($alocacoes as $a) {
    $plantonista = DB::table('plantonistas')->find($a->plantonista_id);
    echo "  Slot {$a->slot}: " . ($plantonista ? $plantonista->nome : 'VAGO') . "\n";
}

// Total de alocações na semana 1
echo "\nTOTAL DE ALOCAÇÕES SEMANA 1:\n";
$porDia = DB::table('alocacoes_template')
    ->where('escala_padrao_id', 1)
    ->where('semana', 1)
    ->select('dia', DB::raw('count(*) as total'))
    ->groupBy('dia')
    ->get();

foreach ($porDia as $pd) {
    echo "  {$pd->dia}: {$pd->total} slots\n";
}

echo "\n===== FIM =====\n";
