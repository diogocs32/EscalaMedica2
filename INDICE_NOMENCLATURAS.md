# 📖 ÍNDICE DE NOMENCLATURAS - EscalaMedica2

> **Objetivo**: Centralizar registro de todas as nomenclaturas utilizadas no sistema para evitar duplicação e facilitar manutenção.

---

## 🏗️ CONTROLLERS

### Classes
- **`DashboardController`** 
  - Localização: `app/Http/Controllers/DashboardController.php`
  - Uso: Rotas `/dashboard`, views `resources/views/dashboard/*`
  - Dependências: Models Plantonista, Unidade, Setor, Alocacao

- **`AlocacaoController`**
  - Localização: `app/Http/Controllers/AlocacaoController.php` 
  - Uso: Rotas `/alocacoes`, views `resources/views/alocacoes/*`
  - Dependências: Models Alocacao, Plantonista, Setor, Turno

- **`SetorController`**
  - Localização: `app/Http/Controllers/SetorController.php`
  - Uso: Rotas `/setores`, views `resources/views/setores/*`
  - Dependências: Models Setor, Unidade

- **`TurnoController`**
  - Localização: `app/Http/Controllers/TurnoController.php`
  - Uso: Rotas `/turnos`, views `resources/views/turnos/*`
  - Dependências: Model Turno

---

## 🗄️ MODELS

### Classes Eloquent
- **`Plantonista`**
  - Localização: `app/Models/Plantonista.php`
  - Relacionamentos: hasMany(Alocacao)
  - Campos: nome, crm, especialidade, telefone, email

- **`Unidade`**
  - Localização: `app/Models/Unidade.php`
  - Relacionamentos: hasMany(Setor)
  - Campos: nome, endereco, telefone

- **`Setor`**
  - Localização: `app/Models/Setor.php`
  - Relacionamentos: belongsTo(Unidade), hasMany(Alocacao)
  - Campos: nome, descricao, unidade_id

- **`Turno`**
  - Localização: `app/Models/Turno.php`
  - Relacionamentos: hasMany(Alocacao), belongsTo(TipoTurno)
  - Campos: nome, hora_inicio, hora_fim, duracao_horas

- **`Alocacao`**
  - Localização: `app/Models/Alocacao.php`
  - Relacionamentos: belongsTo(Plantonista), belongsTo(Setor), belongsTo(Turno), belongsTo(StatusAlocacao)
  - Campos: data, hora_inicio, hora_fim, observacoes

- **`TipoTurno`**
  - Localização: `app/Models/TipoTurno.php`
  - Relacionamentos: hasMany(Turno)
  - Campos: nome, descricao

- **`StatusAlocacao`**
  - Localização: `app/Models/StatusAlocacao.php`
  - Relacionamentos: hasMany(Alocacao)
  - Campos: nome, descricao

---

## 🛡️ VALIDATION & OBSERVERS

### Classes de Validação
- **`AlocacaoValidationRule`**
  - Localização: `app/Rules/AlocacaoValidationRule.php`
  - Uso: Requests de AlocacaoController
  - Função: Prevenir conflitos de horários

### Observers
- **`AlocacaoObserver`**
  - Localização: `app/Observers/AlocacaoObserver.php`
  - Registro: `app/Providers/EventServiceProvider.php`
  - Função: Calcular hora_fim automaticamente

---

## 🎨 VIEWS & LAYOUTS

### Templates Base
- **`layout.blade.php`**
  - Localização: `resources/views/components/layout.blade.php`
  - Uso: Base para todas as views
  - Framework: Bootstrap 5.3.0

- **`navbar.blade.php`**
  - Localização: `resources/views/components/navbar.blade.php`
  - Uso: Navegação global
  - Dependências: Bootstrap Icons

### Views CRUD
- **Views de Alocações**
  - `resources/views/alocacoes/index.blade.php`
  - `resources/views/alocacoes/create.blade.php`
  - `resources/views/alocacoes/edit.blade.php`

- **Views de Setores**
  - `resources/views/setores/index.blade.php`
  - `resources/views/setores/create.blade.php`
  - `resources/views/setores/edit.blade.php`

- **Views de Turnos**
  - `resources/views/turnos/index.blade.php`
  - `resources/views/turnos/create.blade.php`
  - `resources/views/turnos/edit.blade.php`

### Views Especiais
- **`welcome.blade.php`**
  - Localização: `resources/views/welcome.blade.php`
  - Função: Página inicial com hero section
  - Framework: Bootstrap 5.3.0

- **`dashboard/index.blade.php`**
  - Localização: `resources/views/dashboard/index.blade.php`
  - Função: Painel principal com estatísticas
  - Framework: Bootstrap 5.3.0

### Views – Escala Padrão
- **`escalas-padrao/resumo.blade.php`**
  - Localização: `resources/views/escalas-padrao/resumo.blade.php`
  - Função: Cards-resumo por unidade (métricas e navegação)
  - Framework: Bootstrap 5.3.0 + Bootstrap Icons

- **`escalas-padrao/planilha.blade.php`**
  - Localização: `resources/views/escalas-padrao/planilha.blade.php`
  - Função: Planilha 5×7 com Turno → Setor, edição de slots e Atribuição Rápida
  - Estilos: CSS inline específico (ver seção CSS & STYLING – Planilha)
  - JS: Lógica de estados de slot e clonagem de dia/semana

- **`escalas-padrao/index.blade.php`**
  - Localização: `resources/views/escalas-padrao/index.blade.php`
  - Função: Visualização das 5 semanas em tabs

- **`escalas-padrao/create.blade.php`**
  - Localização: `resources/views/escalas-padrao/create.blade.php`
  - Função: Criação de escala padrão

- **`escalas-padrao/edit-dia.blade.php`**
  - Localização: `resources/views/escalas-padrao/edit-dia.blade.php`
  - Função: Configuração granular por dia (Turno + Setor + Quantidade)

---

## 🗄️ DATABASE

### Migrations
- **`create_plantonistas_table`** → `database/migrations/2024_12_28_000001_create_plantonistas_table.php`
- **`create_unidades_table`** → `database/migrations/2024_12_28_000002_create_unidades_table.php`
- **`create_setores_table`** → `database/migrations/2024_12_28_000003_create_setores_table.php`
- **`create_tipos_turno_table`** → `database/migrations/2024_12_28_000004_create_tipos_turno_table.php`
- **`create_turnos_table`** → `database/migrations/2024_12_28_000005_create_turnos_table.php`
- **`create_status_alocacoes_table`** → `database/migrations/2024_12_28_000006_create_status_alocacoes_table.php`
- **`create_alocacoes_table`** → `database/migrations/2024_12_28_000007_create_alocacoes_table.php`

### Seeders
- **`DatabaseSeeder`** → `database/seeders/DatabaseSeeder.php`
  - Popula: Plantonistas, Unidades, Setores, Turnos, TiposTurno, StatusAlocacoes

---

## 🎯 ROTAS

### Named Routes
- **`dashboard`** → GET `/dashboard` → `DashboardController@index`
- **`alocacoes.index`** → GET `/alocacoes` → `AlocacaoController@index`
- **`alocacoes.create`** → GET `/alocacoes/create` → `AlocacaoController@create`
- **`alocacoes.store`** → POST `/alocacoes` → `AlocacaoController@store`
- **`alocacoes.edit`** → GET `/alocacoes/{id}/edit` → `AlocacaoController@edit`
- **`alocacoes.update`** → PUT `/alocacoes/{id}` → `AlocacaoController@update`
- **`alocacoes.destroy`** → DELETE `/alocacoes/{id}` → `AlocacaoController@destroy`
- **`setores.*`** → Resource routes para SetorController
- **`turnos.*`** → Resource routes para TurnoController

---

## 🎨 CSS & STYLING

### Classes Bootstrap Customizadas
- **`.hero-section`** → Usado em `welcome.blade.php`
- **`.feature-card`** → Usado em `welcome.blade.php`
- **`.feature-icon`** → Usado em `welcome.blade.php`
- **`.btn-custom`** → Usado em `welcome.blade.php`
- **`.navbar-custom`** → Usado em `dashboard/index.blade.php`
- **`.stats-card`** → Usado em `dashboard/index.blade.php`
- **`.quick-access-card`** → Usado em `dashboard/index.blade.php`
- **`.content-card`** → Usado em `dashboard/index.blade.php`

### Planilha (Escala Padrão)
- **`.badge-slot`** → Chip de slot (nome do plantonista ou "Buraco N"); min-width: 10ch; centralizado; não quebra linha. Arquivo: `escalas-padrao/planilha.blade.php`
- **`.badge-slot.ocupado`** → Slot ocupado (paleta azul sutil). Arquivo: `escalas-padrao/planilha.blade.php`
- **`.badge-slot.buraco`** + `.badge-soft` → Slot vazio (buraco) com paleta vermelha sutil. Arquivo: `escalas-padrao/planilha.blade.php`
- **`.badge-slot.ocupado-selecionado`** → Slot ocupado pelo plantonista atualmente selecionado (verde sutil). Arquivo: `escalas-padrao/planilha.blade.php`
- **`.badge-slot.buraco-disponivel`** → Buraco disponível para o selecionado (efeito pulse). Arquivo: `escalas-padrao/planilha.blade.php`
- **`.badge-slot.buraco-indisponivel`** → Buraco indisponível por conflito (borda tracejada, opacidade). Arquivo: `escalas-padrao/planilha.blade.php`
- **`.table-schedule`** → Tabela principal com separadores verticais em todas as colunas. Arquivo: `escalas-padrao/planilha.blade.php`
- **`.thead-floating`** → Cabeçalho fixo ao topo ao rolar. Arquivo: `escalas-padrao/planilha.blade.php`
- **`.turno-header`** / **`.setor-header`** → Cabeçalhos de Turno e Setor (estilização e alinhamento). Arquivo: `escalas-padrao/planilha.blade.php`
- **`.day-col`** → Largura padrão da coluna de dia (160px). Arquivo: `escalas-padrao/planilha.blade.php`

> Manutenção: ao alterar a paleta/estados da planilha, atualizar esta lista e a seção "🎨 Regras Visuais (Views)" em `docs/REGRAS_DE_NEGOCIO.md`.

### Framework
- **Bootstrap 5.3.0** → CDN usado em todas as views
- **Bootstrap Icons** → CDN usado para ícones

---

## ⚙️ MÉTODOS PRINCIPAIS

### DashboardController
- **`index()`** → Exibe dashboard principal
- **`calculateCurrentScore()`** → Calcula score atual do plantonista
- **`calculateThreeMonthScore()`** → Calcula score trimestral
- **`getUpcomingShifts()`** → Obtém próximos plantões
- **`getAvailableOffers()`** → Obtém ofertas disponíveis

### AlocacaoController
- **`index()`** → Lista todas as alocações
- **`create()`** → Exibe formulário de criação
- **`store()`** → Salva nova alocação
- **`edit()`** → Exibe formulário de edição
- **`update()`** → Atualiza alocação existente
- **`destroy()`** → Remove alocação

---

## 📝 REGRAS DE NOMENCLATURA

### Padrões Estabelecidos
1. **Controllers**: PascalCase + "Controller" (ex: `SetorController`)
2. **Models**: PascalCase singular (ex: `Plantonista`)
3. **Views**: snake_case (ex: `create.blade.php`)
4. **Rotas**: snake_case com resource naming (ex: `alocacoes.index`)
5. **CSS Classes**: kebab-case (ex: `.hero-section`)
6. **Métodos**: camelCase (ex: `calculateCurrentScore`)
7. **Variáveis**: camelCase (ex: `$upcomingShifts`)

---

*Última atualização: 2024-12-28*
*Total de nomenclaturas registradas: 47*