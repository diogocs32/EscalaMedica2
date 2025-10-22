<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;

echo "=== OPÇÃO 1: Contar a partir do primeiro domingo do mês ===\n\n";

// Dezembro 2025
$primeiroDia = Carbon::create(2025, 12, 1);
$diaSemana = $primeiroDia->dayOfWeek;
$primeiroDomingo = $primeiroDia->copy()->subDays($diaSemana); // Domingo antes ou igual ao dia 1

echo "Primeiro dia: " . $primeiroDia->format('d/m/Y (l)') . "\n";
echo "Primeiro domingo: " . $primeiroDomingo->format('d/m/Y (l)') . "\n\n";

// Calcular semanas de dezembro com essa lógica
for ($dia = 1; $dia <= 31; $dia++) {
    $data = Carbon::create(2025, 12, $dia);
    $diaSemana = $data->dayOfWeek;
    $domingoSemana = $data->copy()->subDays($diaSemana);

    // Diferença do primeiro domingo
    $diffDias = $primeiroDomingo->diffInDays($domingoSemana, false);
    $diffSemanas = floor($diffDias / 7);
    $semana = ($diffSemanas % 5) + 1;
    while ($semana <= 0) {
        $semana += 5;
    }

    if ($data->dayOfWeek == 2) { // Terças
        echo "Terça {$data->format('d/m/Y')} - SEMANA: {$semana}\n";
    }
}
