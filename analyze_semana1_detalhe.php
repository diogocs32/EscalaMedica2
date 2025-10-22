<?php

require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\{EscalaPadrao, SemanaTemplate, DiaTemplate, ConfiguracaoTurnoSetor, AlocacaoTemplate, Turno, Setor};

echo "=== DETALHE SEMANA 1 ===\n";
$escala = EscalaPadrao::where('unidade_id', 1)->first();
$semana = 1;
$sem = SemanaTemplate::where('escala_padrao_id', $escala->id)->where('numero_semana', $semana)->first();
$dias = DiaTemplate::where('semana_template_id', $sem->id)->get();

$map = ['domingo' => 1, 'segunda' => 2, 'terca' => 3, 'quarta' => 4, 'quinta' => 5, 'sexta' => 6, 'sabado' => 7];
$totalEsperado = 0;
$totalAlocados = 0;
$totalPreenchidos = 0;
foreach ($dias as $dia) {
    $numeroDia = $map[$dia->dia_semana] ?? 0;
    $configs = ConfiguracaoTurnoSetor::where('dia_template_id', $dia->id)->get();
    $esperadoDia = $configs->sum('quantidade_necessaria');
    $alocs = AlocacaoTemplate::where('escala_padrao_id', $escala->id)->where('semana', $semana)->where('dia', $numeroDia)->get();
    $alocadosDia = $alocs->count();
    $preenchidosDia = $alocs->whereNotNull('plantonista_id')->count();
    echo strtoupper($dia->dia_semana) . ": Esperado=$esperadoDia, Alocados=$alocadosDia, Preenchidos=$preenchidosDia\n";
    if ($dia->dia_semana === 'sexta') {
        $turno = Turno::where('nome', 'like', '%manha%')->first();
        $setor = Setor::where('nome', 'like', '%teleconsulta%')->where('nome', 'not like', '%pedi%')->first();
        $cfg = ConfiguracaoTurnoSetor::where('dia_template_id', $dia->id)->where('turno_id', $turno->id)->where('setor_id', $setor->id)->first();
        $countAloc = AlocacaoTemplate::where('escala_padrao_id', $escala->id)->where('semana', $semana)->where('dia', $numeroDia)->where('turno_id', $turno->id)->where('setor_id', $setor->id)->count();
        echo "  Sexta Manha Teleconsulta: cfg=" . ($cfg ? $cfg->quantidade_necessaria : 0) . ", aloc=$countAloc\n";
    }
    $totalEsperado += $esperadoDia;
    $totalAlocados += $alocadosDia;
    $totalPreenchidos += $preenchidosDia;
}

echo "TOTAL: Esperado=$totalEsperado, Alocados=$totalAlocados, Preenchidos=$totalPreenchidos, Buracos=" . ($totalEsperado - $totalPreenchidos) . "\n";
