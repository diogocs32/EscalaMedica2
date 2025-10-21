# 📋 Plano de Ação - Guia de Implementação

> **Objetivo**: Fornecer exemplos práticos, snippets reutilizáveis e orientações para implementar funcionalidades com segurança e consistência.

## 📊 Informações do Guia
- **Sistema**: EscalaMedica2
- **Laravel**: 11.46.1
- **Última Atualização**: 2025-10-20
- **Status**: Inicial - expandir com cada implementação

---

## 📋 Índice
- [🚀 Workflow de Desenvolvimento](#-workflow-de-desenvolvimento)
- [🎯 Templates de Implementação](#-templates-de-implementação)
- [📝 Snippets Reutilizáveis](#-snippets-reutilizáveis)
- [⚡ Comandos Essenciais](#-comandos-essenciais)
- [🛡️ Checklist de Segurança](#-checklist-de-segurança)
- [🧪 Padrões de Teste](#-padrões-de-teste)
- [📦 Deploy e Produção](#-deploy-e-produção)

---

## 🚀 Workflow de Desenvolvimento

### 📋 Processo Obrigatório para Cada Feature

#### 1. **ANTES DE COMEÇAR** ⚠️
```bash
# 1. Consultar documentação
cat REGISTRY.md                    # Verificar nomenclaturas e dependências
cat HISTORICO_COMMITS.md          # Ver implementações similares
cat docs/DOCUMENTACAO_TECNICA.md  # Entender arquitetura

# 2. Criar branch (se usando Git)
git checkout -b feature/nome-da-feature
```

#### 2. **PLANEJAMENTO**
- [ ] Definir entidades necessárias
- [ ] Mapear relacionamentos
- [ ] Planejar rotas
- [ ] Identificar middlewares necessários
- [ ] Definir validações
- [ ] Considerar impacto em funcionalidades existentes

#### 3. **IMPLEMENTAÇÃO**
- [ ] Criar migration
- [ ] Criar model
- [ ] Criar controller
- [ ] Definir rotas
- [ ] Criar views
- [ ] Implementar validações
- [ ] Criar testes

#### 4. **VALIDAÇÃO**
- [ ] Executar testes
- [ ] Verificar padrões de código
- [ ] Testar funcionalidade manualmente
- [ ] Verificar responsividade (se aplicável)

#### 5. **DOCUMENTAÇÃO** ⚠️
- [ ] Atualizar REGISTRY.md
- [ ] Documentar em HISTORICO_COMMITS.md
- [ ] Atualizar DOCUMENTACAO_TECNICA.md (se mudanças arquiteturais)
- [ ] Commit com mensagem descritiva

---

## 🎯 Templates de Implementação

### 🗄️ Criando Nova Entidade Completa

#### 1. Migration
```bash
# Comando
php artisan make:migration create_[table_name]_table

# Template para Migration
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('[table_name]', function (Blueprint $table) {
            $table->id();
            
            // Campos específicos da entidade
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            // Relacionamentos (exemplo)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Campos de auditoria (opcional)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Se usar soft delete
            
            // Índices
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('[table_name]');
    }
};
```

#### 2. Model
```bash
# Comando
php artisan make:model [ModelName]

# Template para Model
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class [ModelName] extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
        'user_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors & Mutators
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }
}
```

#### 3. Controller
```bash
# Comando
php artisan make:controller [ModelName]Controller --resource

# Template para Resource Controller
<?php
namespace App\Http\Controllers;

use App\Models\[ModelName];
use App\Http\Requests\Store[ModelName]Request;
use App\Http\Requests\Update[ModelName]Request;
use Illuminate\Http\Request;

class [ModelName]Controller extends Controller
{
    public function index()
    {
        $items = [ModelName]::with(['user', 'creator'])
            ->active()
            ->latest()
            ->paginate(10);

        return view('[model_name].index', compact('items'));
    }

    public function create()
    {
        return view('[model_name].create');
    }

    public function store(Store[ModelName]Request $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $item = [ModelName]::create($data);

        return redirect()
            ->route('[model_name].show', $item)
            ->with('success', '[ModelName] criado com sucesso!');
    }

    public function show([ModelName] $[modelName])
    {
        $[modelName]->load(['user', 'creator']);
        
        return view('[model_name].show', compact('[modelName]'));
    }

    public function edit([ModelName] $[modelName])
    {
        return view('[model_name].edit', compact('[modelName]'));
    }

    public function update(Update[ModelName]Request $request, [ModelName] $[modelName])
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $[modelName]->update($data);

        return redirect()
            ->route('[model_name].show', $[modelName])
            ->with('success', '[ModelName] atualizado com sucesso!');
    }

    public function destroy([ModelName] $[modelName])
    {
        $[modelName]->delete();

        return redirect()
            ->route('[model_name].index')
            ->with('success', '[ModelName] removido com sucesso!');
    }
}
```

#### 4. Form Requests
```bash
# Comando
php artisan make:request Store[ModelName]Request
php artisan make:request Update[ModelName]Request

# Template para Store Request
<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store[ModelName]Request extends FormRequest
{
    public function authorize()
    {
        return auth()->check(); // Ajustar conforme regras de autorização
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:active,inactive',
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais que 255 caracteres.',
            'user_id.required' => 'É necessário selecionar um usuário.',
            'user_id.exists' => 'O usuário selecionado não existe.',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nome',
            'description' => 'descrição',
            'user_id' => 'usuário',
        ];
    }
}

# Template para Update Request
<?php
namespace App\Http\Requests;

class Update[ModelName]Request extends Store[ModelName]Request
{
    public function rules()
    {
        $rules = parent::rules();
        
        // Ajustar regras para update se necessário
        // Ex: unique com exceção do registro atual
        // $rules['email'] = 'required|email|unique:users,email,' . $this->route('user')->id;
        
        return $rules;
    }
}
```

#### 5. Rotas
```php
// Em routes/web.php
Route::middleware('auth')->group(function () {
    Route::resource('[model_name]', [ModelName]Controller::class);
});

// Ou com middlewares específicos
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('[model_name]', [ModelName]Controller::class);
});

// Para APIs (routes/api.php)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('[model_name]', Api\[ModelName]Controller::class);
});
```

#### 6. Views Base
```php
{{-- resources/views/[model_name]/index.blade.php --}}
@extends('layouts.app')

@section('title', '[ModelName] - Listagem')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>[ModelName]</h1>
        <a href="{{ route('[model_name].create') }}" class="btn btn-primary">
            Novo [ModelName]
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Status</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            <span class="badge {{ $item->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td>{{ $item->formatted_created_at }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('[model_name].show', $item) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                <a href="{{ route('[model_name].edit', $item) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                <form action="{{ route('[model_name].destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Tem certeza?')">Remover</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Nenhum registro encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links() }}
</div>
@endsection
```

---

## 📝 Snippets Reutilizáveis

### 🔍 Busca e Filtros
```php
// Controller - método index com filtros
public function index(Request $request)
{
    $query = [ModelName]::query();

    // Filtro por nome
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Filtro por status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filtro por data
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $items = $query->with(['user'])
        ->latest()
        ->paginate(10)
        ->withQueryString(); // Manter filtros na paginação

    return view('[model_name].index', compact('items'));
}
```

### 📊 Dashboard Widgets
```php
// Controller para dashboard
public function dashboard()
{
    $stats = [
        'total_users' => User::count(),
        'active_users' => User::where('status', 'active')->count(),
        'recent_registrations' => User::whereDate('created_at', '>=', now()->subDays(7))->count(),
    ];

    $recentActivity = User::with(['creator'])
        ->latest()
        ->limit(5)
        ->get();

    return view('dashboard', compact('stats', 'recentActivity'));
}
```

### 📤 Exportação de Dados
```php
// Método para exportar dados
public function export(Request $request)
{
    $items = [ModelName]::query()
        ->when($request->status, fn($q) => $q->where('status', $request->status))
        ->with(['user'])
        ->get();

    $filename = '[model_name]_' . now()->format('Y-m-d_H-i-s') . '.csv';

    return response()->streamDownload(function () use ($items) {
        $handle = fopen('php://output', 'w');
        
        // Cabeçalho
        fputcsv($handle, ['ID', 'Nome', 'Status', 'Usuário', 'Criado em']);
        
        // Dados
        foreach ($items as $item) {
            fputcsv($handle, [
                $item->id,
                $item->name,
                $item->status,
                $item->user->name ?? '',
                $item->created_at->format('d/m/Y H:i'),
            ]);
        }
        
        fclose($handle);
    }, $filename);
}
```

### 🔄 Jobs para Processamento Assíncrono
```php
// Comando para criar job
php artisan make:job Process[ModelName]Job

// Template para Job
<?php
namespace App\Jobs;

use App\Models\[ModelName];
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Process[ModelName]Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $[modelName];

    public function __construct([ModelName] $[modelName])
    {
        $this->[modelName] = $[modelName];
    }

    public function handle()
    {
        // Processamento pesado aqui
        // Ex: envio de emails, processamento de imagens, etc.
    }
}

// Disparar o job
Process[ModelName]Job::dispatch($[modelName]);
```

---

## ⚡ Comandos Essenciais

### 🏗️ Criação de Estruturas
```bash
# Criar model com migration, factory e controller
php artisan make:model ModelName -mfc

# Criar resource controller
php artisan make:controller ModelController --resource

# Criar API resource controller
php artisan make:controller Api/ModelController --api

# Criar request de validação
php artisan make:request StoreModelRequest

# Criar seeder
php artisan make:seeder ModelSeeder

# Criar factory
php artisan make:factory ModelFactory

# Criar job
php artisan make:job ProcessModelJob

# Criar service provider
php artisan make:provider ModelServiceProvider

# Criar middleware
php artisan make:middleware CheckModelPermission
```

### 🗄️ Banco de Dados
```bash
# Executar migrations
php artisan migrate

# Rollback última migration
php artisan migrate:rollback

# Rollback múltiplas migrations
php artisan migrate:rollback --step=3

# Resetar e executar migrations
php artisan migrate:fresh

# Executar migrations com seeders
php artisan migrate:fresh --seed

# Verificar status das migrations
php artisan migrate:status

# Criar arquivo de banco SQLite
touch database/database.sqlite
```

### 🔧 Artisan Úteis
```bash
# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Gerar caches para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Listar rotas
php artisan route:list

# Executar testes
php artisan test

# Executar testes com coverage
php artisan test --coverage

# Executar queue worker
php artisan queue:work

# Ver jobs na queue
php artisan queue:monitor

# Servir aplicação
php artisan serve

# Gerar APP_KEY
php artisan key:generate
```

### 📦 Composer
```bash
# Instalar dependências
composer install

# Instalar dependências de produção apenas
composer install --no-dev --optimize-autoloader

# Atualizar dependências
composer update

# Adicionar nova dependência
composer require package/name

# Adicionar dependência de desenvolvimento
composer require --dev package/name

# Remover dependência
composer remove package/name

# Autoload dump
composer dump-autoload
```

---

## 🛡️ Checklist de Segurança

### ✅ Antes de Cada Deploy

#### Código
- [ ] Validação de todos os inputs
- [ ] Autorização implementada (Gates/Policies)
- [ ] Senha sempre hasheada
- [ ] Dados sensíveis no `.env`
- [ ] CSRF protection ativo
- [ ] Mass assignment protegido (`$fillable`)
- [ ] SQL injection prevention (usar Eloquent)
- [ ] XSS prevention (escape de dados)

#### Configuração
- [ ] `APP_DEBUG=false` em produção
- [ ] `APP_ENV=production`
- [ ] HTTPS configurado
- [ ] Logs configurados adequadamente
- [ ] Backup automático do banco
- [ ] Monitoring configurado

#### Banco de Dados
- [ ] Migrations testadas
- [ ] Seeders seguros (sem dados sensíveis)
- [ ] Índices criados
- [ ] Foreign keys configuradas
- [ ] Soft deletes onde necessário

---

## 🧪 Padrões de Teste

### 🧪 Unit Tests
```php
// tests/Unit/UserTest.php
<?php
namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);
    }

    public function test_user_password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => 'password123'
        ]);

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
```

### 🌐 Feature Tests
```php
// tests/Feature/UserControllerTest.php
<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_users_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/users');

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
    }

    public function test_user_can_be_created_via_form()
    {
        $user = User::factory()->create();

        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->actingAs($user)
            ->post('/users', $userData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com'
        ]);
    }

    public function test_validation_errors_are_returned()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/users', []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }
}
```

### 🏭 Factories
```php
// database/factories/UserFactory.php
<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }
}
```

---

## 📦 Deploy e Produção

### 🚀 Checklist de Deploy

#### Pré-Deploy
- [ ] Testes passando (`php artisan test`)
- [ ] Código commitado e pushed
- [ ] Variáveis de ambiente configuradas
- [ ] Banco de produção configurado
- [ ] SSL/HTTPS configurado
- [ ] Domínio apontando para servidor

#### Deploy
```bash
# No servidor de produção
git pull origin main

# Instalar dependências
composer install --no-dev --optimize-autoloader

# Executar migrations
php artisan migrate --force

# Limpar e recriar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar workers (se usando queues)
php artisan queue:restart

# Verificar se está funcionando
php artisan about
```

#### Pós-Deploy
- [ ] Verificar funcionalidades críticas
- [ ] Verificar logs de erro
- [ ] Verificar performance
- [ ] Backup do banco de dados
- [ ] Notificar stakeholders

### 🔧 Configuração de Servidor (Referência)

#### Nginx Config (Exemplo)
```nginx
server {
    listen 80;
    listen 443 ssl;
    server_name escalamedica2.com;
    root /var/www/escalamedica2/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 📞 Suporte e Troubleshooting

### 🐛 Problemas Comuns

#### Erro: "Class not found"
```bash
# Solução: Recriar autoload
composer dump-autoload
```

#### Erro: "Permission denied"
```bash
# Solução: Ajustar permissões (Linux/Mac)
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### Erro: "SQLSTATE connection refused"
```bash
# Verificar configuração do banco
php artisan config:cache
php artisan config:clear
```

### 📞 Contatos de Emergência
- **Desenvolvedor Principal**: [definir]
- **DevOps**: [definir]
- **Suporte**: [definir]

---

**📍 Última atualização**: 2025-10-20  
**👥 Responsável**: Sistema/Desenvolvedor  
**📋 Status**: Guia inicial - expandir com experiências reais  
**🔄 Próxima revisão**: A cada implementação significativa

> **💡 Dica**: Sempre consulte este guia antes de implementar e atualize-o com novos padrões descobertos!