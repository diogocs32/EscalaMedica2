# ⚡ QUICK REFERENCE - EscalaMedica2

> **Referenciado por**: `REGISTRY.md` → "Acesso Rápido"

---

## 🚀 COMANDOS ESSENCIAIS

### **Desenvolvimento Local**
```bash
# Iniciar servidor
php artisan serve --host=localhost --port=8000

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Testes
php artisan test
php artisan test --coverage
```

### **Manutenção Database**
```bash
# Backup
mysqldump -u root escalaMedica2 > backup_$(date +%Y%m%d).sql

# Restore  
mysql -u root escalaMedica2 < backup_20241228.sql

# Reset completo
php artisan migrate:fresh --seed
```

---

## 📁 ESTRUTURA RÁPIDA

### **Controllers Principais**
```
app/Http/Controllers/
├── DashboardController.php      → Painel principal
├── PlantonistaController.php    → Gestão médicos
├── UnidadeController.php        → Gestão hospitais
├── SetorController.php          → Gestão departamentos
├── TurnoController.php          → Gestão horários
└── AlocacaoController.php       → Gestão escalas
```

### **Models e Relacionamentos**
```
app/Models/
├── Plantonista.php             → hasMany(alocacoes)
├── Unidade.php                 → hasMany(setores, alocacoes)
├── Setor.php                   → belongsTo(unidade), hasMany(alocacoes)
├── Turno.php                   → hasMany(alocacoes)
└── Alocacao.php                → belongsTo(plantonista, unidade, setor, turno)
```

### **Views Críticas**
```
resources/views/
├── welcome.blade.php           → Homepage
├── dashboard.blade.php         → Painel principal
├── layouts/
│   └── app.blade.php          → Layout base
└── [entidade]/
    ├── index.blade.php        → Listagem
    ├── create.blade.php       → Criação
    ├── show.blade.php         → Detalhes
    └── edit.blade.php         → Edição
```

---

## 🔗 ROTAS PRINCIPAIS

### **Web Routes**
| Método | Rota | Controller@Action | Descrição |
|--------|------|-------------------|-----------|
| **GET** | `/` | `welcome` | Homepage |
| **GET** | `/dashboard` | `DashboardController@index` | Painel principal |
| **Resource** | `/plantonistas` | `PlantonistaController` | CRUD médicos |
| **Resource** | `/unidades` | `UnidadeController` | CRUD hospitais |
| **Resource** | `/setores` | `SetorController` | CRUD departamentos |
| **Resource** | `/turnos` | `TurnoController` | CRUD horários |
| **Resource** | `/alocacoes` | `AlocacaoController` | CRUD escalas |

### **URLs Desenvolvimento**
```
Base: http://localhost:8000

Dashboard: /dashboard
Plantonistas: /plantonistas
Unidades: /unidades  
Setores: /setores
Turnos: /turnos
Alocações: /alocacoes
```

---

## 🗄️ DATABASE QUICK REFERENCE

### **Tabelas Principais**
| Tabela | Campos Principais | Relacionamentos |
|--------|-------------------|-----------------|
| **plantonistas** | id, nome, crm, especializacao | → alocacoes |
| **unidades** | id, nome, endereco, telefone | → setores, alocacoes |
| **setores** | id, nome, unidade_id | unidade ← → alocacoes |
| **turnos** | id, nome, hora_inicio, hora_fim, duracao | → alocacoes |
| **alocacoes** | id, plantonista_id, unidade_id, setor_id, turno_id, data | ← todos |

### **Relacionamentos FK**
```sql
-- alocacoes table
FOREIGN KEY (plantonista_id) REFERENCES plantonistas(id)
FOREIGN KEY (unidade_id) REFERENCES unidades(id)  
FOREIGN KEY (setor_id) REFERENCES setores(id)
FOREIGN KEY (turno_id) REFERENCES turnos(id)

-- setores table
FOREIGN KEY (unidade_id) REFERENCES unidades(id)
```

---

## 🎨 FRONTEND QUICK REFERENCE

### **Bootstrap Classes Principais**
```css
/* Layout */
.container-fluid          → Full width container
.row                      → Bootstrap row
.col-md-*                 → Responsive columns

/* Components */
.card                     → Card container
.card-header              → Card title area
.card-body                → Card content
.btn btn-primary          → Primary button
.table table-striped      → Striped table

/* Utilities */
.d-flex                   → Flexbox
.justify-content-between  → Space between
.align-items-center       → Center align
.mb-3                     → Margin bottom 3
.text-center              → Center text
```

### **Custom CSS Classes**
```css
/* Dashboard */
.hero-section             → Homepage hero
.feature-card             → Feature cards
.stats-card               → Dashboard statistics

/* Navigation */
.sidebar                  → Dashboard sidebar
.nav-link                 → Navigation links

/* Forms */
.form-group               → Form field container
.form-control             → Input styling
```

---

## 🔧 CONFIGURAÇÕES RÁPIDAS

### **Environment (.env)**
```env
# App
APP_NAME=EscalaMedica2
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=escalaMedica2
DB_USERNAME=root
DB_PASSWORD=

# Cache
CACHE_DRIVER=file
SESSION_DRIVER=file
```

### **Configuração Apache (XAMPP)**
```apache
# httpd.conf ou .htaccess
DocumentRoot "c:/xampp/htdocs"
DirectoryIndex index.php index.html

# mod_rewrite para Laravel
LoadModule rewrite_module modules/mod_rewrite.so
```

---

## 🐛 DEBUGGING RÁPIDO

### **Log Locations**
```
Laravel Logs: storage/logs/laravel.log
Apache Logs: c:/xampp/apache/logs/error.log
MySQL Logs: c:/xampp/mysql/data/*.err
```

### **Common Debug Commands**
```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Debug SQL queries
DB::enableQueryLog();
// ... your code
dd(DB::getQueryLog());

# Cache debug
php artisan route:list        # Ver todas rotas
php artisan config:show       # Ver configurações
php artisan queue:work        # Processar filas
```

### **Validation Debug**
```php
// Controller debug
dd($request->all());          // Ver todos inputs
dd($validator->errors());     // Ver erros validação

// Model debug  
dd($model->toArray());        // Ver atributos model
dd($model->getAttributes());  // Ver dados originais
```

---

## 📊 QUERIES ÚTEIS

### **Estatísticas Dashboard**
```sql
-- Total plantonistas ativos
SELECT COUNT(*) FROM plantonistas;

-- Unidades operacionais  
SELECT COUNT(*) FROM unidades;

-- Setores disponíveis
SELECT COUNT(*) FROM setores;

-- Alocações do mês
SELECT COUNT(*) FROM alocacoes 
WHERE MONTH(data) = MONTH(CURDATE()) 
AND YEAR(data) = YEAR(CURDATE());

-- Próximos plantões
SELECT a.*, p.nome, u.nome as unidade, s.nome as setor, t.nome as turno
FROM alocacoes a
JOIN plantonistas p ON a.plantonista_id = p.id
JOIN unidades u ON a.unidade_id = u.id  
JOIN setores s ON a.setor_id = s.id
JOIN turnos t ON a.turno_id = t.id
WHERE a.data >= CURDATE()
ORDER BY a.data, t.hora_inicio
LIMIT 5;
```

### **Queries de Validação**
```sql
-- Verificar conflitos de horário
SELECT COUNT(*) FROM alocacoes a1
JOIN alocacoes a2 ON a1.plantonista_id = a2.plantonista_id
JOIN turnos t1 ON a1.turno_id = t1.id
JOIN turnos t2 ON a2.turno_id = t2.id  
WHERE a1.id != a2.id
AND a1.data = a2.data
AND (
  (t1.hora_inicio BETWEEN t2.hora_inicio AND t2.hora_fim) OR
  (t1.hora_fim BETWEEN t2.hora_inicio AND t2.hora_fim)
);

-- Integridade referencial
SELECT 'alocacoes' as tabela, COUNT(*) as registros_orfaos
FROM alocacoes a
LEFT JOIN plantonistas p ON a.plantonista_id = p.id
WHERE p.id IS NULL;
```

---

## 🔄 WORKFLOWS COMUNS

### **Adicionar Nova Feature**
1. Criar migration: `php artisan make:migration create_table_name`
2. Criar model: `php artisan make:model ModelName`
3. Criar controller: `php artisan make:controller ModelController --resource`
4. Adicionar rotas: `Route::resource('model', ModelController::class)`
5. Criar views: `index.blade.php`, `create.blade.php`, `show.blade.php`, `edit.blade.php`
6. Testar: `php artisan test`

### **Fix Bug Process**
1. Reproduzir erro
2. Identificar source: logs, dd(), debug
3. Criar test case que falha
4. Implementar fix
5. Verificar test passa
6. Commit com descrição clara

### **Deploy Checklist**
1. `composer install --no-dev`
2. `php artisan config:cache`
3. `php artisan route:cache`
4. `php artisan view:cache`
5. `php artisan migrate --force`
6. Verificar .env produção
7. Testar funcionalidades críticas

---

## 📞 CONTATOS E RECURSOS

### **Documentação Laravel**
- **Docs**: https://laravel.com/docs/11.x
- **API**: https://laravel.com/api/11.x
- **Laracasts**: https://laracasts.com

### **Bootstrap Resources**
- **Docs**: https://getbootstrap.com/docs/5.3
- **Icons**: https://icons.getbootstrap.com
- **Examples**: https://getbootstrap.com/docs/5.3/examples

### **Development Tools**
- **XAMPP**: https://www.apachefriends.org
- **Composer**: https://getcomposer.org
- **Node.js**: https://nodejs.org (se precisar)

---

## 🏃 COMANDOS DE EMERGÊNCIA

### **Sistema Não Funciona**
```bash
# Reset completo
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### **Erro de Permissões**
```bash
# Windows (Execute como Admin)
icacls "storage" /grant Everyone:(OI)(CI)F /T
icacls "bootstrap/cache" /grant Everyone:(OI)(CI)F /T
```

### **Banco Corrompido**
```bash
# Restore backup
mysql -u root escalaMedica2 < backup_latest.sql
php artisan migrate
```

### **Cache Problems**
```bash
# Limpar tudo
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

*Quick Reference completo do EscalaMedica2*
*Última atualização: 2024-12-28*
*Versão: 1.0*