<?php
require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\{Unidade, ConfiguracaoTurnoSetor, EscalaPadrao};

$unidade = Unidade::find(1);
if (!$unidade) {
    echo "Unidade 1 não encontrada\n";
    exit(1);
}
$escala = $unidade->escalaPadrao()->where('status', 'ativo')->with(['semanas.dias.configuracoes.turno', 'semanas.dias.configuracoes.setor'])->first();
if (!$escala) {
    echo "Escala ativa não encontrada\n";
    exit(1);
}

// turnosMapa + grid (como no controller)
$turnosMapa = [];
foreach ($escala->semanas as $semana) {
    foreach ($semana->dias as $dia) {
        foreach ($dia->configuracoes as $cfg) {
            $tId = $cfg->turno->id;
            $sId = $cfg->setor->id;
            if (!isset($turnosMapa[$tId])) {
                $turnosMapa[$tId] = ['turno' => $cfg->turno, 'setores' => []];
            }
            $turnosMapa[$tId]['setores'][$sId] = $cfg->setor;
        }
    }
}
$diasOrdem = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
$grid = [];
foreach ($escala->semanas as $semana) {
    $semNumber = (int)$semana->numero_semana;
    $grid[$semNumber] = [];
    foreach ($diasOrdem as $d) {
        $grid[$semNumber][$d] = [];
    }
    foreach ($semana->dias as $dia) {
        $dKey = $dia->dia_semana;
        foreach ($dia->configuracoes as $cfg) {
            $grid[$semNumber][$dKey][$cfg->turno->id][$cfg->setor->id] = (int)$cfg->quantidade_necessaria;
        }
    }
}

$totalSlots = (int) ConfiguracaoTurnoSetor::whereHas('diaTemplate.semanaTemplate', function ($q) use ($escala) {
    $q->where('escala_padrao_id', $escala->id);
})->sum('quantidade_necessaria');

$diasMap = ['domingo' => 1, 'segunda' => 2, 'terca' => 3, 'quarta' => 4, 'quinta' => 5, 'sexta' => 6, 'sabado' => 7];
$preenchidosCap = 0;
foreach ($grid as $semNumber => $diasGrid) {
    foreach ($diasGrid as $dKey => $turnosGrid) {
        $diaNum = $diasMap[$dKey] ?? null;
        if ($diaNum === null) continue;
        foreach ($turnosGrid as $tId => $setoresGrid) {
            foreach ($setoresGrid as $sId => $qtdEsperada) {
                $filledCount = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
                    ->where('semana', (int)$semNumber)
                    ->where('dia', (int)$diaNum)
                    ->where('turno_id', (int)$tId)
                    ->where('setor_id', (int)$sId)
                    ->whereNotNull('plantonista_id')
                    ->count();
                $preenchidosCap += min((int)$qtdEsperada, (int)$filledCount);
            }
        }
    }
}
$buracos = max(0, $totalSlots - $preenchidosCap);
$taxa = $totalSlots > 0 ? round(($preenchidosCap / $totalSlots) * 100, 1) : 0;

echo "PLANILHA METRICS\n";
echo "Total: $totalSlots\nPreenchidos(cap): $preenchidosCap\nBuracos: $buracos\nTaxa: $taxa%\n";
