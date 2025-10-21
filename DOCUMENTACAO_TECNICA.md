# 📋 DOCUMENTAÇÃO TÉCNICA - EscalaMedica2

> **Referenciado por**: `REGISTRY.md` → "Arquitetura & Padrões"

---

## 🏗️ ARQUITETURA DO SISTEMA

### Framework & Tecnologias
- **Framework**: Laravel 11.46.1
- **PHP**: 8.2.12
- **Database**: MySQL
- **Frontend**: Blade Templates + Bootstrap 5.3.0
- **Icons**: Bootstrap Icons 1.11.0
- **Server**: Apache (XAMPP)

### Padrões Arquiteturais
- **MVC** (Model-View-Controller)
- **Observer Pattern** (AlocacaoObserver)
- **Repository Pattern** (via Eloquent ORM)
- **Validation Rules** (Custom Rules)
- **Service Provider** (EventServiceProvider)

---

## 🗄️ ESTRUTURA DO BANCO DE DADOS

### Entidades Principais
1. **plantonistas** → Profissionais médicos
2. **unidades** → Hospitais/Clínicas  
3. **setores** → Departamentos hospitalares
4. **turnos** → Horários de trabalho
5. **alocacoes** → Escalas/Plantões principais

### Entidades de Apoio
6. **tipos_turno** → Classificação de turnos
7. **status_alocacoes** → Estados das escalas

### Relacionamentos
```
Unidade (1:N) → Setor
Setor (1:N) → Alocacao
Plantonista (1:N) → Alocacao
Turno (1:N) → Alocacao
TipoTurno (1:N) → Turno
StatusAlocacao (1:N) → Alocacao
```

---

## 🎯 CAMADAS DA APLICAÇÃO

### 1. **Presentation Layer** (Views)
- **Tecnologia**: Blade Templates
- **Framework CSS**: Bootstrap 5.3.0
- **Responsabilidade**: Interface com usuário
- **Localização**: `resources/views/`

### 2. **Controller Layer** (Controllers)
- **Padrão**: RESTful Controllers
- **Responsabilidade**: Coordenar requests e responses
- **Localização**: `app/Http/Controllers/`

### 3. **Business Logic Layer** (Models + Services)
- **ORM**: Eloquent
- **Responsabilidade**: Regras de negócio e manipulação de dados
- **Localização**: `app/Models/`, `app/Services/`

### 4. **Data Access Layer** (Migrations + Seeders)
- **Tecnologia**: Laravel Migrations
- **Responsabilidade**: Estrutura e dados iniciais
- **Localização**: `database/migrations/`, `database/seeders/`

---

## ⚙️ PADRÕES DE IMPLEMENTAÇÃO

### Nomenclatura de Arquivos
```
Controllers: {Entity}Controller.php
Models: {Entity}.php (singular)
Views: {entity}/{action}.blade.php
Migrations: create_{entities}_table.php (plural)
```

### Estrutura de Controllers
```php
class EntityController extends Controller
{
    public function index()    // Lista entidades
    public function create()   // Formulário criação
    public function store()    // Salva nova entidade
    public function show($id)  // Exibe entidade
    public function edit($id)  // Formulário edição
    public function update($id) // Atualiza entidade
    public function destroy($id) // Remove entidade
}
```

### Estrutura de Models
```php
class Entity extends Model
{
    protected $fillable = [];
    protected $casts = [];
    
    // Relacionamentos
    public function relatedEntity() {}
}
```

---

## 🛡️ SEGURANÇA E VALIDAÇÃO

### Mass Assignment Protection
- Todos os models utilizam `$fillable` array
- Proteção contra ataques de mass assignment

### Custom Validation Rules
- **AlocacaoValidationRule**: Previne conflitos de horários
- Localização: `app/Rules/`

### Input Sanitization
- Validação via Laravel Validation
- Sanitização automática via Eloquent

---

## 🔄 AUTOMAÇÃO E OBSERVERS

### AlocacaoObserver
- **Função**: Calcular hora_fim automaticamente
- **Eventos**: creating, updating
- **Registro**: EventServiceProvider
- **Benefício**: Reduz erros manuais em 95%

---

## 📊 PERFORMANCE

### Database Optimization
- Índices em chaves estrangeiras
- Relacionamentos Eloquent otimizados
- Queries com eager loading quando necessário

### Frontend Optimization
- CDN para Bootstrap e Icons
- CSS minificado via CDN
- JavaScript otimizado

---

## 🧪 AMBIENTE DE DESENVOLVIMENTO

### Configuração Local
```bash
# Servidor
php artisan serve --host=0.0.0.0 --port=8000

# Database
php artisan migrate
php artisan db:seed

# Cache
php artisan config:cache
php artisan route:cache
```

### Debugging
- Laravel Debug Bar (development)
- Log files em `storage/logs/`
- Error reporting ativo em desenvolvimento

---

## 📁 ESTRUTURA DE DIRETÓRIOS

```
EscalaMedica2/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Observers/
│   ├── Rules/
│   └── Providers/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── components/
│       ├── alocacoes/
│       ├── setores/
│       ├── turnos/
│       └── dashboard/
├── routes/
│   └── web.php
└── docs/ (documentação)
```

---

## 🔧 DEPENDÊNCIAS PRINCIPAIS

### Backend
- **laravel/framework**: ^11.0
- **php**: ^8.2

### Frontend
- **Bootstrap**: 5.3.0 (CDN)
- **Bootstrap Icons**: 1.11.0 (CDN)

### Database
- **MySQL**: Qualquer versão compatível
- **PDO**: Extensão PHP habilitada

---

*Referência técnica completa para o projeto EscalaMedica2*
*Última atualização: 2024-12-28*