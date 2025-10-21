# 🧪 Estratégia de Testes - EscalaMedica2

> **Objetivo**: Definir uma estratégia abrangente de testes para garantir qualidade, confiabilidade e segurança do sistema médico, estabelecendo padrões, ferramentas e processos de validação.

## 📊 Informações da Estratégia
- **Sistema**: EscalaMedica2
- **Cobertura Mínima**: 80% (código crítico: 95%)
- **Ferramentas**: PHPUnit, Laravel Dusk, Pest (futuro)
- **Última Atualização**: 2025-10-20

---

## 📋 Índice
- [🎯 Objetivos e Metas](#-objetivos-e-metas)
- [🏗️ Pirâmide de Testes](#-pirâmide-de-testes)
- [🧪 Tipos de Teste](#-tipos-de-teste)
- [🛠️ Ferramentas e Configuração](#-ferramentas-e-configuração)
- [📝 Padrões de Escrita](#-padrões-de-escrita)
- [🔍 Cenários Críticos](#-cenários-críticos)
- [📊 Métricas e Cobertura](#-métricas-e-cobertura)
- [🚀 CI/CD e Automação](#-cicd-e-automação)

---

## 🎯 Objetivos e Metas

### 🎯 Objetivos Principais
- **Qualidade**: Garantir funcionamento correto das funcionalidades
- **Segurança**: Validar proteção de dados médicos sensíveis
- **Confiabilidade**: Reduzir bugs em produção
- **Regressão**: Evitar quebras em funcionalidades existentes
- **Performance**: Manter tempos de resposta adequados
- **Conformidade**: Atender regulamentações médicas e LGPD

### 📈 Metas Quantitativas
- **Cobertura de Código**: 80% geral, 95% em código crítico
- **Tempo de Execução**: Testes unitários < 30s, funcionais < 5min
- **Taxa de Falsos Positivos**: < 5%
- **Bugs Escapados**: < 2 por release
- **Tempo de Feedback**: < 10min no CI/CD

### 🏥 Critérios Específicos para Sistema Médico
- **Zero Tolerância**: Bugs que afetam segurança do paciente
- **Auditabilidade**: Todos os testes documentados e rastreáveis
- **Dados Sensíveis**: Testes sem dados reais de pacientes
- **Conformidade LGPD**: Validar anonimização e consentimento
- **Disponibilidade**: Sistema 99.9% disponível

---

## 🏗️ Pirâmide de Testes

### 🔧 Testes Unitários (70%)
```
Escopo: Funções, métodos, classes isoladas
Velocidade: Muito rápida (< 1s por teste)
Quantidade: 200+ testes
Responsabilidade: Desenvolvedores
```

**Exemplos:**
- Validação de CPF
- Cálculo de horas de plantão
- Formatação de dados
- Regras de negócio isoladas

### 🧩 Testes de Integração (20%)
```
Escopo: Interação entre módulos
Velocidade: Rápida (1-5s por teste)
Quantidade: 50+ testes
Responsabilidade: Desenvolvedores + QA
```

**Exemplos:**
- Controller + Service + Model
- API + Banco de dados
- Integração com convênios
- Fluxos de autenticação

### 🌐 Testes E2E (10%)
```
Escopo: Fluxos completos do usuário
Velocidade: Lenta (30s-2min por teste)
Quantidade: 20+ testes
Responsabilidade: QA
```

**Exemplos:**
- Cadastro completo de paciente
- Criação de escala mensal
- Processo de internação
- Fluxo de faturamento

---

## 🧪 Tipos de Teste

### 🔧 Testes Unitários

#### 📋 O que Testar
```php
// Modelos e Business Logic
class UserTest extends TestCase
{
    public function test_medico_pode_ter_multiplas_especialidades()
    {
        $medico = User::factory()->medico()->create();
        $cardiologia = Especialidade::factory()->create(['nome' => 'Cardiologia']);
        $neurologia = Especialidade::factory()->create(['nome' => 'Neurologia']);
        
        $medico->especialidades()->attach([$cardiologia->id, $neurologia->id]);
        
        $this->assertCount(2, $medico->especialidades);
        $this->assertTrue($medico->hasEspecialidade('Cardiologia'));
        $this->assertTrue($medico->hasEspecialidade('Neurologia'));
    }
    
    public function test_plantao_nao_pode_sobrepor()
    {
        $medico = User::factory()->medico()->create();
        
        $plantao1 = Plantao::factory()->create([
            'medico_id' => $medico->id,
            'data' => '2025-11-01',
            'hora_inicio' => '08:00',
            'hora_fim' => '16:00'
        ]);
        
        $this->expectException(PlantaoSobrepostoException::class);
        
        Plantao::create([
            'medico_id' => $medico->id,
            'data' => '2025-11-01',
            'hora_inicio' => '14:00', // Sobrepõe com plantao1
            'hora_fim' => '22:00'
        ]);
    }
}
```

### 🧩 Testes de Integração

#### 📋 Controller + Service + Model
```php
class ConsultaControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_medico_pode_criar_consulta_em_sua_especialidade()
    {
        $medico = User::factory()->medico()->create();
        $cardiologia = Especialidade::factory()->create(['nome' => 'Cardiologia']);
        $medico->especialidades()->attach($cardiologia->id);
        
        $paciente = User::factory()->paciente()->create();
        
        $dadosConsulta = [
            'paciente_id' => $paciente->id,
            'especialidade_id' => $cardiologia->id,
            'data_agendamento' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'observacoes' => 'Consulta de rotina'
        ];
        
        $response = $this->actingAs($medico)
            ->post('/consultas', $dadosConsulta);
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('consultas', [
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'especialidade_id' => $cardiologia->id
        ]);
    }
    
    public function test_medico_nao_pode_criar_consulta_fora_de_sua_especialidade()
    {
        $medico = User::factory()->medico()->create();
        $cardiologia = Especialidade::factory()->create(['nome' => 'Cardiologia']);
        $neurologia = Especialidade::factory()->create(['nome' => 'Neurologia']);
        $medico->especialidades()->attach($cardiologia->id); // Apenas cardiologia
        
        $paciente = User::factory()->paciente()->create();
        
        $dadosConsulta = [
            'paciente_id' => $paciente->id,
            'especialidade_id' => $neurologia->id, // Tentando neurologia
            'data_agendamento' => now()->addDays(1)->format('Y-m-d H:i:s')
        ];
        
        $response = $this->actingAs($medico)
            ->post('/consultas', $dadosConsulta);
        
        $response->assertStatus(403);
        $this->assertDatabaseMissing('consultas', [
            'medico_id' => $medico->id,
            'especialidade_id' => $neurologia->id
        ]);
    }
}
```

### 🌐 Testes End-to-End (Browser)

#### 📋 Fluxo Completo de Usuário
```php
class CadastroInternacaoTest extends DuskTestCase
{
    public function test_fluxo_completo_de_internacao()
    {
        $this->browse(function (Browser $browser) {
            $medico = User::factory()->medico()->create([
                'email' => 'dr.silva@hospital.com',
                'password' => Hash::make('password123')
            ]);
            
            $paciente = User::factory()->paciente()->create([
                'nome' => 'João Santos',
                'cpf' => '12345678901'
            ]);
            
            $browser
                // Login
                ->visit('/login')
                ->type('email', 'dr.silva@hospital.com')
                ->type('password', 'password123')
                ->press('Entrar')
                ->assertPathIs('/dashboard')
                
                // Buscar paciente
                ->visit('/internacoes/create')
                ->type('busca_paciente', 'João Santos')
                ->press('Buscar')
                ->assertSee('João Santos')
                ->click('@selecionar-paciente')
                
                // Solicitar internação
                ->select('setor_id', '1') // UTI
                ->select('motivo', 'complicacao_cardiaca')
                ->type('observacoes', 'Paciente com arritmia cardíaca')
                ->press('Solicitar Internação')
                
                // Verificar confirmação
                ->assertSee('Internação solicitada com sucesso')
                ->assertSee('Aguardando disponibilidade de leito');
        });
        
        // Verificar banco de dados
        $this->assertDatabaseHas('internacoes', [
            'paciente_id' => $paciente->id,
            'medico_solicitante_id' => $medico->id,
            'status' => 'aguardando_leito'
        ]);
    }
}
```

### 🔒 Testes de Segurança

#### 📋 Autenticação e Autorização
```php
class SecurityTest extends TestCase
{
    public function test_acesso_negado_sem_autenticacao()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
    
    public function test_medico_nao_acessa_dados_de_outro_setor()
    {
        $medicoUTI = User::factory()->medico()->create(['setor_id' => 1]);
        $medicoEmergencia = User::factory()->medico()->create(['setor_id' => 2]);
        
        $pacienteUTI = User::factory()->paciente()->create(['setor_atual' => 1]);
        
        $response = $this->actingAs($medicoEmergencia)
            ->get("/pacientes/{$pacienteUTI->id}");
            
        $response->assertStatus(403);
    }
    
    public function test_dados_sensiveis_nao_aparecem_em_logs()
    {
        $paciente = User::factory()->paciente()->create([
            'cpf' => '12345678901',
            'nome' => 'João Santos'
        ]);
        
        Log::shouldReceive('info')
            ->once()
            ->with(Mockery::on(function ($message) {
                return !str_contains($message, '12345678901') &&
                       !str_contains($message, 'João Santos');
            }));
        
        $service = new PacienteService();
        $service->logarAcesso($paciente);
    }
}
```

### 📊 Testes de Performance

#### 📋 Tempo de Resposta
```php
class PerformanceTest extends TestCase
{
    public function test_dashboard_carrega_em_menos_de_2_segundos()
    {
        $medico = User::factory()->medico()->create();
        
        $startTime = microtime(true);
        
        $response = $this->actingAs($medico)->get('/dashboard');
        
        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;
        
        $response->assertStatus(200);
        $this->assertLessThan(2.0, $responseTime, 
            "Dashboard demorou {$responseTime}s para carregar");
    }
    
    public function test_busca_paciente_otimizada()
    {
        // Criar 1000 pacientes
        User::factory()->paciente()->count(1000)->create();
        
        $startTime = microtime(true);
        
        $response = $this->get('/api/pacientes/buscar?q=João');
        
        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;
        
        $response->assertStatus(200);
        $this->assertLessThan(1.0, $responseTime,
            "Busca de paciente demorou {$responseTime}s");
    }
}
```

---

## 🛠️ Ferramentas e Configuração

### 🔧 PHPUnit (Principal)

#### 📄 phpunit.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
    
    <source>
        <include>
            <directory>app</directory>
        </include>
        <exclude>
            <directory>app/Console</directory>
            <file>app/Http/Kernel.php</file>
        </exclude>
    </source>
    
    <coverage>
        <report>
            <html outputDirectory="storage/coverage"/>
            <text outputFile="storage/coverage.txt"/>
        </report>
    </coverage>
    
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
    </php>
</phpunit>
```

### 🌐 Laravel Dusk (E2E)

#### 📄 DuskTestCase.php
```php
<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    public static function prepare()
    {
        if (! static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-gpu',
            '--headless', // Para CI/CD
            '--no-sandbox',
            '--disable-dev-shm-usage',
        ])->filter()->toArray());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
```

### 🚀 Pest (Futuro - Sintaxe Moderna)

#### 📄 Exemplo de teste com Pest
```php
<?php

use App\Models\User;
use App\Models\Plantao;

it('can create a user', function () {
    $user = User::factory()->create();
    expect($user)->toBeInstanceOf(User::class);
});

it('validates plantao overlap', function () {
    $medico = User::factory()->medico()->create();
    
    Plantao::factory()->create([
        'medico_id' => $medico->id,
        'data' => '2025-11-01',
        'hora_inicio' => '08:00',
        'hora_fim' => '16:00'
    ]);
    
    expect(fn() => Plantao::create([
        'medico_id' => $medico->id,
        'data' => '2025-11-01',
        'hora_inicio' => '14:00',
        'hora_fim' => '22:00'
    ]))->toThrow(PlantaoSobrepostoException::class);
});

test('dashboard loads quickly', function () {
    $medico = User::factory()->medico()->create();
    
    $start = microtime(true);
    $response = $this->actingAs($medico)->get('/dashboard');
    $end = microtime(true);
    
    expect($response->status())->toBe(200);
    expect($end - $start)->toBeLessThan(2.0);
});
```

---

## 📝 Padrões de Escrita

### 📋 Convenções de Nomenclatura

#### ✅ Nomes de Teste (Português)
```php
// ✅ BOM - Descreve o comportamento esperado
public function test_medico_pode_criar_consulta_em_sua_especialidade()
public function test_paciente_nao_pode_ser_internado_sem_leito_disponivel()
public function test_escala_deve_ter_cobertura_completa_24_horas()

// ❌ RUIM - Muito genérico
public function test_create_consulta()
public function test_internacao()
public function test_escala()
```

#### ✅ Estrutura AAA (Arrange, Act, Assert)
```php
public function test_calculo_horas_plantao()
{
    // Arrange - Preparar dados
    $plantao = new Plantao([
        'hora_inicio' => '08:00',
        'hora_fim' => '16:00'
    ]);
    
    // Act - Executar ação
    $horas = $plantao->calcularHoras();
    
    // Assert - Verificar resultado
    $this->assertEquals(8, $horas);
}
```

#### ✅ Given-When-Then (BDD Style)
```php
public function test_medico_recebe_notificacao_quando_plantao_e_alterado()
{
    // Given - Estado inicial
    $medico = User::factory()->medico()->create(['email' => 'dr.silva@hospital.com']);
    $plantao = Plantao::factory()->create(['medico_id' => $medico->id]);
    
    // When - Ação executada
    $plantao->update(['data' => now()->addDays(1)]);
    
    // Then - Resultado esperado
    Notification::assertSentTo($medico, PlantaoAlteradoNotification::class);
}
```

### 🏭 Factories Bem Estruturadas

#### 📄 UserFactory.php
```php
class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'tipo_usuario' => 'paciente',
            'status' => 'ativo',
        ];
    }
    
    public function medico()
    {
        return $this->state([
            'tipo_usuario' => 'medico',
            'crm' => fake()->numerify('CRM/SP ####'),
        ]);
    }
    
    public function enfermeiro()
    {
        return $this->state([
            'tipo_usuario' => 'enfermeiro',
            'coren' => fake()->numerify('COREN-SP ####'),
        ]);
    }
    
    public function inativo()
    {
        return $this->state(['status' => 'inativo']);
    }
}
```

### 🧪 Testes de Dados

#### ✅ Dados de Teste Realistas mas Seguros
```php
class PacienteFactory extends Factory
{
    public function definition()
    {
        return [
            'nome' => fake()->name(),
            'cpf' => $this->gerarCpfValido(), // CPF válido mas fictício
            'data_nascimento' => fake()->dateTimeBetween('-80 years', '-18 years'),
            'telefone' => fake()->phoneNumber(),
            'endereco' => fake()->address(),
            // NUNCA usar dados reais de pacientes
        ];
    }
    
    private function gerarCpfValido()
    {
        // Gerar CPF válido mas fictício para testes
        $cpf = fake()->numerify('###########');
        return $this->aplicarAlgoritmoCpf($cpf);
    }
}
```

---

## 🔍 Cenários Críticos

### 🚨 Testes Obrigatórios (Zero Tolerância a Falhas)

#### 🏥 Segurança do Paciente
```php
class SegurancaPacienteTest extends TestCase
{
    /** @test */
    public function medicamento_alergico_bloqueia_prescricao()
    {
        $paciente = User::factory()->paciente()->create();
        $paciente->alergias()->create(['medicamento' => 'Penicilina']);
        
        $this->expectException(MedicamentoAlergicoException::class);
        
        $prescricaoService = new PrescricaoService();
        $prescricaoService->prescrever($paciente, 'Penicilina', '500mg');
    }
    
    /** @test */
    public function dose_maxima_medicamento_nao_pode_ser_excedida()
    {
        $paciente = User::factory()->paciente()->create(['peso' => 70]);
        
        $this->expectException(DoseExcedidaException::class);
        
        $prescricaoService = new PrescricaoService();
        $prescricaoService->prescrever($paciente, 'Paracetamol', '2000mg'); // Dose perigosa
    }
}
```

#### 🔐 Segurança de Dados
```php
class SegurancaDadosTest extends TestCase
{
    /** @test */
    public function dados_sensiveis_sao_criptografados()
    {
        $paciente = User::factory()->paciente()->create([
            'cpf' => '12345678901'
        ]);
        
        $pacienteDb = DB::table('users')->find($paciente->id);
        
        // CPF não deve estar em texto plano no banco
        $this->assertNotEquals('12345678901', $pacienteDb->cpf);
        $this->assertTrue(Hash::check('12345678901', $pacienteDb->cpf));
    }
    
    /** @test */
    public function logs_nao_contem_dados_sensiveis()
    {
        Log::spy();
        
        $paciente = User::factory()->paciente()->create([
            'cpf' => '12345678901',
            'nome' => 'João Santos'
        ]);
        
        $service = new PacienteService();
        $service->buscar($paciente->id);
        
        Log::shouldNotHaveReceived('info', [
            Mockery::on(fn($msg) => str_contains($msg, '12345678901'))
        ]);
    }
}
```

#### ⚖️ Conformidade LGPD
```php
class LgpdComplianceTest extends TestCase
{
    /** @test */
    public function dados_podem_ser_anonimizados()
    {
        $paciente = User::factory()->paciente()->create([
            'nome' => 'João Santos',
            'cpf' => '12345678901',
            'email' => 'joao@email.com'
        ]);
        
        $lgpdService = new LgpdService();
        $lgpdService->anonimizarPaciente($paciente->id);
        
        $paciente->refresh();
        
        $this->assertStringStartsWith('ANONIMO_', $paciente->nome);
        $this->assertNull($paciente->cpf);
        $this->assertNull($paciente->email);
        $this->assertEquals('anonimizado', $paciente->status);
    }
    
    /** @test */
    public function consentimento_e_obrigatorio_para_dados_sensiveis()
    {
        $paciente = User::factory()->paciente()->create();
        
        $this->expectException(ConsentimentoNecessarioException::class);
        
        $relatorioService = new RelatorioService();
        $relatorioService->gerarRelatorioDetalhado($paciente->id); // Sem consentimento
    }
}
```

---

## 📊 Métricas e Cobertura

### 📈 Configuração de Cobertura

#### 📄 Comando para Gerar Cobertura
```bash
# Cobertura completa
php artisan test --coverage --min=80

# Cobertura por tipo
php artisan test tests/Unit --coverage
php artisan test tests/Feature --coverage

# Cobertura HTML
php artisan test --coverage-html storage/coverage

# Cobertura em texto
php artisan test --coverage-text
```

#### 📊 Relatório de Cobertura
```
Code Coverage Report:
  Summary:
    Classes:  88.89% (8/9)
    Methods:  83.33% (20/24)
    Lines:    85.71% (120/140)

  App\Models\User
    Methods:  91.67% (11/12)
    Lines:    89.47% (34/38)

  App\Services\PacienteService
    Methods:  75.00% (6/8)
    Lines:    81.25% (39/48)

  App\Http\Controllers\ConsultaController
    Methods:  80.00% (4/5)
    Lines:    82.35% (28/34)
```

### 🎯 Metas por Módulo

#### 📋 Classificação de Criticidade
```
🔴 Crítico (95%+ cobertura):
- Autenticação e autorização
- Cálculos de medicamentos
- Validações de segurança
- Processamento de dados médicos

🟡 Importante (85%+ cobertura):
- Controllers principais
- Services de negócio
- Models com relacionamentos
- APIs de integração

🟢 Normal (70%+ cobertura):
- Helpers e utilities
- Formatação de dados
- Notificações
- Relatórios
```

### 📊 Dashboard de Qualidade

#### 📄 Script para Métricas
```bash
#!/bin/bash
# quality-check.sh

echo "🧪 Executando testes..."
php artisan test --stop-on-failure

echo "📊 Verificando cobertura..."
php artisan test --coverage --min=80

echo "🔍 Análise estática..."
./vendor/bin/phpstan analyse

echo "🎨 Verificando estilo..."
./vendor/bin/pint --test

echo "🔒 Verificando segurança..."
composer audit

echo "✅ Verificação de qualidade concluída!"
```

---

## 🚀 CI/CD e Automação

### 🔄 Pipeline de Testes

#### 📄 .github/workflows/tests.yml
```yaml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  tests:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        php-version: [8.2, 8.3]
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ matrix.php-version }}
        extensions: mbstring, xml, ctype, iconv, intl, pdo_sqlite, zip
        coverage: xdebug
    
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress
    
    - name: Create SQLite database
      run: touch database/database.sqlite
    
    - name: Run migrations
      run: php artisan migrate --env=testing
    
    - name: Run unit tests
      run: php artisan test tests/Unit --coverage-clover coverage.xml
    
    - name: Run feature tests
      run: php artisan test tests/Feature
    
    - name: Upload coverage to Codecov
      uses: codecov/codecov-action@v3
      with:
        file: ./coverage.xml
```

### 🌐 Testes de Browser (Dusk)

#### 📄 .github/workflows/dusk.yml
```yaml
name: Browser Tests

on:
  schedule:
    - cron: '0 2 * * *' # Todo dia às 2h
  workflow_dispatch:

jobs:
  dusk:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.2
        extensions: mbstring, xml, ctype, iconv, intl, pdo_sqlite, zip
    
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress
    
    - name: Setup Chrome
      uses: browser-actions/setup-chrome@latest
    
    - name: Start Chrome Driver
      run: ./vendor/laravel/dusk/bin/chromedriver-linux &
    
    - name: Run Laravel Server
      run: php artisan serve &
      env:
        APP_URL: http://127.0.0.1:8000
    
    - name: Run Dusk Tests
      run: php artisan dusk
      env:
        APP_URL: http://127.0.0.1:8000
    
    - name: Upload Screenshots
      uses: actions/upload-artifact@v3
      if: failure()
      with:
        name: screenshots
        path: tests/Browser/screenshots
```

### 📊 Relatórios Automáticos

#### 📄 Script de Relatório Diário
```bash
#!/bin/bash
# daily-report.sh

DATE=$(date +%Y-%m-%d)
REPORT_FILE="storage/reports/quality-report-$DATE.md"

echo "# 📊 Relatório de Qualidade - $DATE" > $REPORT_FILE
echo "" >> $REPORT_FILE

echo "## 🧪 Testes" >> $REPORT_FILE
php artisan test --coverage-text >> $REPORT_FILE

echo "" >> $REPORT_FILE
echo "## 🔍 Análise Estática" >> $REPORT_FILE
./vendor/bin/phpstan analyse --error-format=table >> $REPORT_FILE

echo "" >> $REPORT_FILE
echo "## 🔒 Auditoria de Segurança" >> $REPORT_FILE
composer audit >> $REPORT_FILE

# Enviar por email (configurar SMTP)
mail -s "Relatório de Qualidade - $DATE" dev-team@hospital.com < $REPORT_FILE
```

---

## 📞 Ferramentas Adicionais

### 🔍 Análise Estática
```bash
# PHPStan
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse

# Psalm
composer require --dev vimeo/psalm
./vendor/bin/psalm --init
./vendor/bin/psalm
```

### 🎨 Code Style
```bash
# Laravel Pint
./vendor/bin/pint

# PHP CS Fixer
composer require --dev friendsofphp/php-cs-fixer
./vendor/bin/php-cs-fixer fix
```

### 🔒 Segurança
```bash
# Composer Audit
composer audit

# Security Checker
composer require --dev enlightn/security-checker
./vendor/bin/security-checker security:check
```

### 📊 Performance
```bash
# Laravel Debugbar
composer require --dev barryvdh/laravel-debugbar

# Clockwork
composer require --dev itsgoingd/clockwork
```

---

**📍 Última atualização**: 2025-10-20  
**👥 Responsáveis**: Tech Lead + QA Team  
**📋 Status**: Estratégia definida - implementar gradualmente  
**🔄 Próxima revisão**: A cada release ou mudança significativa

> **⚠️ IMPORTANTE**: Todos os testes devem passar antes de qualquer deploy para produção!