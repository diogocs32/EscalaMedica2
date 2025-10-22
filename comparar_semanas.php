<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== COMPARAÇÃO COMPLETA: TEMPLATE VS PUBLICADO ===\n\n";

// Dezembro 2025
$ano = 2025;
$mes = 12;

$escalaPublicada = \App\Models\EscalaPublicada::where('ano', $ano)
    ->where('mes', $mes)
    ->first();

if (!$escalaPublicada) {
    echo "❌ Nenhuma escala publicada para $mes/$ano\n";
    exit(1);
}

$escalaPadrao = $escalaPublicada->escalaPadrao;

echo "Escala Publicada ID: {$escalaPublicada->id}\n";
echo "Escala Padrão: {$escalaPadrao->nome}\n\n";

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

// Agrupar por semana
$semanas = [1 => [], 2 => [], 3 => [], 4 => [], 5 => []];

$diasNoMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

for ($dia = 1; $dia <= $diasNoMes; $dia++) {
    $data = \Carbon\Carbon::create($ano, $mes, $dia);
    $semanaMes = (int) ceil($dia / 7);
    $numeroDaSemana = (($semanaMes - 1) % 5) + 1;

    $nomeDiaSemana = getNomeDiaSemana($data->dayOfWeek);
    $numeroDiaSemana = $data->dayOfWeek + 1;

    // Alocações template
    $alocacoesTemplate = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
        ->where('semana', $numeroDaSemana)
        ->where('dia', $numeroDiaSemana)
        ->count();

    // Alocações publicadas
    $alocacoesPublicadas = \App\Models\AlocacaoPublicada::where('escala_publicada_id', $escalaPublicada->id)
        ->where('data', $data->format('Y-m-d'))
        ->count();

    $semanas[$semanaMes][] = [
        'dia' => $dia,
        'data' => $data->format('d/m'),
        'dia_semana' => $nomeDiaSemana,
        'semana_ciclo' => $numeroDaSemana,
        'template' => $alocacoesTemplate,
        'publicado' => $alocacoesPublicadas,
        'diferenca' => $alocacoesPublicadas - $alocacoesTemplate
    ];
}

// Mostrar por semana
foreach ($semanas as $numSemana => $dias) {
    if (empty($dias)) continue;

    echo "\n" . str_repeat("=", 90) . "\n";
    echo "SEMANA $numSemana\n";
    echo str_repeat("=", 90) . "\n";
    echo sprintf(
        "%-5s %-8s %-12s %-12s %-10s %-10s %-10s\n",
        "Dia",
        "Data",
        "DiaSemana",
        "SemCiclo",
        "Template",
        "Publicado",
        "Diferença"
    );
    echo str_repeat("-", 90) . "\n";

    $totalTemplate = 0;
    $totalPublicado = 0;

    foreach ($dias as $info) {
        $mark = $info['diferenca'] != 0 ? " ⚠️" : "";
        echo sprintf(
            "%-5d %-8s %-12s %-12d %-10d %-10d %-10d%s\n",
            $info['dia'],
            $info['data'],
            $info['dia_semana'],
            $info['semana_ciclo'],
            $info['template'],
            $info['publicado'],
            $info['diferenca'],
            $mark
        );

        $totalTemplate += $info['template'];
        $totalPublicado += $info['publicado'];
    }

    echo str_repeat("-", 90) . "\n";
    echo sprintf(
        "%-40s %-10d %-10d %-10d\n",
        "TOTAL DA SEMANA:",
        $totalTemplate,
        $totalPublicado,
        $totalPublicado - $totalTemplate
    );
}

echo "\n\n" . str_repeat("=", 90) . "\n";
echo "RESUMO GERAL\n";
echo str_repeat("=", 90) . "\n";

$totalTemplateGeral = \App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->whereIn('semana', [1, 2, 3, 4, 5])
    ->count();

$totalPublicadoGeral = \App\Models\AlocacaoPublicada::where('escala_publicada_id', $escalaPublicada->id)
    ->count();

echo "Total de alocações no template (todas as semanas): $totalTemplateGeral\n";
echo "Total de alocações publicadas (dezembro): $totalPublicadoGeral\n";
echo "Diferença: " . ($totalPublicadoGeral - $totalTemplateGeral) . "\n\n";

echo "⚠️ = Dia com diferença entre template e publicado\n";
echo "Isso pode ocorrer quando:\n";
echo "  - ConfiguracaoTurnoSetor define quantidade_necessaria > alocações template\n";
echo "  - Há slots vagos que não estavam no template\n";
