<?php
require __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;

echo "=== COMPARAÇÃO DE SEMANAS - DEZEMBRO 2025 ===\n\n";

// Teste para dezembro de 2025
$ano = 2025;
$mes = 12;
$diasNoMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

echo "LÓGICA DO DISPLAY (EscalaPublicadaController):\n";
echo str_repeat("-", 80) . "\n";
echo sprintf("%-5s %-12s %-15s %-10s\n", "Dia", "Data", "Dia Semana", "Semana");
echo str_repeat("-", 80) . "\n";

for ($dia = 1; $dia <= $diasNoMes; $dia++) {
    $data = Carbon::create($ano, $mes, $dia);

    // LÓGICA DO DISPLAY
    $semanaDisplay = (int) ceil($dia / 7);

    echo sprintf(
        "%-5d %-12s %-15s %-10d\n",
        $dia,
        $data->format('d/m/Y'),
        $data->locale('pt_BR')->isoFormat('dddd'),
        $semanaDisplay
    );
}

echo "\n\n";
echo "LÓGICA DA PUBLICAÇÃO (EscalaPadraoController):\n";
echo str_repeat("-", 80) . "\n";
echo sprintf("%-5s %-12s %-15s %-12s %-15s\n", "Dia", "Data", "Dia Semana", "SemanaMês", "SemCiclo(1-5)");
echo str_repeat("-", 80) . "\n";

for ($dia = 1; $dia <= $diasNoMes; $dia++) {
    $data = Carbon::create($ano, $mes, $dia);

    // LÓGICA DA PUBLICAÇÃO
    $semanaMes = (int) ceil($dia / 7);
    $numeroDaSemana = (($semanaMes - 1) % 5) + 1;

    echo sprintf(
        "%-5d %-12s %-15s %-12d %-15d\n",
        $dia,
        $data->format('d/m/Y'),
        $data->locale('pt_BR')->isoFormat('dddd'),
        $semanaMes,
        $numeroDaSemana
    );
}

echo "\n\n";
echo "=== ANÁLISE DE DIFERENÇAS ===\n";
echo "A lógica do DISPLAY mostra a SEMANA DO MÊS (1-5)\n";
echo "A lógica da PUBLICAÇÃO calcula:\n";
echo "  1. SemanaMês (1-5 baseado em ceil(dia/7))\n";
echo "  2. SemCiclo = ((SemanaMês - 1) % 5) + 1\n";
echo "\nPara meses com 31 dias:\n";
echo "  - Dias 1-7   => SemanaMês=1 => SemCiclo=1\n";
echo "  - Dias 8-14  => SemanaMês=2 => SemCiclo=2\n";
echo "  - Dias 15-21 => SemanaMês=3 => SemCiclo=3\n";
echo "  - Dias 22-28 => SemanaMês=4 => SemCiclo=4\n";
echo "  - Dias 29-31 => SemanaMês=5 => SemCiclo=5\n";
echo "\nO PROBLEMA: Display mostra 1-5, mas NÃO usa módulo 5!\n";
echo "Se o mês tem 5 semanas, ambos mostram 1-5.\n";
echo "MAS o template é um CICLO de 5 semanas que pode repetir!\n\n";

// Teste com novembro (30 dias - 5 semanas)
echo "\n=== TESTE NOVEMBRO 2025 (30 dias) ===\n";
$mes = 11;
$diasNoMes = cal_days_in_month(CAL_GREGORIAN, $mes, 2025);
echo sprintf("%-5s %-12s %-12s %-15s\n", "Dia", "SemanaMês", "SemCiclo", "DIFERENÇA?");
echo str_repeat("-", 60) . "\n";

for ($dia = 1; $dia <= $diasNoMes; $dia++) {
    $semanaMes = (int) ceil($dia / 7);
    $numeroDaSemana = (($semanaMes - 1) % 5) + 1;
    $diff = ($semanaMes == $numeroDaSemana) ? "OK" : "DIFERENTE!";

    echo sprintf("%-5d %-12d %-15d %-15s\n", $dia, $semanaMes, $numeroDaSemana, $diff);
}

echo "\n=== CONCLUSÃO ===\n";
echo "Para meses com 28-31 dias (4-5 semanas), SemanaMês == SemCiclo sempre!\n";
echo "O módulo 5 só faria diferença se houvesse mais de 5 semanas no mês.\n";
echo "MAS: o problema REAL pode estar na correspondência com o TEMPLATE!\n";
