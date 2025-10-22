<?php

require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE REPUBLICAÇÃO COM CORREÇÃO DE BURACOS ===\n\n";

// Deletar publicação antiga
$publicacaoAntiga = App\Models\EscalaPublicada::where('unidade_id', 1)
    ->where('ano', 2025)
    ->where('mes', 12)
    ->first();

if ($publicacaoAntiga) {
    echo "Deletando publicação antiga ID: {$publicacaoAntiga->id}\n";
    $publicacaoAntiga->alocacoes()->delete();
    $publicacaoAntiga->delete();
}

$escalaPadrao = App\Models\EscalaPadrao::where('unidade_id', 1)->first();
$unidade = App\Models\Unidade::find(1);

echo "Processando: {$unidade->nome}\n";
echo "Escala Padrão ID: {$escalaPadrao->id}\n\n";

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

    $dias = [0 => 'domingo', 1 => 'segunda', 2 => 'terca', 3 => 'quarta', 4 => 'quinta', 5 => 'sexta', 6 => 'sabado'];
    $nomeDiaSemana = $dias[$dataAtual->dayOfWeek];

    echo "Data: 1 de dezembro 2025 (segunda-feira)\n";
    echo "Semana do ciclo: $numeroDaSemana\n";
    echo "Número do dia: $numeroDiaSemana\n\n";

    // Buscar dia template
    $diaTemplate = App\Models\DiaTemplate::whereHas('semanaTemplate', function ($q) use ($escalaPadrao, $numeroDaSemana) {
        $q->where('escala_padrao_id', $escalaPadrao->id)
            ->where('numero_semana', $numeroDaSemana);
    })
        ->where('dia_semana', $nomeDiaSemana)
        ->with('configuracoes.turno', 'configuracoes.setor')
        ->first();

    if (!$diaTemplate) {
        echo "❌ DiaTemplate não encontrado!\n";
        Illuminate\Support\Facades\DB::rollBack();
        exit;
    }

    echo "✅ DiaTemplate encontrado\n";
    echo "Configurações: " . $diaTemplate->configuracoes->count() . "\n\n";

    // Buscar alocações template
    $alocacoesTemplate = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
        ->where('semana', $numeroDaSemana)
        ->where('dia', $numeroDiaSemana)
        ->get()
        ->keyBy(function ($item) {
            return "{$item->turno_id}-{$item->setor_id}-{$item->slot}";
        });

    echo "Alocações template encontradas: " . $alocacoesTemplate->count() . "\n\n";

    // Processar TODAS as configurações
    echo "=== PROCESSANDO SLOTS ===\n\n";

    $totalSlots = 0;
    $preenchidos = 0;
    $buracos = 0;

    foreach ($diaTemplate->configuracoes as $config) {
        $turno = $config->turno;
        $setor = $config->setor;

        echo "Turno: {$turno->nome} | Setor: {$setor->nome} | Qty: {$config->quantidade_necessaria}\n";

        for ($slot = 1; $slot <= $config->quantidade_necessaria; $slot++) {
            $chave = "{$config->turno_id}-{$config->setor_id}-{$slot}";
            $alocTemplate = $alocacoesTemplate->get($chave);

            $plantonista_id = $alocTemplate ? $alocTemplate->plantonista_id : null;
            $status = ($alocTemplate && $alocTemplate->plantonista_id) ? 'preenchido' : 'vago';

            App\Models\AlocacaoPublicada::create([
                'escala_publicada_id' => $escalaPublicada->id,
                'data' => $dataAtual->format('Y-m-d'),
                'turno_id' => $config->turno_id,
                'setor_id' => $config->setor_id,
                'plantonista_id' => $plantonista_id,
                'status' => $status,
                'observacoes' => $config->observacoes,
            ]);

            $totalSlots++;
            if ($status == 'preenchido') {
                $preenchidos++;
                echo "  - Slot $slot: Médico ID $plantonista_id ✅\n";
            } else {
                $buracos++;
                echo "  - Slot $slot: BURACO ⚠️\n";
            }
        }
        echo "\n";
    }

    Illuminate\Support\Facades\DB::commit();

    echo "=== RESULTADO FINAL ===\n";
    echo "Total de slots: $totalSlots\n";
    echo "Preenchidos: $preenchidos\n";
    echo "Buracos: $buracos\n";
    echo "Taxa: " . ($totalSlots > 0 ? round(($preenchidos / $totalSlots) * 100, 2) : 0) . "%\n";
} catch (Exception $e) {
    Illuminate\Support\Facades\DB::rollBack();
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
