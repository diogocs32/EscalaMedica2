<?php

require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ANÁLISE PROFUNDA DA ESTRUTURA DE SLOTS ===\n\n";

$escalaPadrao = App\Models\EscalaPadrao::where('unidade_id', 1)->first();
echo "Escala Padrão ID: {$escalaPadrao->id}\n";
echo "Unidade: {$escalaPadrao->unidade->nome}\n\n";

// Exemplo: Segunda-feira da Semana 1, Turno Manhã
echo "=== EXEMPLO: Segunda-feira, Semana 1, Turno Manhã ===\n\n";

// Buscar alocações template
$alocacoesSegunda = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->where('semana', 1)
    ->where('dia', 2) // segunda = 2
    ->orderBy('turno_id')
    ->orderBy('setor_id')
    ->orderBy('slot')
    ->get();

echo "Total de alocações na segunda-feira semana 1: " . $alocacoesSegunda->count() . "\n\n";

// Agrupar por turno e setor
$grouped = $alocacoesSegunda->groupBy(function ($item) {
    return $item->turno_id . '-' . $item->setor_id;
});

foreach ($grouped as $key => $items) {
    $first = $items->first();
    $turno = App\Models\Turno::find($first->turno_id);
    $setor = App\Models\Setor::find($first->setor_id);

    echo "Turno: {$turno->nome} | Setor: {$setor->nome}\n";
    echo "Total de slots: " . $items->count() . "\n";

    foreach ($items as $aloc) {
        $medico = $aloc->plantonista_id ? "Médico ID {$aloc->plantonista_id}" : "VAZIO (buraco)";
        echo "  - Slot {$aloc->slot}: $medico\n";
    }
    echo "\n";
}

// Verificar especificamente Turno Manhã
$turnoManha = App\Models\Turno::where('nome', 'like', '%manhã%')->orWhere('nome', 'like', '%Manhã%')->first();
if ($turnoManha) {
    echo "=== FOCO NO TURNO MANHÃ (ID: {$turnoManha->id}) ===\n\n";

    $alocacoesManha = $alocacoesSegunda->where('turno_id', $turnoManha->id);
    echo "Total de slots no turno manhã: " . $alocacoesManha->count() . "\n\n";

    $porSetor = $alocacoesManha->groupBy('setor_id');
    foreach ($porSetor as $setorId => $slots) {
        $setor = App\Models\Setor::find($setorId);
        $preenchidos = $slots->whereNotNull('plantonista_id')->count();
        $buracos = $slots->whereNull('plantonista_id')->count();

        echo "Setor: {$setor->nome} (ID: {$setorId})\n";
        echo "  Total slots: " . $slots->count() . "\n";
        echo "  Preenchidos: $preenchidos\n";
        echo "  Buracos: $buracos\n";

        foreach ($slots as $aloc) {
            $medico = $aloc->plantonista_id ? "Médico {$aloc->plantonista_id}" : "BURACO";
            echo "    - Slot {$aloc->slot}: $medico\n";
        }
        echo "\n";
    }
}

// Estatísticas gerais
echo "=== ESTATÍSTICAS GERAIS DA ESCALA PADRÃO ===\n\n";
$totalAlocacoes = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)->count();
$preenchidos = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)->whereNotNull('plantonista_id')->count();
$buracos = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)->whereNull('plantonista_id')->count();

echo "Total de slots (5 semanas × 7 dias): $totalAlocacoes\n";
echo "Preenchidos: $preenchidos\n";
echo "Buracos: $buracos\n";
echo "Taxa de preenchimento: " . round(($preenchidos / $totalAlocacoes) * 100, 2) . "%\n\n";

// Verificar se tabela configuracoes_turno_setor tem informações
echo "=== CONFIGURAÇÕES vs ALOCAÇÕES ===\n\n";
$semana1 = App\Models\SemanaTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->where('numero_semana', 1)
    ->first();

if ($semana1) {
    $diaSegunda = App\Models\DiaTemplate::where('semana_template_id', $semana1->id)
        ->where('dia_semana', 'segunda')
        ->first();

    if ($diaSegunda) {
        $configs = App\Models\ConfiguracaoTurnoSetor::where('dia_template_id', $diaSegunda->id)->get();

        echo "Configurações na segunda-feira semana 1:\n";
        foreach ($configs as $config) {
            $turno = $config->turno;
            $setor = $config->setor;
            echo "  - Turno: {$turno->nome}, Setor: {$setor->nome}, Qty necessária: {$config->quantidade_necessaria}\n";
        }
        echo "\n";

        echo "IMPORTANTE: ConfiguracaoTurnoSetor define QUANTOS slots devem existir.\n";
        echo "AlocacaoTemplate define QUEM está em cada slot.\n\n";
    }
}

// Simular dezembro
echo "=== SIMULAÇÃO: PUBLICAÇÃO DEZEMBRO 2025 ===\n\n";
$dataExemplo = Carbon\Carbon::create(2025, 12, 1); // Segunda-feira
$vigenciaInicio = Carbon\Carbon::parse($escalaPadrao->vigencia_inicio)->startOfDay();

$diasDesdeVigencia = $dataExemplo->diffInDays($vigenciaInicio, false);
$numeroDaSemana = (int)(floor($diasDesdeVigencia / 7) % 5) + 1;
if ($numeroDaSemana <= 0) {
    $numeroDaSemana = 5 + ($numeroDaSemana % 5);
}
$numeroDiaSemana = $dataExemplo->dayOfWeek + 1;

echo "Data: 1 de dezembro 2025 (segunda-feira)\n";
echo "Semana do ciclo: $numeroDaSemana\n";
echo "Número do dia: $numeroDiaSemana\n\n";

// Buscar alocações que DEVERIAM ser publicadas
$alocacoesParaPublicar = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->where('semana', $numeroDaSemana)
    ->where('dia', $numeroDiaSemana)
    ->get();

echo "Alocações que deveriam ser publicadas para 1/12/2025: " . $alocacoesParaPublicar->count() . "\n";
$preenchidosPublicar = $alocacoesParaPublicar->whereNotNull('plantonista_id')->count();
$buracosPublicar = $alocacoesParaPublicar->whereNull('plantonista_id')->count();

echo "Preenchidos: $preenchidosPublicar\n";
echo "Buracos: $buracosPublicar\n";
