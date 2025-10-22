<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;

// Data de vigência
$dataVigencia = Carbon::create(2025, 12, 1);
$diaVigencia = $dataVigencia->dayOfWeek;
$domingoVigencia = $dataVigencia->copy()->subDays($diaVigencia);

echo "Data de Vigência: " . $dataVigencia->format('d/m/Y (l)') . " - DayOfWeek: " . $dataVigencia->dayOfWeek . "\n";
echo "Domingo da Vigência: " . $domingoVigencia->format('d/m/Y (l)') . "\n\n";

// Testar dias de dezembro
for ($dia = 1; $dia <= 31; $dia++) {
    $data = Carbon::create(2025, 12, $dia);
    $diaSemana = $data->dayOfWeek;

    // Encontrar o domingo da semana atual
    $domingoSemana = $data->copy()->subDays($diaSemana);

    // Calcular diferença em semanas entre os domingos
    $diffDias = $domingoVigencia->diffInDays($domingoSemana, false);
    $diffSemanas = floor($diffDias / 7);

    // Calcular qual semana do ciclo (1-5)
    $semana = ($diffSemanas % 5) + 1;
    while ($semana <= 0) {
        $semana += 5;
    }

    echo sprintf(
        "Dia %02d/12 (%9s) - Domingo: %s - DiffDias: %3d - DiffSemanas: %2d - Semana: %d\n",
        $dia,
        $data->format('l'),
        $domingoSemana->format('d/m'),
        $diffDias,
        $diffSemanas,
        $semana
    );
}
