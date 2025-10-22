<?php

require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ANÁLISE COMPLETA: ESCALA PADRÃO vs PUBLICADA ===\n\n";

// Buscar escala padrão da unidade 1
$escalaPadrao = App\Models\EscalaPadrao::where('unidade_id', 1)->first();
if (!$escalaPadrao) {
    echo "Nenhuma escala padrão encontrada para unidade 1\n";
    exit;
}

// Contar alocações template
$totalAlocacoes = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)->count();
$comPlantonista = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)->whereNotNull('plantonista_id')->count();
$semPlantonista = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)->whereNull('plantonista_id')->count();

echo "=== ESCALA PADRÃO (Template) ===\n";
echo "ID: {$escalaPadrao->id}\n";
echo "Unidade: {$escalaPadrao->unidade->nome}\n";
echo "Vigência: {$escalaPadrao->vigencia_inicio}\n";
echo "Total de alocações template: $totalAlocacoes\n";
echo "Com plantonista: $comPlantonista\n";
echo "Sem plantonista (vazios): $semPlantonista\n\n";

// Sample de alocações template
echo "Sample de alocações template (primeiras 10):\n";
$samples = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->orderBy('semana')
    ->orderBy('dia')
    ->limit(10)
    ->get();

foreach ($samples as $aloc) {
    $plantonista = $aloc->plantonista_id ? "Médico ID: {$aloc->plantonista_id}" : "VAZIO";
    echo "  - Semana {$aloc->semana}, {$aloc->dia}, Turno {$aloc->turno_id}, Setor {$aloc->setor_id}, Slot {$aloc->slot}: $plantonista\n";
}
echo "\n";

// Buscar escalas publicadas de dezembro
$publicadas = App\Models\EscalaPublicada::where('unidade_id', 1)
    ->where('ano', 2025)
    ->where('mes', 12)
    ->get();

echo "=== ESCALAS PUBLICADAS (Dezembro 2025) ===\n";
echo "Total de publicações: " . $publicadas->count() . "\n\n";

foreach ($publicadas as $pub) {
    echo "Publicação ID: {$pub->id}\n";
    echo "Período: {$pub->mes}/{$pub->ano}\n";
    echo "Criada em: {$pub->created_at}\n";

    $totalSlots = $pub->alocacoes()->count();
    $preenchidos = $pub->alocacoes()->whereNotNull('plantonista_id')->where('status', 'preenchido')->count();
    $vazios = $pub->alocacoes()->whereNull('plantonista_id')->count();
    $statusVago = $pub->alocacoes()->where('status', 'vago')->count();

    echo "Total de slots: $totalSlots\n";
    echo "Preenchidos (plantonista_id NOT NULL + status preenchido): $preenchidos\n";
    echo "Vazios (plantonista_id NULL): $vazios\n";
    echo "Status 'vago': $statusVago\n\n";

    // Mostrar sample de 10 alocações
    echo "Sample de alocações publicadas (primeiras 10):\n";
    $samplesPubl = $pub->alocacoes()->orderBy('data')->limit(10)->get();
    foreach ($samplesPubl as $aloc) {
        $plantonista = $aloc->plantonista_id ? "Médico ID: {$aloc->plantonista_id}" : "NULL";
        echo "  - Data: {$aloc->data}, Turno {$aloc->turno_id}, Setor {$aloc->setor_id}, Status: {$aloc->status}, Plantonista: $plantonista\n";
    }
    echo "\n";

    // Verificar se há algum com plantonista
    $comPlantonistaPub = $pub->alocacoes()->whereNotNull('plantonista_id')->count();
    echo "ATENÇÃO: Alocações com plantonista_id preenchido: $comPlantonistaPub\n";

    if ($comPlantonistaPub > 0) {
        echo "Exemplos de alocações COM plantonista:\n";
        $comPlantonistas = $pub->alocacoes()->whereNotNull('plantonista_id')->limit(5)->get();
        foreach ($comPlantonistas as $aloc) {
            echo "  - Data: {$aloc->data}, Plantonista: {$aloc->plantonista_id}, Status: {$aloc->status}\n";
        }
    }
    echo "\n";
}

echo "=== DIAGNÓSTICO ===\n";
if ($comPlantonista > 0 && $publicadas->count() > 0) {
    $pub = $publicadas->first();
    $preenchidosPub = $pub->alocacoes()->whereNotNull('plantonista_id')->count();

    if ($preenchidosPub == 0) {
        echo "❌ PROBLEMA IDENTIFICADO: Template tem $comPlantonista médicos alocados, mas publicação tem 0 médicos!\n";
        echo "Provável causa: O método publicar() NÃO está copiando os plantonistas do template.\n";
    } else {
        echo "✅ Sistema está copiando médicos do template para publicação.\n";
        echo "Template: $comPlantonista médicos | Publicação: $preenchidosPub médicos\n";
    }
}
