<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "===== ANÁLISE PROFUNDA DO SISTEMA =====\n\n";

// 1. Listar todas as tabelas
echo "1. TABELAS NO BANCO DE DADOS:\n";
$tables = DB::select("SHOW TABLES");
$dbName = DB::getDatabaseName();
foreach ($tables as $t) {
    $tableName = $t->{"Tables_in_{$dbName}"};
    echo "  - {$tableName}\n";
}

// 2. Verificar estrutura da tabela configuracoes_turno_setor
echo "\n2. ESTRUTURA: configuracoes_turno_setor\n";
$columns = DB::select("DESCRIBE configuracoes_turno_setor");
foreach ($columns as $col) {
    echo "  - {$col->Field} ({$col->Type})\n";
}

// 3. Verificar se há alguma relação com plantonistas
echo "\n3. BUSCANDO RELAÇÕES COM PLANTONISTAS...\n";
$configs = DB::table('configuracoes_turno_setor')
    ->where('quantidade_necessaria', 3)
    ->get();

echo "  Configurações com quantidade=3: " . $configs->count() . "\n";
foreach ($configs as $cfg) {
    echo "    ID: {$cfg->id} | Dia Template: {$cfg->dia_template_id} | Turno: {$cfg->turno_id} | Setor: {$cfg->setor_id}\n";
}

// 4. Verificar tabelas que possam ter alocações
$possiveisTabelasAlocacao = ['alocacoes', 'alocacoes_template', 'alocacoes_padrao', 'slots', 'slots_template'];
echo "\n4. VERIFICANDO TABELAS DE ALOCAÇÃO...\n";
foreach ($possiveisTabelasAlocacao as $tabela) {
    try {
        $count = DB::table($tabela)->count();
        echo "  ✅ {$tabela} EXISTE!\n";
        echo "     Registros: {$count}\n";

        // Mostrar estrutura
        $cols = DB::select("DESCRIBE {$tabela}");
        echo "     Colunas: ";
        echo implode(', ', array_map(fn($c) => $c->Field, $cols)) . "\n";
    } catch (\Exception $e) {
        echo "  ❌ {$tabela} não existe\n";
    }
}

// 5. Verificar a view da planilha
echo "\n5. ANALISANDO CONTROLLER DA PLANILHA...\n";
echo "  Arquivo: app/Http/Controllers/EscalaPadraoController.php\n";
echo "  Método: planilha()\n";

// 6. Verificar rotas relacionadas
echo "\n6. ROTAS DE ESCALA PADRÃO:\n";
$routes = app('router')->getRoutes();
foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'escala') !== false || strpos($uri, 'schedule') !== false) {
        echo "  - {$route->methods()[0]} /{$uri}\n";
    }
}

// 7. Buscar models relacionados
echo "\n7. MODELS RELACIONADOS:\n";
$modelsDir = __DIR__ . '/app/Models';
$files = scandir($modelsDir);
foreach ($files as $file) {
    if (strpos($file, '.php') !== false && strpos(strtolower($file), 'escala') !== false) {
        echo "  - {$file}\n";
    }
}

echo "\n===== FIM DA ANÁLISE =====\n";
