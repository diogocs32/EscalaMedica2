<?php

require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$escala = App\Models\EscalaPadrao::where('unidade_id', 1)->first();
$semana = 1;
$diaNome = 'sexta';
$turno = App\Models\Turno::where('nome', 'like', '%manha%')->first();
$setor = App\Models\Setor::where('nome', 'like', '%teleconsulta%')->where('nome', 'not like', '%pedi%')->first();

$sem = App\Models\SemanaTemplate::where('escala_padrao_id', $escala->id)->where('numero_semana', $semana)->first();
$dia = App\Models\DiaTemplate::where('semana_template_id', $sem->id)->where('dia_semana', $diaNome)->first();

if (!$dia) {
    echo "DiaTemplate sexta não encontrado\n";
    exit;
}

$cfg = App\Models\ConfiguracaoTurnoSetor::where('dia_template_id', $dia->id)
    ->where('turno_id', $turno->id)
    ->where('setor_id', $setor->id)
    ->first();

echo 'Config Sexta Manha Teleconsulta Qty: ' . ($cfg ? $cfg->quantidade_necessaria : '(NENHUMA)') . "\n";

$map = ['domingo' => 1, 'segunda' => 2, 'terca' => 3, 'quarta' => 4, 'quinta' => 5, 'sexta' => 6, 'sabado' => 7];
$numeroDia = $map[$diaNome];

$countAloc = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escala->id)
    ->where('semana', $semana)
    ->where('dia', $numeroDia)
    ->where('turno_id', $turno->id)
    ->where('setor_id', $setor->id)
    ->count();

echo 'Alocações criadas p/ esse turno+setor: ' . $countAloc . "\n";
