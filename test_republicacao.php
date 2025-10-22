<?php

require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE REPUBLICAÇÃO CORRIGIDA ===\n\n";

// Deletar publicação antiga de dezembro
$publicacaoAntiga = App\Models\EscalaPublicada::where('unidade_id', 1)
    ->where('ano', 2025)
    ->where('mes', 12)
    ->first();

if ($publicacaoAntiga) {
    echo "Deletando publicação antiga ID: {$publicacaoAntiga->id}\n";
    $publicacaoAntiga->alocacoes()->delete();
    $publicacaoAntiga->delete();
    echo "✅ Publicação antiga removida\n\n";
}

// Buscar escala padrão
$escalaPadrao = App\Models\EscalaPadrao::where('unidade_id', 1)->first();
$unidade = App\Models\Unidade::find(1);

echo "Processando: {$unidade->nome}\n";
echo "Escala Padrão ID: {$escalaPadrao->id}\n";
echo "Vigência: {$escalaPadrao->vigencia_inicio}\n\n";

// Criar nova publicação
Illuminate\Support\Facades\DB::beginTransaction();

try {
    $escalaPublicada = App\Models\EscalaPublicada::create([
        'unidade_id' => $unidade->id,
        'escala_padrao_id' => $escalaPadrao->id,
        'ano' => 2025,
        'mes' => 12,
        'status' => 'em_edicao',
    ]);

    echo "✅ Publicação criada ID: {$escalaPublicada->id}\n\n";

    // Processar primeiro dia (1 de dezembro)
    $dataAtual = Carbon\Carbon::create(2025, 12, 1)->startOfDay();
    $vigenciaInicio = Carbon\Carbon::parse($escalaPadrao->vigencia_inicio)->startOfDay();

    $diasDesdeVigencia = $dataAtual->diffInDays($vigenciaInicio, false);
    $numeroDaSemana = (int)(floor($diasDesdeVigencia / 7) % 5) + 1;
    if ($numeroDaSemana <= 0) {
        $numeroDaSemana = 5 + ($numeroDaSemana % 5);
    }

    $numeroDiaSemana = $dataAtual->dayOfWeek + 1;

    echo "Data: {$dataAtual->format('Y-m-d')} (dia da semana: $numeroDiaSemana)\n";
    echo "Semana do ciclo: $numeroDaSemana\n\n";

    // Buscar alocações template
    $alocacoesTemplate = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
        ->where('semana', $numeroDaSemana)
        ->where('dia', $numeroDiaSemana)
        ->orderBy('turno_id')
        ->orderBy('setor_id')
        ->orderBy('slot')
        ->get();

    echo "Alocações template encontradas: " . $alocacoesTemplate->count() . "\n\n";

    if ($alocacoesTemplate->count() > 0) {
        echo "Primeiras 10 alocações a serem clonadas:\n";
        foreach ($alocacoesTemplate->take(10) as $aloc) {
            $plantonista = $aloc->plantonista_id ? "Médico {$aloc->plantonista_id}" : "VAZIO";
            $status = $aloc->plantonista_id ? 'preenchido' : 'vago';
            echo "  - Turno {$aloc->turno_id}, Setor {$aloc->setor_id}, Slot {$aloc->slot}: $plantonista (status: $status)\n";

            // Criar alocação publicada
            App\Models\AlocacaoPublicada::create([
                'escala_publicada_id' => $escalaPublicada->id,
                'data' => $dataAtual->format('Y-m-d'),
                'turno_id' => $aloc->turno_id,
                'setor_id' => $aloc->setor_id,
                'plantonista_id' => $aloc->plantonista_id,
                'status' => $status,
                'observacoes' => null,
            ]);
        }
        echo "\n✅ Alocações criadas!\n\n";
    }

    Illuminate\Support\Facades\DB::commit();

    // Verificar resultado
    $totalSlots = $escalaPublicada->alocacoes()->count();
    $preenchidos = $escalaPublicada->alocacoes()->whereNotNull('plantonista_id')->count();
    $vazios = $escalaPublicada->alocacoes()->whereNull('plantonista_id')->count();

    echo "=== RESULTADO ===\n";
    echo "Total de slots: $totalSlots\n";
    echo "Preenchidos: $preenchidos\n";
    echo "Vazios: $vazios\n";
    echo "Taxa: " . ($totalSlots > 0 ? round(($preenchidos / $totalSlots) * 100, 2) : 0) . "%\n";
} catch (Exception $e) {
    Illuminate\Support\Facades\DB::rollBack();
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
