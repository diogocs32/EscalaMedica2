<?php

require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ANÁLISE DETALHADA: CONFIGURAÇÃO vs ALOCAÇÕES ===\n\n";

$escalaPadrao = App\Models\EscalaPadrao::where('unidade_id', 1)->first();
echo "Escala Padrão ID: {$escalaPadrao->id}\n";
echo "Unidade: {$escalaPadrao->unidade->nome}\n\n";

// Verificar TODAS as semanas para encontrar buracos
echo "=== ANALISANDO TODAS AS 5 SEMANAS ===\n\n";

for ($semana = 1; $semana <= 5; $semana++) {
    echo "--- SEMANA $semana ---\n";

    $semanaTemplate = App\Models\SemanaTemplate::where('escala_padrao_id', $escalaPadrao->id)
        ->where('numero_semana', $semana)
        ->first();

    if (!$semanaTemplate) {
        echo "  Semana não encontrada!\n\n";
        continue;
    }

    $dias = App\Models\DiaTemplate::where('semana_template_id', $semanaTemplate->id)->get();

    foreach ($dias as $diaTemplate) {
        // Buscar configurações
        $configuracoes = App\Models\ConfiguracaoTurnoSetor::where('dia_template_id', $diaTemplate->id)->get();

        if ($configuracoes->isEmpty()) continue;

        echo "  {$diaTemplate->dia_semana}:\n";

        // Calcular total esperado
        $totalEsperado = $configuracoes->sum('quantidade_necessaria');

        // Buscar alocações reais (numeroDia: 1=domingo, 2=segunda, etc)
        $mapaDias = [
            'domingo' => 1,
            'segunda' => 2,
            'terca' => 3,
            'quarta' => 4,
            'quinta' => 5,
            'sexta' => 6,
            'sabado' => 7
        ];
        $numeroDia = $mapaDias[$diaTemplate->dia_semana] ?? 0;

        $alocacoes = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
            ->where('semana', $semana)
            ->where('dia', $numeroDia)
            ->get();

        $preenchidos = $alocacoes->whereNotNull('plantonista_id')->count();
        $buracos = $alocacoes->whereNull('plantonista_id')->count();
        $totalAlocacoes = $alocacoes->count();

        echo "    Total esperado (config): $totalEsperado\n";
        echo "    Total alocações: $totalAlocacoes\n";
        echo "    Preenchidos: $preenchidos\n";
        echo "    Buracos (plantonista null): $buracos\n";

        // PROBLEMA: Se totalAlocacoes < totalEsperado, há slots que nem foram criados!
        $slotsNaoCriados = $totalEsperado - $totalAlocacoes;
        if ($slotsNaoCriados > 0) {
            echo "    ⚠️ SLOTS NÃO CRIADOS: $slotsNaoCriados\n";
        }

        // Detalhar por turno+setor
        foreach ($configuracoes as $config) {
            $turno = $config->turno;
            $setor = $config->setor;

            $alocacoesConfig = $alocacoes->where('turno_id', $config->turno_id)
                ->where('setor_id', $config->setor_id);

            $qtdReal = $alocacoesConfig->count();
            $qtdConfig = $config->quantidade_necessaria;

            if ($qtdReal != $qtdConfig) {
                echo "      ⚠️ {$turno->nome} / {$setor->nome}: Config={$qtdConfig}, Real={$qtdReal}\n";

                // Mostrar quais slots existem
                $slotsExistentes = $alocacoesConfig->pluck('slot')->sort()->values();
                echo "         Slots existentes: " . $slotsExistentes->implode(', ') . "\n";

                $slotsFaltando = collect(range(1, $qtdConfig))
                    ->diff($slotsExistentes)
                    ->values();
                if ($slotsFaltando->isNotEmpty()) {
                    echo "         Slots faltando: " . $slotsFaltando->implode(', ') . "\n";
                }
            }
        }
        echo "\n";
    }
}

echo "\n=== RESUMO GERAL ===\n";
$totalConfigs = App\Models\ConfiguracaoTurnoSetor::whereHas('diaTemplate.semanaTemplate', function ($q) use ($escalaPadrao) {
    $q->where('escala_padrao_id', $escalaPadrao->id);
})->get();

$totalEsperadoGeral = $totalConfigs->sum('quantidade_necessaria');
$totalAlocacoesGeral = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)->count();
$preenchidosGeral = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)->whereNotNull('plantonista_id')->count();

echo "Total de slots esperados (soma das configs): $totalEsperadoGeral\n";
echo "Total de alocações criadas: $totalAlocacoesGeral\n";
echo "Preenchidos: $preenchidosGeral\n";
echo "Buracos (slots não criados): " . ($totalEsperadoGeral - $totalAlocacoesGeral) . "\n";
echo "Buracos (null em alocações): " . ($totalAlocacoesGeral - $preenchidosGeral) . "\n";
