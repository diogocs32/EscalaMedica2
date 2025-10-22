<?php

require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== REPUBLICAÇÃO COMPLETA DE DEZEMBRO 2025 ===\n\n";

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
echo "Total de slots esperados no template: 187\n";
echo "Slots com médicos no template: 167\n";
echo "Buracos no template: 20\n\n";

// Executar método publicar completo (simular controller)
$request = new Illuminate\Http\Request();
$request->merge(['periodo' => '2025-12']);

$controller = new App\Http\Controllers\EscalaPadraoController();

try {
    // Publicar usando o método do controller
    $controller->publicar($request, $unidade);

    echo "✅ Publicação executada com sucesso!\n\n";

    // Verificar resultado
    $publicacao = App\Models\EscalaPublicada::where('unidade_id', 1)
        ->where('ano', 2025)
        ->where('mes', 12)
        ->orderBy('id', 'desc')
        ->first();

    if ($publicacao) {
        $totalSlots = $publicacao->alocacoes()->count();
        $preenchidos = $publicacao->alocacoes()->where('status', 'preenchido')->count();
        $buracos = $publicacao->alocacoes()->where('status', 'vago')->count();

        echo "=== RESULTADO NA ESCALA PUBLICADA ===\n";
        echo "Publicação ID: {$publicacao->id}\n";
        echo "Total de slots: $totalSlots\n";
        echo "Preenchidos: $preenchidos\n";
        echo "Buracos: $buracos\n";
        echo "Taxa: " . ($totalSlots > 0 ? round(($preenchidos / $totalSlots) * 100, 2) : 0) . "%\n\n";

        // Verificar proporção esperada
        $diasNoMes = cal_days_in_month(CAL_GREGORIAN, 12, 2025); // 31 dias
        $slotsEsperadosPorDia = 187 / 5; // slots por semana / 5 semanas
        $totalEsperado = round($slotsEsperadosPorDia * ($diasNoMes / 7)); // aproximado

        echo "Dias em dezembro: $diasNoMes\n";
        echo "Slots esperados (aproximado): " . round($totalEsperado * 5) . "\n";

        if ($buracos == 0) {
            echo "\n⚠️ PROBLEMA: Buracos = 0, mas deveria ser proporcional aos 20 buracos do template!\n";
        } else {
            echo "\n✅ Buracos detectados corretamente!\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
