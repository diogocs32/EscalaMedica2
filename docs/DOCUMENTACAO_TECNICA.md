# 🏗️ Documentação Técnica do Sistema

> **Objetivo**: Fornecer uma visão completa da arquitetura, padrões e relacionamentos do sistema para desenvolvedores, QA e stakeholders.

## 📊 Informações do Sistema
- **Nome do Projeto**: EscalaMedica2
- **Versão Laravel**: 11.46.1
- **Versão PHP**: 8.2.12
- **Tipo de Sistema**: Aplicação Web Laravel
- **Ambiente**: XAMPP (Windows)
- **Última Atualização**: 2025-10-20

---

## 📋 Índice
- [🎯 Visão Geral](#-visão-geral)
- [🏛️ Arquitetura Geral](#-arquitetura-geral)
- [📊 Estrutura de Pastas](#-estrutura-de-pastas)
- [🗄️ Banco de Dados](#-banco-de-dados)
- [🛣️ Sistema de Rotas](#-sistema-de-rotas)
- [🧩 Componentes e Camadas](#-componentes-e-camadas)
- [⚙️ Configurações](#-configurações)
- [🔧 Padrões de Desenvolvimento](#-padrões-de-desenvolvimento)
- [🔗 Integrações e APIs](#-integrações-e-apis)
- [🛡️ Segurança](#-segurança)
- [📈 Performance](#-performance)
- [🧪 Testes](#-testes)

---

## 🎯 Visão Geral

### Propósito do Sistema
O **EscalaMedica2** é um sistema de gestão médica desenvolvido em Laravel 11, projetado para [inserir descrição específica conforme evolução do projeto].

### Características Principais
- **Framework**: Laravel 11.46.1 (LTS)
- **Arquitetura**: MVC (Model-View-Controller)
- **Banco de Dados**: SQLite (desenvolvimento) / MySQL (produção)
- **Frontend**: Blade Templates + [definir stack frontend]
- **Estilo**: [definir framework CSS - Bootstrap, Tailwind, etc.]

### Stakeholders
- **Desenvolvedores**: Implementação e manutenção
- **QA**: Testes e validação
- **Médicos**: Usuários finais
- **Administradores**: Gestão do sistema

---

## 🏛️ Arquitetura Geral

### Diagrama de Arquitetura

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│    Frontend     │    │    Backend      │    │   Banco de      │
│   (Blade/JS)    │◄──►│   (Laravel)     │◄──►│     Dados       │
│                 │    │                 │    │  (SQLite/MySQL) │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Interfaces    │    │   Controllers   │    │    Migrations   │
│   (Views)       │    │   Models        │    │    Seeders     │
│   Components    │    │   Services      │    │    Factories   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Fluxo de Dados

1. **Request** → Route → Controller
2. **Controller** → Service/Repository (opcional) → Model
3. **Model** → Database
4. **Response** ← View ← Controller ← Model

### Camadas do Sistema

#### 🌐 Camada de Apresentação (Presentation Layer)
- **Responsabilidade**: Interface do usuário e experiência
- **Tecnologias**: Blade Templates, CSS, JavaScript
- **Localização**: `resources/views/`

#### 🎮 Camada de Controle (Controller Layer)
- **Responsabilidade**: Lógica de aplicação e coordenação
- **Tecnologias**: Laravel Controllers
- **Localização**: `app/Http/Controllers/`

#### 💼 Camada de Negócio (Business Layer)
- **Responsabilidade**: Regras de negócio e validações
- **Tecnologias**: Services, Repositories, Models
- **Localização**: `app/Services/`, `app/Models/`

#### 🗄️ Camada de Dados (Data Layer)
- **Responsabilidade**: Persistência e acesso aos dados
- **Tecnologias**: Eloquent ORM, Migrations
- **Localização**: `app/Models/`, `database/`

---

## 📊 Estrutura de Pastas

### Organização do Projeto Laravel

```
EscalaMedica2/
├── 📁 app/
│   ├── 📁 Console/           # Comandos Artisan
│   ├── 📁 Exceptions/        # Tratamento de exceções
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/   # Controllers da aplicação
│   │   ├── 📁 Middleware/    # Middlewares customizados
│   │   └── 📁 Requests/      # Form Requests
│   ├── 📁 Models/            # Models Eloquent
│   ├── 📁 Providers/         # Service Providers
│   └── 📁 Services/          # Services customizados
├── 📁 bootstrap/             # Inicialização da aplicação
├── 📁 config/                # Arquivos de configuração
├── 📁 database/
│   ├── 📁 factories/         # Model Factories
│   ├── 📁 migrations/        # Migrations do banco
│   └── 📁 seeders/           # Seeders
├── 📁 docs/                  # Documentação técnica
├── 📁 public/                # Assets públicos
├── 📁 resources/
│   ├── 📁 css/              # Estilos CSS
│   ├── 📁 js/               # Scripts JavaScript
│   └── 📁 views/            # Templates Blade
├── 📁 routes/                # Definição de rotas
├── 📁 storage/               # Arquivos de armazenamento
├── 📁 tests/                 # Testes automatizados
└── 📁 vendor/                # Dependências do Composer
```

### Convenções de Nomenclatura por Pasta

- **Controllers**: `PascalCaseController.php`
- **Models**: `PascalCase.php`
- **Services**: `PascalCaseService.php`
- **Migrations**: `yyyy_mm_dd_hhmmss_action_table.php`
- **Views**: `kebab-case.blade.php`

---

## 🗄️ Banco de Dados

### Configuração Atual

#### Desenvolvimento (SQLite)
```php
'default' => 'sqlite',
'connections' => [
    'sqlite' => [
        'driver' => 'sqlite',
        'database' => database_path('database.sqlite'),
        'prefix' => '',
        'foreign_key_constraints' => true,
    ],
]
```

#### Produção (MySQL) - Configuração Futura
```php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'escala_medica'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]
```

### Convenções de Banco de Dados

#### Tabelas
- **Nomenclatura**: snake_case, plural (ex: `users`, `medical_schedules`)
- **Chaves Primárias**: `id` (auto-increment)
- **Chaves Estrangeiras**: `table_name_id` (ex: `user_id`)
- **Timestamps**: `created_at`, `updated_at` (automático)
- **Soft Deletes**: `deleted_at` (quando aplicável)

#### Campos Comuns
```php
// Padrão para todas as tabelas
$table->id();                          // Chave primária
$table->timestamps();                  // created_at, updated_at
$table->softDeletes();                // deleted_at (opcional)

// Campos de auditoria (opcional)
$table->unsignedBigInteger('created_by')->nullable();
$table->unsignedBigInteger('updated_by')->nullable();
```

### Relacionamentos

#### Tipos de Relacionamento
- **One-to-One**: `hasOne()` / `belongsTo()`
- **One-to-Many**: `hasMany()` / `belongsTo()`
- **Many-to-Many**: `belongsToMany()`
- **Polymorphic**: `morphTo()` / `morphMany()`

#### Exemplo de Relacionamentos
```php
// User Model
public function profile() {
    return $this->hasOne(Profile::class);
}

public function posts() {
    return $this->hasMany(Post::class);
}

public function roles() {
    return $this->belongsToMany(Role::class);
}
```

---

## 🛣️ Sistema de Rotas

### Estrutura de Rotas

#### Web Routes (`routes/web.php`)
```php
// Rotas públicas
Route::get('/', WelcomeController::class)->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// Rotas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('users', UserController::class);
});

// Rotas administrativas
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('settings', SettingController::class);
});
```

#### API Routes (`routes/api.php`)
```php
// Rotas de API (futuro)
Route::prefix('v1')->group(function () {
    Route::apiResource('users', Api\UserController::class);
});
```

### Convenções de Rotas

#### Nomenclatura
- **Web**: `resource.action` (ex: `user.show`)
- **API**: `api.v1.resource.action`
- **Admin**: `admin.resource.action`

#### Parâmetros
- **IDs**: sempre numéricos
- **Slugs**: kebab-case para URLs amigáveis
- **Recursos**: plural (ex: `/users/{user}`)

---

## 🧩 Componentes e Camadas

### Controllers

#### Responsabilidades
- Receber requests HTTP
- Validar dados de entrada
- Coordenar com Services/Models
- Retornar responses

#### Estrutura Padrão
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function index()
    {
        // Lista recursos
    }

    public function create()
    {
        // Exibe formulário de criação
    }

    public function store(Request $request)
    {
        // Processa criação
    }

    public function show($id)
    {
        // Exibe recurso específico
    }

    public function edit($id)
    {
        // Exibe formulário de edição
    }

    public function update(Request $request, $id)
    {
        // Processa atualização
    }

    public function destroy($id)
    {
        // Remove recurso
    }
}
```

### Models

#### Responsabilidades
- Representar entidades do banco
- Definir relacionamentos
- Implementar business logic simples
- Configurar atributos e casting

#### Estrutura Padrão
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Example extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'status' => 'boolean',
    ];

    // Relacionamentos
    public function relatedModel()
    {
        return $this->belongsTo(RelatedModel::class);
    }
}
```

### Services (Camada de Negócio)

#### Quando Usar
- Lógica de negócio complexa
- Operações que envolvem múltiplos models
- Integrações com APIs externas
- Processamento de dados complexo

#### Estrutura Padrão
```php
<?php

namespace App\Services;

class ExampleService
{
    public function processBusinessLogic($data)
    {
        // Implementar lógica de negócio
    }

    public function integrateWithExternalApi($params)
    {
        // Implementar integração externa
    }
}
```

---

## ⚙️ Configurações

### Arquivos de Configuração Principais

#### `config/app.php`
```php
'name' => env('APP_NAME', 'EscalaMedica2'),
'env' => env('APP_ENV', 'production'),
'debug' => env('APP_DEBUG', false),
'url' => env('APP_URL', 'http://localhost'),
'timezone' => 'America/Sao_Paulo',
'locale' => 'pt_BR',
```

#### `config/database.php`
- Configurações de conexão com banco de dados
- Definição de drivers e credenciais

#### `config/mail.php`
- Configurações de envio de email
- Drivers de email (SMTP, SendGrid, etc.)

### Variáveis de Ambiente (`.env`)

#### Desenvolvimento
```env
APP_NAME=EscalaMedica2
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

MAIL_MAILER=log
```

#### Produção
```env
APP_NAME=EscalaMedica2
APP_ENV=production
APP_DEBUG=false
APP_URL=https://escalamedica2.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=escala_medica_prod
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
```

---

## 🔧 Padrões de Desenvolvimento

### Código PHP

#### PSR Standards
- **PSR-4**: Autoloading
- **PSR-12**: Coding Style
- **PSR-7**: HTTP Message Interface

#### Convenções Laravel
```php
// Nomes de Classes: PascalCase
class UserController extends Controller {}

// Nomes de Métodos: camelCase
public function showProfile() {}

// Nomes de Variáveis: camelCase
$userData = [];

// Constantes: UPPER_SNAKE_CASE
const MAX_UPLOAD_SIZE = 1024;
```

### Banco de Dados

#### Migrations
```php
// Sempre usar Schema Builder
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

#### Seeders
```php
// Usar factories quando possível
User::factory()->count(50)->create();

// Dados específicos para produção
User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
]);
```

### Frontend (Blade)

#### Estrutura de Views
```php
// Layout principal
@extends('layouts.app')

// Seções de conteúdo
@section('title', 'Título da Página')

@section('content')
    <div class="container">
        <h1>{{ $title }}</h1>
        @include('partials.breadcrumb')
        
        @if($errors->any())
            @include('partials.errors')
        @endif
        
        <!-- Conteúdo específico -->
    </div>
@endsection
```

### Validação

#### Form Requests
```php
class StoreUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Este email já está em uso.',
        ];
    }
}
```

---

## 🔗 Integrações e APIs

### APIs Externas (Futuro)

#### Estrutura para Integrações
```php
namespace App\Services\External;

class ExternalApiService
{
    protected $httpClient;
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->httpClient = new \GuzzleHttp\Client();
        $this->baseUrl = config('services.external_api.base_url');
        $this->apiKey = config('services.external_api.key');
    }

    public function makeRequest($endpoint, $data = [])
    {
        // Implementar requisição
    }
}
```

### API Interna (Futuro)

#### Versionamento
- **v1**: `/api/v1/`
- **v2**: `/api/v2/` (quando necessário)

#### Autenticação
- **Sanctum**: Para SPAs
- **Passport**: Para OAuth2 (se necessário)

---

## 🛡️ Segurança

### Medidas de Segurança Implementadas

#### Laravel Security Features
- **CSRF Protection**: Automático em formulários
- **XSS Protection**: Escape automático no Blade
- **SQL Injection**: Proteção via Eloquent ORM
- **Mass Assignment**: Proteção via `$fillable`

#### Middleware de Segurança
```php
// Aplicar em rotas sensíveis
Route::middleware(['auth', 'verified', 'throttle:60,1'])->group(function () {
    // Rotas protegidas
});
```

#### Autenticação
- **Laravel Breeze**: Starter kit simples
- **Laravel Jetstream**: Full-featured (futuro)

### Boas Práticas de Segurança

1. **Senhas**: Sempre usar `Hash::make()`
2. **Dados Sensíveis**: Nunca no código, sempre no `.env`
3. **Validação**: Sempre validar inputs
4. **Autorização**: Usar Gates e Policies
5. **HTTPS**: Obrigatório em produção

---

## 📈 Performance

### Otimizações Implementadas

#### Cache
```php
// Cache de configuração
php artisan config:cache

// Cache de rotas
php artisan route:cache

// Cache de views
php artisan view:cache
```

#### Database
- **Eager Loading**: Evitar N+1 queries
- **Indexes**: Em campos de busca frequente
- **Query Optimization**: Usar DB::listen() para debug

#### Frontend
- **Asset Compilation**: Via Vite
- **Minification**: Automática em produção
- **CDN**: Para assets estáticos (futuro)

### Monitoramento

#### Logs
```php
// Diferentes níveis de log
Log::info('User logged in', ['user_id' => $user->id]);
Log::warning('Failed login attempt', ['email' => $email]);
Log::error('Database connection failed', ['exception' => $e]);
```

#### Métricas (Futuro)
- **Response Time**: Tempo de resposta
- **Memory Usage**: Uso de memória
- **Database Queries**: Número e tempo de queries

---

## 🧪 Testes

### Estrutura de Testes

#### Tipos de Teste
- **Unit Tests**: `tests/Unit/`
- **Feature Tests**: `tests/Feature/`
- **Browser Tests**: `tests/Browser/` (Dusk)

#### Exemplo de Teste
```php
class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('/users', $userData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);
    }
}
```

### Comando de Testes
```bash
# Executar todos os testes
php artisan test

# Executar com coverage
php artisan test --coverage

# Executar testes específicos
php artisan test --filter UserTest
```

---

## 📚 Referências e Links Úteis

### Documentação Oficial
- [Laravel Documentation](https://laravel.com/docs/11.x)
- [PHP Manual](https://www.php.net/manual/en/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

### Ferramentas de Desenvolvimento
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)
- [Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper)
- [PHPStan](https://phpstan.org/)

### Comunidade
- [Laravel News](https://laravel-news.com/)
- [Laracasts](https://laracasts.com/)
- [Laravel Daily](https://laraveldaily.com/)

---

**📍 Última atualização**: 2025-10-20  
**👥 Contribuidores**: Sistema/Desenvolvedor  
**📋 Status**: Documentação inicial - expandir conforme evolução do projeto  
**🔄 Próxima revisão**: A cada grande implementação ou mudança arquitetural