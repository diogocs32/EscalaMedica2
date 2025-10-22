<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFICAÇÃO DAS ESCALAS PUBLICADAS ===\n\n";

$ep = \App\Models\EscalaPublicada::where('mes', 12)->where('ano', 2025)->first();

if (!$ep) {
    echo "Nenhuma escala publicada encontrada para dezembro/2025\n";
    exit;
}

echo "Escala Publicada ID: {$ep->id}\n";
echo "Criada em: {$ep->created_at}\n";
echo "Atualizada em: {$ep->updated_at}\n\n";

echo "Total alocações: " . $ep->alocacoes()->count() . "\n";
echo "Alocações dia 02/12: " . $ep->alocacoes()->where('data', '2025-12-02')->count() . "\n";
echo "Alocações dia 09/12: " . $ep->alocacoes()->where('data', '2025-12-09')->count() . "\n\n";

echo "=== SOLUÇÃO ===\n";
echo "A escala foi publicada com a lógica ANTIGA de cálculo de semanas.\n";
echo "Você precisa RE-PUBLICAR a escala para aplicar a correção!\n\n";
echo "Comandos sugeridos:\n";
echo "1. Deletar a escala atual de dezembro:\n";
echo "   DELETE FROM escalas_publicadas WHERE mes=12 AND ano=2025;\n\n";
echo "2. Ou use a interface web para deletar/republica a escala\n";
