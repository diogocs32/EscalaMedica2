<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\EscalaPadrao;
use App\Models\EscalaPublicada;
use App\Models\AlocacaoPublicada;
use App\Models\DiaTemplate;
use App\Models\AlocacaoTemplate;
use App\Models\ConfiguracaoTurnoSetor;

echo "=== REPUBLICANDO DEZEMBRO 2025 ===\n\n";

$escalaPadrao = EscalaPadrao::latest()->first();
if (!$escalaPadrao) {
    echo "❌ Nenhuma escala padrão encontrada!\n";
    exit(1);
}

echo "Escala Padrão: {$escalaPadrao->nome}\n";
echo "Vigência: {$escalaPadrao->vigencia_inicio}\n\n";

$ano = 2025;
$mes = 12;

// Buscar unidade
$unidade = \App\Models\Unidade::first();
if (!$unidade) {
    echo "❌ Nenhuma unidade encontrada!\n";
    exit(1);
}

echo "Unidade: {$unidade->nome}\n\n";

// Criar a escala publicada
$escalaPublicada = EscalaPublicada::create([
    'ano' => $ano,
    'mes' => $mes,
    'unidade_id' => $unidade->id,
    'escala_padrao_id' => $escalaPadrao->id,
    'status' => 'publicado',
]);

echo "✅ Escala publicada criada (ID: {$escalaPublicada->id})\n\n";

$diasNoMes = cal_days_in_month(CAL_GREGORIAN, (int)$mes, (int)$ano);
$vigenciaInicio = \Carbon\Carbon::parse($escalaPadrao->vigencia_inicio)->startOfDay();
$totalAlocacoes = 0;

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

echo "Processando dias...\n";
echo str_repeat("-", 80) . "\n";
echo sprintf(
    "%-5s %-12s %-12s %-10s %-10s %-15s\n",
    "Dia",
    "Data",
    "DiaSemana",
    "SemMês",
    "SemCiclo",
    "Alocações"
);
echo str_repeat("-", 80) . "\n";

for ($dia = 1; $dia <= $diasNoMes; $dia++) {
    $dataAtual = \Carbon\Carbon::create($ano, $mes, $dia)->startOfDay();

    // Calcular semana do mês: blocos de 7 dias (1-7, 8-14, 15-21, 22-28, 29-31)
    $semanaMes = (int) ceil($dia / 7);

    // Calcular qual semana do ciclo de 5 semanas (1-5)
    $numeroDaSemana = (($semanaMes - 1) % 5) + 1;

    // Nome do dia da semana (segunda, terca, etc)
    $nomeDiaSemana = getNomeDiaSemana($dataAtual->dayOfWeek);

    // Número do dia da semana (1=domingo, 2=segunda, ..., 7=sábado)
    $numeroDiaSemana = $dataAtual->dayOfWeek + 1;

    // Buscar configurações do dia correspondente no template
    $diaTemplate = DiaTemplate::whereHas('semanaTemplate', function ($q) use ($escalaPadrao, $numeroDaSemana) {
        $q->where('escala_padrao_id', $escalaPadrao->id)
            ->where('numero_semana', $numeroDaSemana);
    })
        ->where('dia_semana', $nomeDiaSemana)
        ->with('configuracoes.turno', 'configuracoes.setor')
        ->first();

    $alocacoesCount = 0;

    if ($diaTemplate) {
        // Buscar alocações template
        $alocacoesTemplate = AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
            ->where('semana', $numeroDaSemana)
            ->where('dia', $numeroDiaSemana)
            ->get()
            ->keyBy(function ($item) {
                return "{$item->turno_id}-{$item->setor_id}-{$item->slot}";
            });

        // Processar configurações
        foreach ($diaTemplate->configuracoes as $config) {
            for ($slot = 1; $slot <= $config->quantidade_necessaria; $slot++) {
                $chave = "{$config->turno_id}-{$config->setor_id}-{$slot}";
                $alocTemplate = $alocacoesTemplate[$chave] ?? null;

                AlocacaoPublicada::create([
                    'escala_publicada_id' => $escalaPublicada->id,
                    'data' => $dataAtual->format('Y-m-d'),
                    'turno_id' => $config->turno_id,
                    'setor_id' => $config->setor_id,
                    'plantonista_id' => $alocTemplate->plantonista_id ?? null,
                    'status' => $alocTemplate ? 'preenchido' : 'vago',
                    'observacoes' => null,
                ]);

                $alocacoesCount++;
                $totalAlocacoes++;
            }
        }
    }

    echo sprintf(
        "%-5d %-12s %-12s %-10d %-10d %-15d\n",
        $dia,
        $dataAtual->format('d/m'),
        $nomeDiaSemana,
        $semanaMes,
        $numeroDaSemana,
        $alocacoesCount
    );
}

echo str_repeat("-", 80) . "\n";
echo "\n✅ Publicação concluída!\n";
echo "Total de alocações criadas: $totalAlocacoes\n\n";

// Verificar terça-feira da semana 1
echo "=== VERIFICAÇÃO: TERÇA-FEIRA SEMANA 1 (02/12) ===\n";
$alocacoesTerca = AlocacaoPublicada::where('escala_publicada_id', $escalaPublicada->id)
    ->where('data', '2025-12-02')
    ->with('turno', 'setor', 'plantonista')
    ->orderBy('turno_id')
    ->get();

echo "Total: {$alocacoesTerca->count()}\n\n";
echo sprintf("%-20s %-25s %-25s %-12s\n", "Turno", "Setor", "Plantonista", "Status");
echo str_repeat("-", 85) . "\n";

foreach ($alocacoesTerca as $aloc) {
    echo sprintf(
        "%-20s %-25s %-25s %-12s\n",
        $aloc->turno->nome ?? 'N/A',
        $aloc->setor->nome ?? 'N/A',
        $aloc->plantonista->nome ?? 'VAGO',
        $aloc->status
    );
}
