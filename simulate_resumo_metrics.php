<?php
require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\{Unidade, ConfiguracaoTurnoSetor};

$unidades = App\Models\Unidade::orderBy('nome')->get();
$diasMap = ['domingo' => 1, 'segunda' => 2, 'terca' => 3, 'quarta' => 4, 'quinta' => 5, 'sexta' => 6, 'sabado' => 7];

foreach ($unidades as $unidade) {
    $escala = $unidade->escalaPadrao()->where('status', 'ativo')->first();
    if (!$escala) {
        continue;
    }
    $totalSlots = (int) ConfiguracaoTurnoSetor::whereHas('diaTemplate.semanaTemplate', function ($q) use ($escala) {
        $q->where('escala_padrao_id', $escala->id);
    })->sum('quantidade_necessaria');
    $preCap = 0;
    $semanas = $escala->semanas()->with(['dias.configuracoes'])->get();
    foreach ($semanas as $sem) {
        $s = (int)$sem->numero_semana;
        foreach ($sem->dias as $dia) {
            $diaNum = $diasMap[$dia->dia_semana] ?? null;
            if ($diaNum === null) continue;
            foreach ($dia->configuracoes as $cfg) {
                $qtd = (int)$cfg->quantidade_necessaria;
                $filled = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
                    ->where('semana', $s)->where('dia', $diaNum)
                    ->where('turno_id', $cfg->turno_id)->where('setor_id', $cfg->setor_id)
                    ->whereNotNull('plantonista_id')->count();
                $preCap += min($qtd, $filled);
            }
        }
    }
    $buracos = max(0, $totalSlots - $preCap);
    $taxa = $totalSlots > 0 ? round(($preCap / $totalSlots) * 100, 1) : 0;
    echo "{$unidade->nome}: total=$totalSlots, preenchidos(cap)=$preCap, buracos=$buracos, taxa=$taxa%\n";
}
