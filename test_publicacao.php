<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "===== TESTE DE PUBLICAÇÃO CORRIGIDO =====\n\n";

// Buscar unidade Telemedicina
$unidade = \App\Models\Unidade::where('nome', 'LIKE', '%Telemedicina%')->first();
if (!$unidade) {
    echo "❌ Unidade não encontrada!\n";
    exit(1);
}

echo "✅ Unidade: {$unidade->nome} (ID: {$unidade->id})\n";

// Buscar escala padrão
$escalaPadrao = $unidade->escalaPadrao()->where('status', 'ativo')->first();
if (!$escalaPadrao) {
    echo "❌ Escala padrão não encontrada!\n";
    exit(1);
}

echo "✅ Escala Padrão ID: {$escalaPadrao->id}\n";
echo "✅ Vigência: {$escalaPadrao->vigencia_inicio}\n\n";

// Simular publicação para outubro/2025
$ano = 2025;
$mes = 10;

echo "📅 Simulando publicação para {$mes}/{$ano}...\n\n";

// Contar configurações no padrão
$totalConfigsPadrao = 0;
foreach ($escalaPadrao->semanas as $semana) {
    foreach ($semana->dias as $dia) {
        foreach ($dia->configuracoes as $config) {
            $totalConfigsPadrao += $config->quantidade_necessaria;
        }
    }
}
echo "📊 Total de slots no padrão: {$totalConfigsPadrao}\n\n";

// Criar publicação via código (simulando o controller)
DB::beginTransaction();

try {
    $escalaPublicada = \App\Models\EscalaPublicada::create([
        'unidade_id' => $unidade->id,
        'escala_padrao_id' => $escalaPadrao->id,
        'ano' => $ano,
        'mes' => sprintf('%02d', $mes),
        'status' => 'em_edicao',
    ]);

    echo "✅ Escala publicada criada (ID: {$escalaPublicada->id})\n\n";

    // Processar cada dia do mês
    $diasNoMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
    echo "📅 Processando {$diasNoMes} dias do mês...\n\n";

    $vigenciaInicio = \Carbon\Carbon::parse($escalaPadrao->vigencia_inicio)->startOfDay();
    $slotsGerados = 0;

    for ($dia = 1; $dia <= $diasNoMes; $dia++) {
        $dataAtual = \Carbon\Carbon::create($ano, $mes, $dia)->startOfDay();

        // Calcular qual semana do ciclo (1-5) com suporte a datas antes da vigência
        $diasDesdeVigencia = $dataAtual->diffInDays($vigenciaInicio, false); // signed diff
        $numeroDaSemana = (int)(floor($diasDesdeVigencia / 7) % 5) + 1;

        // Garantir que o número da semana esteja entre 1 e 5
        if ($numeroDaSemana <= 0) {
            $numeroDaSemana = 5 + ($numeroDaSemana % 5);
        }

        // Nome do dia da semana
        $nomeDiaSemana = [
            0 => 'domingo',
            1 => 'segunda',
            2 => 'terca',
            3 => 'quarta',
            4 => 'quinta',
            5 => 'sexta',
            6 => 'sabado'
        ][$dataAtual->dayOfWeek];

        // Buscar template
        $diaTemplate = \App\Models\DiaTemplate::whereHas('semanaTemplate', function ($q) use ($escalaPadrao, $numeroDaSemana) {
            $q->where('escala_padrao_id', $escalaPadrao->id)
                ->where('numero_semana', $numeroDaSemana);
        })
            ->where('dia_semana', $nomeDiaSemana)
            ->with('configuracoes')
            ->first();

        if (!$diaTemplate) {
            echo sprintf("⚠️  Dia %02d/%02d (%s): Sem configuração\n", $dia, $mes, $nomeDiaSemana);
            continue;
        }

        $slotsNoDia = 0;
        foreach ($diaTemplate->configuracoes as $config) {
            for ($slot = 1; $slot <= $config->quantidade_necessaria; $slot++) {
                \App\Models\AlocacaoPublicada::create([
                    'escala_publicada_id' => $escalaPublicada->id,
                    'data' => $dataAtual->format('Y-m-d'),
                    'turno_id' => $config->turno_id,
                    'setor_id' => $config->setor_id,
                    'plantonista_id' => null,
                    'status' => 'vago',
                    'observacoes' => $config->observacoes,
                ]);
                $slotsNoDia++;
                $slotsGerados++;
            }
        }

        echo sprintf("✅ Dia %02d/%02d (%s, Semana %d): %d slots\n", $dia, $mes, $nomeDiaSemana, $numeroDaSemana, $slotsNoDia);
    }

    DB::commit();

    echo "\n===== RESULTADO =====\n";
    echo "✅ Publicação concluída com sucesso!\n";
    echo "📊 Total de slots gerados: {$slotsGerados}\n";
    echo "📊 Total esperado (padrão): {$totalConfigsPadrao}\n";

    if ($slotsGerados == $totalConfigsPadrao) {
        echo "🎉 PERFEITO! Quantidade CORRETA de slots!\n";
    } else {
        echo "⚠️  ATENÇÃO! Diferença de " . abs($slotsGerados - $totalConfigsPadrao) . " slots\n";
    }

    // Verificar métricas
    $escalaPublicada->refresh();
    echo "\n📈 MÉTRICAS:\n";
    echo "  - Total Slots: {$escalaPublicada->total_slots}\n";
    echo "  - Preenchidos: {$escalaPublicada->preenchidos}\n";
    echo "  - Buracos: {$escalaPublicada->buracos}\n";
    echo "  - Taxa: {$escalaPublicada->taxa}%\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "\n===== FIM DO TESTE =====\n";
