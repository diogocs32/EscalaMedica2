<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;

// Buscar a data de vigência real do banco
$escalaPadrao = \App\Models\EscalaPadrao::first();
if (!$escalaPadrao) {
    die("Nenhuma escala padrão encontrada!\n");
}

$dataVigencia = Carbon::parse($escalaPadrao->vigencia_inicio);
echo "=== Data de Vigência Real: " . $dataVigencia->format('d/m/Y (l)') . " ===\n\n";

// Buscar configurações da Semana 1, Terça
$semana1 = \App\Models\SemanaTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->where('numero_semana', 1)
    ->first();

$diaTerca = \App\Models\DiaTemplate::where('semana_template_id', $semana1->id)
    ->where('dia_semana', 'terca')
    ->first();

echo "=== Configurações da SEMANA 1 - TERÇA ===\n";
if ($diaTerca) {
    $configs = $diaTerca->configuracoes()->with('turno', 'setor')->get();
    foreach ($configs as $config) {
        echo "- {$config->turno->nome} | {$config->setor->nome} | Slots: {$config->quantidade_necessaria}\n";
    }
} else {
    echo "Nenhuma configuração encontrada!\n";
}

// Buscar alocações da Semana 1, dia=3 (terça, se domingo=1)
echo "\n=== Alocações Template SEMANA 1 - DIA 3 (Terça) ===\n";
$alocacoes = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->where('semana', 1)
    ->where('dia', 3)
    ->with('turno', 'setor', 'plantonista')
    ->get();

foreach ($alocacoes as $aloc) {
    $medico = $aloc->plantonista ? $aloc->plantonista->nome : "VAGO";
    echo "- {$aloc->turno->nome} | {$aloc->setor->nome} | Slot {$aloc->slot}: {$medico}\n";
}

// Agora calcular qual dia do mês de Dezembro 2025 cairá na Semana 1 Terça
echo "\n=== Calculando para DEZEMBRO 2025 ===\n";
$diaVigencia = $dataVigencia->dayOfWeek;
$domingoVigencia = $dataVigencia->copy()->subDays($diaVigencia);
echo "Domingo da Vigência: " . $domingoVigencia->format('d/m/Y') . "\n\n";

// Encontrar todas as terças de dezembro 2025
for ($dia = 1; $dia <= 31; $dia++) {
    $data = Carbon::create(2025, 12, $dia);
    if ($data->dayOfWeek == 2) { // 2 = terça
        $diaSemana = $data->dayOfWeek;
        $domingoSemana = $data->copy()->subDays($diaSemana);
        $diffDias = $domingoVigencia->diffInDays($domingoSemana, false);
        $diffSemanas = floor($diffDias / 7);
        $semana = ($diffSemanas % 5) + 1;
        while ($semana <= 0) {
            $semana += 5;
        }

        echo "Terça {$data->format('d/m/Y')} - Domingo: {$domingoSemana->format('d/m')} - DiffSemanas: {$diffSemanas} - SEMANA: {$semana}\n";
    }
}
