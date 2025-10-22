<?php

require 'c:/xampp/htdocs/EscalaMedica2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/EscalaMedica2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ANÁLISE: DISCREPÂNCIA NA CONTAGEM DE BURACOS ===\n\n";

$escalaPadrao = App\Models\EscalaPadrao::where('unidade_id', 1)->first();
echo "Escala Padrão ID: {$escalaPadrao->id}\n";
echo "Unidade: {$escalaPadrao->unidade->nome}\n\n";

// Contar na tabela alocacoes_template
echo "=== CONTAGEM NA TABELA alocacoes_template ===\n";
$totalTemplate = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)->count();
$preenchidosTemplate = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->whereNotNull('plantonista_id')
    ->count();
$vaziosTemplate = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
    ->whereNull('plantonista_id')
    ->count();

echo "Total de alocações template: $totalTemplate\n";
echo "Preenchidos (plantonista_id NOT NULL): $preenchidosTemplate\n";
echo "Vazios (plantonista_id NULL): $vaziosTemplate\n\n";

// Contar baseado em ConfiguracaoTurnoSetor
echo "=== CONTAGEM BASEADA EM ConfiguracaoTurnoSetor ===\n";
$totalConfigs = App\Models\ConfiguracaoTurnoSetor::whereHas('diaTemplate.semanaTemplate', function ($q) use ($escalaPadrao) {
    $q->where('escala_padrao_id', $escalaPadrao->id);
})->get();

$totalEsperado = $totalConfigs->sum('quantidade_necessaria');
echo "Total de slots esperados (soma configs): $totalEsperado\n";
echo "Slots criados em alocacoes_template: $totalTemplate\n";
echo "Diferença (slots não criados): " . ($totalEsperado - $totalTemplate) . "\n\n";

// Cálculo correto de buracos
$buracosReais = ($totalEsperado - $preenchidosTemplate);
echo "=== CÁLCULO CORRETO DE BURACOS ===\n";
echo "Total esperado: $totalEsperado\n";
echo "Preenchidos: $preenchidosTemplate\n";
echo "Buracos CORRETOS: $buracosReais\n";
echo "  = Slots não criados: " . ($totalEsperado - $totalTemplate) . "\n";
echo "  + Slots criados mas vazios: $vaziosTemplate\n";
echo "  = Total buracos: $buracosReais\n\n";

// Verificar o que a view planilha.blade.php está calculando
echo "=== ANÁLISE DA LÓGICA DA VIEW ===\n";
echo "Se a view está calculando como:\n";
echo "  Buracos = totalTemplate - preenchidosTemplate\n";
echo "  Buracos = $totalTemplate - $preenchidosTemplate = " . ($totalTemplate - $preenchidosTemplate) . "\n";
echo "  ❌ ERRADO! Isso ignora os slots que nem foram criados.\n\n";

echo "Cálculo correto deveria ser:\n";
echo "  Buracos = totalEsperado - preenchidosTemplate\n";
echo "  Buracos = $totalEsperado - $preenchidosTemplate = $buracosReais\n";
echo "  ✅ CORRETO!\n\n";

// Análise detalhada por semana
echo "=== DETALHAMENTO POR SEMANA ===\n";
for ($semana = 1; $semana <= 5; $semana++) {
    $semanaTemplate = App\Models\SemanaTemplate::where('escala_padrao_id', $escalaPadrao->id)
        ->where('numero_semana', $semana)
        ->first();

    if (!$semanaTemplate) continue;

    $dias = App\Models\DiaTemplate::where('semana_template_id', $semanaTemplate->id)->get();

    $totalEsperadoSemana = 0;
    $totalAlocadosSemana = 0;
    $preenchidosSemana = 0;

    foreach ($dias as $dia) {
        $configs = App\Models\ConfiguracaoTurnoSetor::where('dia_template_id', $dia->id)->get();
        $esperado = $configs->sum('quantidade_necessaria');

        $mapaDias = [
            'domingo' => 1,
            'segunda' => 2,
            'terca' => 3,
            'quarta' => 4,
            'quinta' => 5,
            'sexta' => 6,
            'sabado' => 7
        ];
        $numeroDia = $mapaDias[$dia->dia_semana] ?? 0;

        $alocacoes = App\Models\AlocacaoTemplate::where('escala_padrao_id', $escalaPadrao->id)
            ->where('semana', $semana)
            ->where('dia', $numeroDia)
            ->get();

        $totalEsperadoSemana += $esperado;
        $totalAlocadosSemana += $alocacoes->count();
        $preenchidosSemana += $alocacoes->whereNotNull('plantonista_id')->count();
    }

    $buracosSemana = $totalEsperadoSemana - $preenchidosSemana;

    echo "Semana $semana:\n";
    echo "  Esperado: $totalEsperadoSemana\n";
    echo "  Alocados: $totalAlocadosSemana\n";
    echo "  Preenchidos: $preenchidosSemana\n";
    echo "  Buracos: $buracosSemana\n\n";
}
