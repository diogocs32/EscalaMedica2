# ⚡ Quick Reference - Comandos e Referências Rápidas

> **Objetivo**: Referência rápida para comandos essenciais, configurações e troubleshooting do dia a dia.

## 📊 Informações Rápidas
- **Sistema**: EscalaMedica2 (Laravel 11.46.1)
- **PHP**: 8.2.12
- **Banco**: SQLite (dev) / MySQL (prod)
- **Servidor Local**: XAMPP
- **Última Atualização**: 2025-10-20

---

## 📋 Índice Rápido
- [🚀 Setup e Inicialização](#-setup-e-inicialização)
- [⚡ Comandos Diários](#-comandos-diários)
- [🗄️ Banco de Dados](#-banco-de-dados)
- [🧪 Testes](#-testes)
- [🔧 Debugging](#-debugging)
- [📦 Composer](#-composer)
- [🎨 Frontend](#-frontend)
- [🔑 Artisan Custom](#-artisan-custom)
- [🐛 Troubleshooting](#-troubleshooting)
- [📝 Comandos Úteis do Sistema](#-comandos-úteis-do-sistema)

---

## 🚀 Setup e Inicialização

### 🔄 Setup Inicial (Primeiro uso)
```bash
# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Criar banco SQLite
touch database/database.sqlite

# Executar migrations
php artisan migrate

# Iniciar servidor
php artisan serve
```

### 🎯 Setup Diário (Desenvolvimento)
```bash
# Atualizar dependências (se necessário)
composer install

# Servir aplicação
php artisan serve

# Verificar status
php artisan about
```

---

## ⚡ Comandos Diários

### 🏃‍♂️ Mais Usados
```bash
# Servir aplicação local
php artisan serve

# Executar testes
php artisan test

# Listar rotas
php artisan route:list

# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Limpar cache
php artisan cache:clear

# Executar migrations
php artisan migrate

# Criar backup do banco
php artisan db:backup  # (se configurado)
```

### 🔧 Cache Management
```bash
# Limpar TODOS os caches
php artisan optimize:clear

# Limpar caches específicos
php artisan cache:clear          # Application cache
php artisan config:clear         # Configuration cache
php artisan route:clear          # Route cache
php artisan view:clear           # Compiled views
php artisan event:clear          # Event cache

# Criar caches (produção)
php artisan optimize             # Todos os caches
php artisan config:cache         # Configuration
php artisan route:cache          # Routes
php artisan view:cache           # Views
php artisan event:cache          # Events
```

---

## 🗄️ Banco de Dados

### 📊 Migrations
```bash
# Executar migrations pendentes
php artisan migrate

# Ver status das migrations
php artisan migrate:status

# Rollback última migration
php artisan migrate:rollback

# Rollback múltiplas migrations
php artisan migrate:rollback --step=3

# Resetar e executar tudo
php artisan migrate:fresh

# Com seeders
php artisan migrate:fresh --seed

# Executar migration específica
php artisan migrate:install
php artisan migrate --path=/database/migrations/specific_migration.php
```

### 🌱 Seeders
```bash
# Executar todos os seeders
php artisan db:seed

# Executar seeder específico
php artisan db:seed --class=UserSeeder

# Executar com refresh
php artisan migrate:fresh --seed
```

### 🏭 Factories
```bash
# No Tinker
php artisan tinker
User::factory()->count(10)->create()
Model::factory()->count(50)->create()
```

---

## 🧪 Testes

### 🧪 Executar Testes
```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter UserTest
php artisan test tests/Feature/UserTest.php

# Com coverage
php artisan test --coverage

# Paralelo (mais rápido)
php artisan test --parallel

# Stop on failure
php artisan test --stop-on-failure

# Verbose output
php artisan test -v
```

### 📊 Tipos de Teste
```bash
# Criar testes
php artisan make:test UserTest                    # Feature test
php artisan make:test UserTest --unit            # Unit test
php artisan make:test Api/UserTest               # API test
```

---

## 🔧 Debugging

### 🐛 Debug Commands
```bash
# Informações do sistema
php artisan about

# Debug de rotas
php artisan route:list
php artisan route:list --name=user
php artisan route:list --method=GET

# Debug de configuração
php artisan config:show
php artisan config:show database

# Debug de ambiente
php artisan env

# Debug de providers
php artisan package:discover

# Debug de jobs/queue
php artisan queue:work --verbose
php artisan queue:monitor
php artisan queue:failed
```

### 📝 Logs
```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Limpar logs
echo "" > storage/logs/laravel.log

# Ver logs específicos do dia
tail -100 storage/logs/laravel-$(date +%Y-%m-%d).log
```

### 🔍 Tinker (REPL)
```bash
# Iniciar Tinker
php artisan tinker

# Comandos úteis no Tinker
User::all()
User::find(1)
User::where('email', 'test@example.com')->first()
Cache::get('key')
config('app.name')
Route::list()
```

---

## 📦 Composer

### 📥 Dependências
```bash
# Instalar dependências
composer install

# Instalar para produção
composer install --no-dev --optimize-autoloader

# Atualizar dependências
composer update

# Adicionar dependência
composer require package/name

# Adicionar dependência de dev
composer require --dev package/name

# Remover dependência
composer remove package/name
```

### 🔧 Autoload
```bash
# Regenerar autoload
composer dump-autoload

# Com otimização
composer dump-autoload --optimize

# Ver pacotes instalados
composer show

# Ver dependências desatualizadas
composer outdated
```

---

## 🎨 Frontend

### 🏗️ Build Assets
```bash
# Instalar dependências Node
npm install

# Desenvolvimento (watch)
npm run dev

# Produção (minificado)
npm run build

# Watch com hot reload
npm run dev -- --host
```

### 📦 Assets Management
```bash
# Limpar assets compilados
rm -rf public/build/*

# Verificar arquivos gerados
ls -la public/build/
```

---

## 🔑 Artisan Custom

### 🛠️ Criação de Estruturas
```bash
# Model completo (model + migration + factory + controller)
php artisan make:model Post -mfcs

# Resource controller
php artisan make:controller PostController --resource

# API controller
php artisan make:controller Api/PostController --api --model=Post

# Form requests
php artisan make:request StorePostRequest

# Middleware
php artisan make:middleware CheckPostOwner

# Service Provider
php artisan make:provider PostServiceProvider

# Job
php artisan make:job ProcessPost

# Notification
php artisan make:notification PostCreated

# Event e Listener
php artisan make:event PostCreated
php artisan make:listener SendPostNotification --event=PostCreated

# Seeder e Factory
php artisan make:seeder PostSeeder
php artisan make:factory PostFactory

# Console command
php artisan make:command ProcessPosts
```

### 📋 Listagens
```bash
# Listar tudo
php artisan list

# Comandos específicos
php artisan route:list           # Rotas
php artisan event:list           # Events
php artisan schedule:list        # Scheduled tasks
php artisan queue:monitor        # Queue status
```

---

## 🐛 Troubleshooting

### ⚠️ Problemas Comuns

#### "Class not found"
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

#### "Permission denied" (Linux/Mac)
```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### "Connection refused" (Database)
```bash
# Verificar configuração
php artisan config:show database

# Testar conexão
php artisan tinker
DB::connection()->getPdo()

# Recriar banco SQLite
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate
```

#### "Route not found"
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list
```

#### "View not found"
```bash
php artisan view:clear
php artisan config:clear
```

#### "Mix manifest not found"
```bash
npm run build
# ou
npm run dev
```

### 🔧 Reset Completo (Último recurso)
```bash
# CUIDADO: Isso apaga tudo!
php artisan optimize:clear
composer dump-autoload
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate:fresh --seed
npm run build
```

---

## 📝 Comandos Úteis do Sistema

### 💻 Windows (PowerShell)
```powershell
# Verificar se XAMPP está rodando
Get-Process | Where-Object {$_.ProcessName -like "*apache*"}
Get-Process | Where-Object {$_.ProcessName -like "*mysql*"}

# Navegar para o projeto
cd C:\xampp\htdocs\EscalaMedica2

# Ver arquivos
dir
Get-ChildItem -Force

# Editar arquivo
notepad .env
code .  # Se tiver VS Code

# Verificar PHP
php --version
php --ini

# Verificar Composer
composer --version
composer diagnose
```

### 🐧 Linux/Mac
```bash
# Navegar para projeto
cd /path/to/EscalaMedica2

# Ver arquivos ocultos
ls -la

# Editar arquivo
nano .env
vim .env

# Verificar serviços
sudo systemctl status apache2
sudo systemctl status mysql

# Logs do sistema
tail -f /var/log/apache2/error.log
tail -f /var/log/mysql/error.log
```

### 🌐 URLs Importantes (Desenvolvimento)
```
Aplicação:         http://localhost:8000
Aplicação (XAMPP): http://localhost/EscalaMedica2/public
phpMyAdmin:        http://localhost/phpmyadmin
XAMPP Control:     http://localhost/xampp
```

---

## 📞 Referências Rápidas

### 🔗 Links Úteis
- **Laravel Docs**: https://laravel.com/docs/11.x
- **Artisan Commands**: https://laravel.com/docs/11.x/artisan
- **Eloquent ORM**: https://laravel.com/docs/11.x/eloquent
- **Blade Templates**: https://laravel.com/docs/11.x/blade
- **Validation Rules**: https://laravel.com/docs/11.x/validation#available-validation-rules

### 📱 Códigos de Status HTTP
```
200 - OK
201 - Created
302 - Redirect
400 - Bad Request
401 - Unauthorized
403 - Forbidden
404 - Not Found
422 - Validation Error
500 - Server Error
```

### 🎯 Variáveis de Ambiente Essenciais
```env
APP_NAME=EscalaMedica2
APP_ENV=local|production
APP_DEBUG=true|false
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite|mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=escala_medica
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log|smtp
CACHE_DRIVER=file|database|redis
SESSION_DRIVER=file|database
QUEUE_CONNECTION=sync|database|redis
```

---

## 🆘 Em Caso de Emergência

### 🚨 Problema Crítico em Produção
1. **Verificar logs**: `tail -f storage/logs/laravel.log`
2. **Verificar aplicação**: `php artisan about`
3. **Rollback se necessário**: `git revert <commit>`
4. **Notificar equipe**
5. **Documentar problema**

### 📞 Contatos de Emergência
```
Desenvolvedor Principal: [Definir]
DevOps/Servidor:        [Definir]
Suporte Técnico:        [Definir]
```

### 🔧 Comandos de Emergência
```bash
# Modo de manutenção
php artisan down --message="Manutenção em andamento" --retry=60

# Sair do modo de manutenção
php artisan up

# Backup rápido do banco
mysqldump -u root -p escala_medica > backup_$(date +%Y%m%d_%H%M%S).sql

# Verificar espaço em disco
df -h

# Verificar uso de memória
free -h

# Verificar processos
top
```

---

**📍 Última atualização**: 2025-10-20  
**⚡ Acesso rápido**: Marcar como favorito para consulta diária  
**🔄 Atualização**: Adicionar novos comandos conforme uso  

> **💡 Dica**: Use `Ctrl+F` para buscar rapidamente o que precisa!