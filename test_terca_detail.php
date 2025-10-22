<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;

echo "=== ANÁLISE DETALHADA: TERÇA-FEIRA DA SEMANA 1 ===\n\n";

// Vamos focar na terça da semana 1 de dezembro (dia 2)
$ano = 2025;
$mes = 12;
$dia = 2; // Terça-feira

$data = Carbon::create($ano, $mes, $dia);

echo "DATA ANALISADA: {$data->format('d/m/Y')} ({$data->locale('pt_BR')->isoFormat('dddd')})\n";
echo str_repeat("=", 80) . "\n\n";

// CÁLCULO DO DISPLAY
$semanaDisplay = (int) ceil($dia / 7);
echo "DISPLAY (EscalaPublicadaController):\n";
echo "  - Dia: $dia\n";
echo "  - Semana calculada: $semanaDisplay\n";
echo "  - Fórmula: ceil($dia / 7) = $semanaDisplay\n\n";

// CÁLCULO DA PUBLICAÇÃO
$semanaMes = (int) ceil($dia / 7);
$numeroDaSemana = (($semanaMes - 1) % 5) + 1;

// Nome do dia da semana usado no template
function getNomeDiaSemana($dayOfWeek)
{
    $dias = [
        0 => 'domingo',
        1 => 'segunda',
        2 => 'terca',
        3 => 'quarta',
        4 => 'quinta',
        5 => 'sexta',
        6 => 'sabado'
    ];
    return $dias[$dayOfWeek] ?? null;
}

$nomeDiaSemana = getNomeDiaSemana($data->dayOfWeek);
$numeroDiaSemana = $data->dayOfWeek + 1; // Carbon: 0=domingo -> converter para 1-7

echo "PUBLICAÇÃO (EscalaPadraoController):\n";
echo "  - Dia: $dia\n";
echo "  - SemanaMês: $semanaMes (ceil($dia / 7))\n";
echo "  - NumeroDaSemana (ciclo 1-5): $numeroDaSemana ((($semanaMes - 1) % 5) + 1)\n";
echo "  - Nome dia semana: $nomeDiaSemana\n";
echo "  - Número dia semana: $numeroDiaSemana (Carbon dayOfWeek + 1)\n\n";

echo str_repeat("=", 80) . "\n";
echo "BUSCA NO BANCO DE DADOS:\n";
echo str_repeat("=", 80) . "\n\n";

// Buscar a escala padrão mais recente
$escalaPadrao = \App\Models\EscalaPadrao::latest()->first();

if (!$escalaPadrao) {
    echo "❌ ERRO: Nenhuma escala padrão encontrada!\n";
    exit(1);
}

echo "Escala Padrão ID: {$escalaPadrao->id}\n";
echo "Nome: {$escalaPadrao->nome}\n";
echo "Vigência: {$escalaPadrao->vigencia_inicio}\n\n";

// Buscar semana template
$semanaTemplate = \App\Models\SemanaTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->where('numero_semana', $numeroDaSemana)
    ->first();

if ($semanaTemplate) {
    echo "✅ Semana Template encontrada:\n";
    echo "  - ID: {$semanaTemplate->id}\n";
    echo "  - Número: {$semanaTemplate->numero_semana}\n";
} else {
    echo "❌ Semana Template NÃO encontrada para número: $numeroDaSemana\n";
}

// Buscar dia template
$diaTemplate = \App\Models\DiaTemplate::whereHas('semanaTemplate', function ($q) use ($escalaPadrao, $numeroDaSemana) {
    $q->where('escala_padrao_id', $escalaPadrao->id)
        ->where('numero_semana', $numeroDaSemana);
})
    ->where('dia_semana', $nomeDiaSemana)
    ->with('configuracoes.turno', 'configuracoes.setor')
    ->first();

if ($diaTemplate) {
    echo "\n✅ Dia Template encontrado:\n";
    echo "  - ID: {$diaTemplate->id}\n";
    echo "  - Dia Semana: {$diaTemplate->dia_semana}\n";
    echo "  - Semana Template ID: {$diaTemplate->semana_template_id}\n";
    echo "  - Configurações: " . $diaTemplate->configuracoes->count() . "\n";
} else {
    echo "\n❌ Dia Template NÃO encontrado!\n";
    echo "  - Buscando: semana=$numeroDaSemana, dia_semana=$nomeDiaSemana\n";
}

// Buscar alocações template
echo "\n" . str_repeat("=", 80) . "\n";
echo "ALOCAÇÕES TEMPLATE:\n";
echo str_repeat("=", 80) . "\n";

$alocacoesTemplate = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->where('semana', $numeroDaSemana)
    ->where('dia', $numeroDiaSemana)
    ->with('plantonista', 'turno', 'setor')
    ->get();

echo "Total de alocações encontradas: {$alocacoesTemplate->count()}\n\n";

if ($alocacoesTemplate->count() > 0) {
    echo sprintf(
        "%-6s %-10s %-20s %-20s %-20s %-6s\n",
        "ID",
        "Semana",
        "Turno",
        "Setor",
        "Plantonista",
        "Slot"
    );
    echo str_repeat("-", 80) . "\n";

    foreach ($alocacoesTemplate as $aloc) {
        echo sprintf(
            "%-6d %-10d %-20s %-20s %-20s %-6d\n",
            $aloc->id,
            $aloc->semana,
            $aloc->turno->nome ?? 'N/A',
            $aloc->setor->nome ?? 'N/A',
            $aloc->plantonista->nome ?? 'VAGO',
            $aloc->slot
        );
    }
} else {
    echo "⚠️ Nenhuma alocação template encontrada para:\n";
    echo "  - Semana: $numeroDaSemana\n";
    echo "  - Dia: $numeroDiaSemana\n";
}

// Agora vamos verificar TODAS as alocações da semana 1
echo "\n\n" . str_repeat("=", 80) . "\n";
echo "TODAS AS ALOCAÇÕES DA SEMANA 1 NO TEMPLATE:\n";
echo str_repeat("=", 80) . "\n";

$todasSemana1 = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->where('semana', 1)
    ->with('plantonista', 'turno', 'setor')
    ->orderBy('dia')
    ->orderBy('turno_id')
    ->get();

echo "Total: {$todasSemana1->count()}\n\n";

if ($todasSemana1->count() > 0) {
    $diasNomes = [1 => 'Domingo', 2 => 'Segunda', 3 => 'Terça', 4 => 'Quarta', 5 => 'Quinta', 6 => 'Sexta', 7 => 'Sábado'];

    echo sprintf(
        "%-8s %-10s %-20s %-20s %-20s %-6s\n",
        "Dia#",
        "Dia Nome",
        "Turno",
        "Setor",
        "Plantonista",
        "Slot"
    );
    echo str_repeat("-", 90) . "\n";

    foreach ($todasSemana1 as $aloc) {
        echo sprintf(
            "%-8d %-10s %-20s %-20s %-20s %-6d\n",
            $aloc->dia,
            $diasNomes[$aloc->dia] ?? 'N/A',
            $aloc->turno->nome ?? 'N/A',
            $aloc->setor->nome ?? 'N/A',
            $aloc->plantonista->nome ?? 'VAGO',
            $aloc->slot
        );
    }
}

// Verificar alocações PUBLICADAS para o dia 2/12
echo "\n\n" . str_repeat("=", 80) . "\n";
echo "ALOCAÇÕES PUBLICADAS PARA 02/12/2025:\n";
echo str_repeat("=", 80) . "\n";

$escalaPublicada = \App\Models\EscalaPublicada::where('ano', $ano)
    ->where('mes', $mes)
    ->first();

if ($escalaPublicada) {
    $alocacoesPublicadas = \App\Models\AlocacaoPublicada::where('escala_publicada_id', $escalaPublicada->id)
        ->where('data', $data->format('Y-m-d'))
        ->with('plantonista', 'turno', 'setor')
        ->orderBy('turno_id')
        ->get();

    echo "Total: {$alocacoesPublicadas->count()}\n\n";

    if ($alocacoesPublicadas->count() > 0) {
        echo sprintf(
            "%-6s %-20s %-20s %-20s %-10s\n",
            "ID",
            "Turno",
            "Setor",
            "Plantonista",
            "Status"
        );
        echo str_repeat("-", 80) . "\n";

        foreach ($alocacoesPublicadas as $aloc) {
            echo sprintf(
                "%-6d %-20s %-20s %-20s %-10s\n",
                $aloc->id,
                $aloc->turno->nome ?? 'N/A',
                $aloc->setor->nome ?? 'N/A',
                $aloc->plantonista->nome ?? 'VAGO',
                $aloc->status
            );
        }
    }
} else {
    echo "❌ Nenhuma escala publicada encontrada para $mes/$ano\n";
}
