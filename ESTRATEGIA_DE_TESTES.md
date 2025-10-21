# 🧪 ESTRATÉGIA DE TESTES - EscalaMedica2

> **Referenciado por**: `REGISTRY.md` → "Qualidade e Testes"

---

## 🎯 FILOSOFIA DE TESTES

### **Pirâmide de Testes**
```
    /\     E2E Tests (10%)
   /  \    Integration Tests (20%)
  /____\   Unit Tests (70%)
```

### **Princípios Fundamentais**
- **Fast**: Testes devem executar rapidamente
- **Independent**: Cada teste é independente
- **Repeatable**: Resultados consistentes
- **Self-Validating**: Pass/Fail claro
- **Timely**: Escritos junto com o código

---

## 🔬 TESTES UNITÁRIOS

### **Models - Plantonista**
```php
// tests/Unit/PlantonistaTest.php
class PlantonistaTest extends TestCase
{
    /** @test */
    public function deve_criar_plantonista_com_dados_validos()
    {
        $plantonista = Plantonista::create([
            'nome' => 'Dr. João Silva',
            'crm' => '12345-SP',
            'especializacao' => 'Cardiologia'
        ]);
        
        $this->assertDatabaseHas('plantonistas', [
            'nome' => 'Dr. João Silva',
            'crm' => '12345-SP'
        ]);
    }
    
    /** @test */
    public function nao_deve_aceitar_crm_duplicado()
    {
        Plantonista::create([
            'nome' => 'Dr. João',
            'crm' => '12345-SP',
            'especializacao' => 'Cardiologia'
        ]);
        
        $this->expectException(QueryException::class);
        
        Plantonista::create([
            'nome' => 'Dr. Pedro',
            'crm' => '12345-SP',
            'especializacao' => 'Pediatria'
        ]);
    }
}
```

### **Validation Rules - ValidacaoAlocacao**
```php
// tests/Unit/ValidacaoAlocacaoTest.php
class ValidacaoAlocacaoTest extends TestCase
{
    /** @test */
    public function deve_rejeitar_conflito_de_horarios()
    {
        $plantonista = Plantonista::factory()->create();
        $unidade = Unidade::factory()->create();
        $setor = Setor::factory()->create(['unidade_id' => $unidade->id]);
        $turno = Turno::factory()->create();
        
        // Primeira alocação
        Alocacao::create([
            'plantonista_id' => $plantonista->id,
            'unidade_id' => $unidade->id,
            'setor_id' => $setor->id,
            'turno_id' => $turno->id,
            'data' => '2024-12-30'
        ]);
        
        // Tentar criar conflito
        $validator = new ValidacaoAlocacao();
        $result = $validator->passes('', [
            'plantonista_id' => $plantonista->id,
            'data' => '2024-12-30',
            'turno_id' => $turno->id
        ]);
        
        $this->assertFalse($result);
    }
}
```

### **Observers - AlocacaoObserver**
```php
// tests/Unit/AlocacaoObserverTest.php
class AlocacaoObserverTest extends TestCase
{
    /** @test */
    public function deve_logar_criacao_de_alocacao()
    {
        $alocacao = Alocacao::factory()->create();
        
        $this->assertDatabaseHas('logs', [
            'action' => 'created',
            'model' => 'Alocacao',
            'model_id' => $alocacao->id
        ]);
    }
}
```

---

## 🔗 TESTES DE INTEGRAÇÃO

### **Controllers - PlantonistaController**
```php
// tests/Feature/PlantonistaControllerTest.php
class PlantonistaControllerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function pode_listar_plantonistas()
    {
        Plantonista::factory()->count(3)->create();
        
        $response = $this->get('/plantonistas');
        
        $response->assertStatus(200);
        $response->assertViewIs('plantonistas.index');
        $response->assertViewHas('plantonistas');
    }
    
    /** @test */
    public function pode_criar_plantonista()
    {
        $dados = [
            'nome' => 'Dr. Test',
            'crm' => '54321-RJ',
            'especializacao' => 'Neurologia'
        ];
        
        $response = $this->post('/plantonistas', $dados);
        
        $response->assertRedirect('/plantonistas');
        $this->assertDatabaseHas('plantonistas', $dados);
    }
    
    /** @test */
    public function nao_pode_criar_sem_dados_obrigatorios()
    {
        $response = $this->post('/plantonistas', []);
        
        $response->assertSessionHasErrors(['nome', 'crm']);
    }
}
```

### **Dashboard - DashboardController**
```php
// tests/Feature/DashboardTest.php
class DashboardTest extends TestCase
{
    /** @test */
    public function dashboard_exibe_estatisticas_corretas()
    {
        // Criar dados de teste
        Plantonista::factory()->count(5)->create();
        Unidade::factory()->count(3)->create();
        Setor::factory()->count(7)->create();
        
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertViewHas([
            'totalPlantonistas',
            'totalUnidades', 
            'totalSetores',
            'alocacoesMes'
        ]);
    }
}
```

### **API Routes - Validação de Rotas**
```php
// tests/Feature/RoutesTest.php
class RoutesTest extends TestCase
{
    /** @test */
    public function todas_rotas_principais_funcionam()
    {
        $routes = [
            '/' => 200,
            '/dashboard' => 200,
            '/plantonistas' => 200,
            '/unidades' => 200,
            '/setores' => 200,
            '/turnos' => 200,
            '/alocacoes' => 200
        ];
        
        foreach ($routes as $route => $expectedStatus) {
            $response = $this->get($route);
            $response->assertStatus($expectedStatus);
        }
    }
}
```

---

## 🌐 TESTES END-TO-END

### **User Journey - Criar Alocação Completa**
```php
// tests/Browser/CriarAlocacaoTest.php
class CriarAlocacaoTest extends DuskTestCase
{
    /** @test */
    public function usuario_pode_criar_alocacao_completa()
    {
        $plantonista = Plantonista::factory()->create();
        $unidade = Unidade::factory()->create();
        $setor = Setor::factory()->create(['unidade_id' => $unidade->id]);
        $turno = Turno::factory()->create();
        
        $this->browse(function (Browser $browser) use ($plantonista, $unidade, $setor, $turno) {
            $browser->visit('/alocacoes/create')
                    ->select('plantonista_id', $plantonista->id)
                    ->select('unidade_id', $unidade->id)
                    ->waitFor('#setor_id option[value="' . $setor->id . '"]')
                    ->select('setor_id', $setor->id)
                    ->select('turno_id', $turno->id)
                    ->type('data', '2024-12-30')
                    ->press('Salvar')
                    ->assertPathIs('/alocacoes')
                    ->assertSee('Alocação criada com sucesso');
        });
    }
}
```

### **Navigation Flow - Dashboard to CRUD**
```php
// tests/Browser/NavigationTest.php
class NavigationTest extends DuskTestCase
{
    /** @test */
    public function pode_navegar_do_dashboard_para_crud()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/dashboard')
                    ->clickLink('Plantonistas')
                    ->assertPathIs('/plantonistas')
                    ->clickLink('Novo Plantonista')
                    ->assertPathIs('/plantonistas/create')
                    ->type('nome', 'Dr. Teste E2E')
                    ->type('crm', '99999-SP')
                    ->type('especializacao', 'Teste')
                    ->press('Salvar')
                    ->assertPathIs('/plantonistas')
                    ->assertSee('Dr. Teste E2E');
        });
    }
}
```

---

## 🏭 FACTORIES E SEEDERS

### **Factory - Plantonista**
```php
// database/factories/PlantonistaFactory.php
class PlantonistaFactory extends Factory
{
    public function definition()
    {
        return [
            'nome' => 'Dr. ' . $this->faker->name(),
            'crm' => $this->faker->numerify('#####') . '-' . $this->faker->stateAbbr(),
            'especializacao' => $this->faker->randomElement([
                'Cardiologia', 'Pediatria', 'Neurologia', 
                'Ortopedia', 'Ginecologia'
            ])
        ];
    }
}
```

### **Factory - Alocacao**
```php
// database/factories/AlocacaoFactory.php
class AlocacaoFactory extends Factory
{
    public function definition()
    {
        return [
            'plantonista_id' => Plantonista::factory(),
            'unidade_id' => Unidade::factory(),
            'setor_id' => function (array $attributes) {
                return Setor::factory()->create([
                    'unidade_id' => $attributes['unidade_id']
                ])->id;
            },
            'turno_id' => Turno::factory(),
            'data' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d')
        ];
    }
}
```

### **Test Seeder**
```php
// database/seeders/TestSeeder.php
class TestSeeder extends Seeder
{
    public function run()
    {
        // Dados básicos para todos os testes
        $plantonistas = Plantonista::factory()->count(10)->create();
        $unidades = Unidade::factory()->count(3)->create();
        
        $unidades->each(function ($unidade) {
            Setor::factory()->count(3)->create(['unidade_id' => $unidade->id]);
        });
        
        Turno::factory()->count(6)->create();
        
        // Alocações de exemplo
        Alocacao::factory()->count(20)->create();
    }
}
```

---

## 📊 COBERTURA DE TESTES

### **Métricas Alvo**
- **Line Coverage**: >= 80%
- **Branch Coverage**: >= 75%
- **Method Coverage**: >= 85%

### **Comando de Cobertura**
```bash
php artisan test --coverage-html coverage-report
```

### **Exclusões de Cobertura**
```php
// phpunit.xml
<filter>
    <whitelist>
        <directory suffix=".php">./app</directory>
        <exclude>
            <directory>./app/Console</directory>
            <directory>./app/Exceptions</directory>
            <file>./app/Http/Kernel.php</file>
        </exclude>
    </whitelist>
</filter>
```

---

## 🔄 TESTES DE REGRESSÃO

### **Smoke Tests - Funcionalidades Críticas**
```php
// tests/Feature/SmokeTest.php
class SmokeTest extends TestCase
{
    /** @test */
    public function funcionalidades_criticas_funcionam()
    {
        // Dashboard carrega
        $this->get('/dashboard')->assertStatus(200);
        
        // CRUD básico funciona
        $plantonista = Plantonista::factory()->create();
        $this->get("/plantonistas/{$plantonista->id}")->assertStatus(200);
        
        // Validações críticas funcionam
        $this->post('/alocacoes', [])->assertSessionHasErrors();
    }
}
```

### **Database Integrity Tests**
```php
// tests/Feature/DatabaseIntegrityTest.php
class DatabaseIntegrityTest extends TestCase
{
    /** @test */
    public function foreign_keys_funcionam_corretamente()
    {
        $this->expectException(QueryException::class);
        
        // Tentar criar alocação com plantonista inexistente
        Alocacao::create([
            'plantonista_id' => 99999,
            'unidade_id' => Unidade::factory()->create()->id,
            'setor_id' => Setor::factory()->create()->id,
            'turno_id' => Turno::factory()->create()->id,
            'data' => '2024-12-30'
        ]);
    }
}
```

---

## ⚡ TESTES DE PERFORMANCE

### **Load Testing - Dashboard**
```php
// tests/Performance/DashboardPerformanceTest.php
class DashboardPerformanceTest extends TestCase
{
    /** @test */
    public function dashboard_carrega_rapidamente()
    {
        // Criar muitos dados
        Plantonista::factory()->count(1000)->create();
        Alocacao::factory()->count(5000)->create();
        
        $start = microtime(true);
        $this->get('/dashboard');
        $end = microtime(true);
        
        $executionTime = ($end - $start) * 1000; // em ms
        $this->assertLessThan(2000, $executionTime); // < 2 segundos
    }
}
```

### **Memory Usage Tests**
```php
/** @test */
public function operacoes_nao_causam_memory_leak()
{
    $initialMemory = memory_get_usage();
    
    for ($i = 0; $i < 100; $i++) {
        Plantonista::factory()->create();
    }
    
    $finalMemory = memory_get_usage();
    $memoryIncrease = $finalMemory - $initialMemory;
    
    $this->assertLessThan(10 * 1024 * 1024, $memoryIncrease); // < 10MB
}
```

---

## 🛠️ FERRAMENTAS E CONFIGURAÇÃO

### **PHPUnit Configuration**
```xml
<!-- phpunit.xml -->
<phpunit bootstrap="vendor/autoload.php">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
    </php>
</phpunit>
```

### **Scripts de Teste**
```json
// composer.json
{
  "scripts": {
    "test": "php artisan test",
    "test-unit": "php artisan test --testsuite=Unit",
    "test-feature": "php artisan test --testsuite=Feature",
    "test-coverage": "php artisan test --coverage-html coverage-report",
    "test-parallel": "php artisan test --parallel"
  }
}
```

### **Continuous Integration**
```yaml
# .github/workflows/tests.yml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v2
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.2
    - name: Install dependencies
      run: composer install
    - name: Run tests
      run: php artisan test --coverage-clover coverage.xml
```

---

## 📈 MÉTRICAS E RELATÓRIOS

### **Test Metrics Dashboard**
- Total de testes executados
- Tempo de execução por suite
- Cobertura de código por módulo
- Testes que falharam historicamente
- Performance trends

### **Quality Gates**
- Todos os testes devem passar
- Cobertura >= 80%
- Tempo de execução < 5 minutos
- Sem warnings ou notices
- Memory usage estável

---

*Estratégia de testes completa do EscalaMedica2*
*Última atualização: 2024-12-28*
*Total de categorias de teste: 8*