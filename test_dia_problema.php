<?php
require __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;

echo "=== PROBLEMA: NUMERAÇÃO DOS DIAS DA SEMANA ===\n\n";

echo "DEZEMBRO 2025:\n";
echo str_repeat("-", 80) . "\n";
echo sprintf(
    "%-5s %-12s %-15s %-15s %-15s\n",
    "Dia",
    "Data",
    "Dia Semana",
    "dayOfWeek",
    "dia# Template"
);
echo str_repeat("-", 80) . "\n";

for ($dia = 1; $dia <= 7; $dia++) {
    $data = Carbon::create(2025, 12, $dia);
    $dayOfWeek = $data->dayOfWeek; // Carbon: 0=domingo, 1=segunda, 2=terça...
    $numeroDiaSemana = $dayOfWeek + 1; // Convertido: 1=domingo, 2=segunda, 3=terça...

    echo sprintf(
        "%-5d %-12s %-15s %-15d %-15d\n",
        $dia,
        $data->format('d/m/Y'),
        $data->locale('pt_BR')->isoFormat('dddd'),
        $dayOfWeek,
        $numeroDiaSemana
    );
}

echo "\n\n";
echo "PROBLEMA IDENTIFICADO:\n";
echo str_repeat("=", 80) . "\n";
echo "02/12/2025 é TERÇA-FEIRA\n";
echo "  - Carbon dayOfWeek = 2 (terça)\n";
echo "  - numeroDiaSemana = 2 + 1 = 3\n";
echo "  - Mas 02/12 é o DIA 2 DO MÊS, não o dia 3!\n\n";

echo "O campo 'dia' na tabela alocacoes_template armazena:\n";
echo "  1 = Domingo\n";
echo "  2 = Segunda\n";
echo "  3 = Terça\n";
echo "  4 = Quarta\n";
echo "  5 = Quinta\n";
echo "  6 = Sexta\n";
echo "  7 = Sábado\n\n";

echo "Mas o dia 02/12/2025 (segunda-feira) está buscando dia# 3 (terça) no template!\n";
echo "Quando deveria buscar baseado no NOME do dia da semana, não no número!\n\n";

echo str_repeat("=", 80) . "\n";
echo "MAPEAMENTO CORRETO PARA DEZEMBRO 2025:\n";
echo str_repeat("=", 80) . "\n";
echo sprintf(
    "%-8s %-12s %-15s %-20s\n",
    "Dia Mês",
    "Data",
    "Dia Semana",
    "Deve buscar dia#"
);
echo str_repeat("-", 80) . "\n";

for ($dia = 1; $dia <= 7; $dia++) {
    $data = Carbon::create(2025, 12, $dia);
    $dayOfWeek = $data->dayOfWeek;
    $nomeDia = $data->locale('pt_BR')->isoFormat('dddd');

    // Mapeamento correto: baseado no DIA DA SEMANA, não no dia do mês
    $diaNomes = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];
    $diaTemplate = [
        'domingo' => 1,
        'segunda-feira' => 2,
        'terça-feira' => 3,
        'quarta-feira' => 4,
        'quinta-feira' => 5,
        'sexta-feira' => 6,
        'sábado' => 7
    ];

    $deveBuscar = $diaTemplate[$nomeDia] ?? '?';

    echo sprintf(
        "%-8d %-12s %-15s %-20s\n",
        $dia,
        $data->format('d/m/Y'),
        $nomeDia,
        "$deveBuscar ($nomeDia)"
    );
}

echo "\n\n";
echo "VERIFICAÇÃO: O código atual usa:\n";
echo "  \$numeroDiaSemana = \$dataAtual->dayOfWeek + 1;\n";
echo "  ->where('dia', \$numeroDiaSemana)\n\n";
echo "Isso está CORRETO! O problema NÃO é o código de publicação!\n";
echo "O problema pode estar nas ALOCAÇÕES PUBLICADAS existentes!\n";
