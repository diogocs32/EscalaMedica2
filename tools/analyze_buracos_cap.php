<?php
require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\{EscalaPadrao, SemanaTemplate, DiaTemplate, ConfiguracaoTurnoSetor, AlocacaoTemplate};

$escala = EscalaPadrao::where('unidade_id', 1)->first();
$diasMap = ['domingo' => 1, 'segunda' => 2, 'terca' => 3, 'quarta' => 4, 'quinta' => 5, 'sexta' => 6, 'sabado' => 7];

$totalEsperado = (int) ConfiguracaoTurnoSetor::whereHas('diaTemplate.semanaTemplate', function ($q) use ($escala) {
    $q->where('escala_padrao_id', $escala->id);
})->sum('quantidade_necessaria');

$semanal = [];
$capSum = 0;
$naoCriados = 0;
$excessoTotal = 0;
$semanas = $escala->semanas()->with(['dias.configuracoes'])->get();
foreach ($semanas as $sem) {
    $s = (int) $sem->numero_semana;
    $semanal[$s] = ['esperado' => 0, 'cap' => 0, 'buracos' => 0, 'excesso' => 0];
    foreach ($sem->dias as $dia) {
        $diaNum = $diasMap[$dia->dia_semana] ?? null;
        if ($diaNum === null) continue;
        foreach ($dia->configuracoes as $cfg) {
            $qtd = (int) $cfg->quantidade_necessaria;
            $semanal[$s]['esperado'] += $qtd;
            $filled = AlocacaoTemplate::where('escala_padrao_id', $escala->id)
                ->where('semana', $s)->where('dia', $diaNum)
                ->where('turno_id', $cfg->turno_id)->where('setor_id', $cfg->setor_id)
                ->whereNotNull('plantonista_id')->count();
            $cap = min($qtd, $filled);
            $capSum += $cap;
            $semanal[$s]['cap'] += $cap;
            $semanal[$s]['excesso'] += max(0, $filled - $qtd);
        }
    }
    $semanal[$s]['buracos'] = $semanal[$s]['esperado'] - $semanal[$s]['cap'];
}

$buracos = $totalEsperado - $capSum;

echo "TOTAL ESPERADO: $totalEsperado\n";
echo "PREENCHIDOS (cap): $capSum\n";
echo "BURACOS (cap): $buracos\n\n";
foreach ($semanal as $s => $row) {
    echo "Semana $s: Esperado={$row['esperado']}, Cap={$row['cap']}, Buracos={$row['buracos']}, Excesso={$row['excesso']}\n";
}
