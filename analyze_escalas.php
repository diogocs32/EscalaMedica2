<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "===== ANÁLISE DO SISTEMA DE PUBLICAÇÃO =====\n\n";

// 1. Verificar Escalas Publicadas
echo "1. ESCALAS PUBLICADAS:\n";
$escalas = \App\Models\EscalaPublicada::with(['unidade.cidade', 'escalaPadrao'])->get();
foreach ($escalas as $escala) {
    echo "  - ID: {$escala->id}\n";
    echo "    Unidade: {$escala->unidade->nome}\n";
    echo "    Cidade: {$escala->unidade->cidade->nome}\n";
    echo "    Período: {$escala->mes}/{$escala->ano}\n";
    echo "    Status: {$escala->status}\n";
    echo "    Total Alocações: " . $escala->alocacoes()->count() . "\n";
    echo "    Com Plantonista: " . $escala->alocacoes()->whereNotNull('plantonista_id')->count() . "\n\n";
}

// 2. Verificar Alocações Publicadas por Escala
echo "\n2. ALOCAÇÕES PUBLICADAS (primeiras 10):\n";
$alocacoes = \App\Models\AlocacaoPublicada::with(['turno', 'setor', 'plantonista'])
    ->orderBy('escala_publicada_id')
    ->orderBy('data')
    ->take(10)
    ->get();

foreach ($alocacoes as $aloc) {
    echo "  - Escala ID: {$aloc->escala_publicada_id} | Data: {$aloc->data} | ";
    echo "Turno: {$aloc->turno->nome} | Setor: {$aloc->setor->nome} | ";
    echo "Status: {$aloc->status} | ";
    echo "Plantonista: " . ($aloc->plantonista ? $aloc->plantonista->nome : 'VAGO') . "\n";
}

// 3. Verificar Configurações na Escala Padrão
echo "\n3. ESCALA PADRÃO (Telemedicina):\n";
$unidadeTele = \App\Models\Unidade::where('nome', 'LIKE', '%Telemedicina%')->first();
if ($unidadeTele) {
    echo "  Unidade encontrada: {$unidadeTele->nome} (ID: {$unidadeTele->id})\n";

    $escalaPadrao = \App\Models\EscalaPadrao::where('unidade_id', $unidadeTele->id)->first();
    if ($escalaPadrao) {
        echo "  Escala Padrão ID: {$escalaPadrao->id}\n";
        echo "  Vigência Início: {$escalaPadrao->vigencia_inicio}\n\n";

        $totalConfigs = 0;
        foreach ($escalaPadrao->semanas as $semana) {
            echo "  SEMANA {$semana->numero_semana}:\n";
            foreach ($semana->dias as $dia) {
                $configs = $dia->configuracoes()->with(['turno', 'setor'])->get();
                echo "    {$dia->dia_semana}: {$configs->count()} configurações\n";
                foreach ($configs as $config) {
                    echo "      - {$config->turno->nome} / {$config->setor->nome} / Qty: {$config->quantidade_necessaria}\n";
                    $totalConfigs += $config->quantidade_necessaria;
                }
            }
        }
        echo "\n  TOTAL DE SLOTS NO PADRÃO: {$totalConfigs}\n";
    } else {
        echo "  ⚠️ Escala Padrão NÃO encontrada!\n";
    }
} else {
    echo "  ⚠️ Unidade Telemedicina NÃO encontrada!\n";
}

// 4. Verificar dados da publicação específica de Telemedicina
echo "\n4. PUBLICAÇÃO DE TELEMEDICINA:\n";
if ($unidadeTele) {
    $publicacao = \App\Models\EscalaPublicada::where('unidade_id', $unidadeTele->id)->first();
    if ($publicacao) {
        echo "  Publicação ID: {$publicacao->id}\n";
        echo "  Período: {$publicacao->mes}/{$publicacao->ano}\n";

        $alocacoesPub = $publicacao->alocacoes()->with(['turno', 'setor'])->get();
        echo "  Total Alocações Criadas: {$alocacoesPub->count()}\n";

        // Agrupar por data
        $porData = $alocacoesPub->groupBy('data');
        echo "  Dias com alocações: " . $porData->count() . "\n";

        // Mostrar exemplo do primeiro dia
        $primeiraData = $porData->keys()->first();
        if ($primeiraData) {
            echo "\n  Exemplo - Dia {$primeiraData}:\n";
            foreach ($porData[$primeiraData] as $aloc) {
                echo "    - {$aloc->turno->nome} / {$aloc->setor->nome} / Status: {$aloc->status}\n";
            }
        }
    } else {
        echo "  ⚠️ Publicação NÃO encontrada!\n";
    }
}

echo "\n===== FIM DA ANÁLISE =====\n";
