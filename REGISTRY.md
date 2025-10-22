# 📋 REGISTRY - FONTE CENTRAL DE VERDADE - EscalaMedica2

> **⚠️ ATENÇÃO: Este arquivo é a FONTE CENTRAL DE VERDADE. Deve ser consultado ANTES de qualquer implementação e atualizado APÓS qualquer alteração no sistema.**

> **🎯 FUNÇÃO**: Servir como índice central para localizar entidades, rotas, componentes, regras e nomenclaturas em seus respectivos documentos especializados.

## 📊 ESTATÍSTICAS DO SISTEMA
- **Última Atualização**: 2025-10-21 (Commit: 1bc44729)
- **Status Geral**: ✅ **100% FUNCIONAL**
- **Total de Funcionalidades**: 12 ✅ (incluindo Escala Padrão 5 Semanas)
- **Total de Entidades**: 11 ✅ (7 originais + 4 novos models Escala Padrão)
- **Total de Rotas**: 58 ✅ (+10 rotas de escala padrão com planilha)
- **Total de Controllers**: 9 ✅ (+1 EscalaPadraoController)
- **Total de Views**: 34 ✅ (+5 views de escala padrão: index, create, edit-dia, resumo, planilha)
- **Total de Componentes**: 32 ✅
- **Total de Documentos**: 16 ✅ (+2 SISTEMA_ESCALA_PADRAO.md, GUIA_USO_ESCALA_PADRAO.md)
- **Total de Nomenclaturas Registradas**: 47 ✅
- **Bugs Corrigidos Hoje**: 11 ✅
- **Versão Laravel**: 11.46.1
- **Versão PHP**: 8.2.12
- **Dashboard Status**: ✅ **IMPLEMENTADO**
- **Sistema Escala Padrão**: ✅ **IMPLEMENTADO** (Backend + Frontend + Planilha 5×7 Completo)

---

## 📂 GUIA DE DOCUMENTAÇÃO

### 🎯 Para cada necessidade, consulte:

| Necessidade | Documento | Descrição |
|------------|-----------|----------|
| **Arquitetura & Padrões** | `DOCUMENTACAO_TECNICA.md` | Estrutura técnica, tecnologias e padrões |
| **Mapa de Relacionamentos** | `MAPA_RELACIONAMENTOS.md` | 🔗 **CRÍTICO**: Todos os relacionamentos entre entidades e pontos de impacto |
| **Termos e Definições** | `GLOSSARIO_DE_DOMINIO.md` | Vocabulário médico e técnico padronizado |
| **Regras de Negócio** | `REGRAS_DE_NEGOCIO.md` | Validações e constraints funcionais |
| **Fluxos de Trabalho** | `FLUXOS_FUNCIONAIS.md` | Processos e workflows mapeados |
| **Estratégia de Testes** | `ESTRATEGIA_DE_TESTES.md` | Cobertura e metodologia de qualidade |
| **Plano de Ação** | `PLANO_DE_ACAO.md` | Roadmap e metodologia de implementação |
| **Comandos Essenciais** | `QUICK_REFERENCE.md` | Shortcuts e comandos frequentes |
| **Visão Geral** | `README.md` | Introdução e setup do projeto |
| **Progresso Atual** | `PROGRESSO_ATUAL.md` | ⚡ Tarefas em andamento (resetado a cada commit) |
| **Nomenclaturas** | `INDICE_NOMENCLATURAS.md` | ⚡ Registro de todas as classes, métodos e onde são usadas |
| **Bugs Corrigidos** | `BUGS_CORRIGIDOS.md` | ⚡ Histórico de correções e melhorias |
| **Histórico** | `HISTORICO_COMMITS.md` | Log de todas as alterações do sistema |
| **Sistema Escala Padrão** | `SISTEMA_ESCALA_PADRAO.md` | 📅 Arquitetura técnica da escala padrão de 5 semanas |
| **Guia de Uso Escala** | `GUIA_USO_ESCALA_PADRAO.md` | 📖 Tutorial completo de uso do sistema de escala padrão |
| **Checklist de Validação** | `CHECKLIST_VALIDACAO.md` | ✅ **CRÍTICO**: Checklist obrigatório antes de cada commit |

---

## 🎯 Índice de Navegação
- [🏗️ Funcionalidades](#-funcionalidades)
- [🗃️ Entidades e Modelos](#-entidades-e-modelos)
- [🛣️ Rotas](#-rotas)
- [🧩 Componentes](#-componentes)
- [⚙️ Serviços](#-serviços)
- [🗄️ Banco de Dados](#-banco-de-dados)
- [📋 Regras de Negócio](#-regras-de-negócio)
- [🔗 Dependências Externas](#-dependências-externas)

---

## 🏗️ Funcionalidades

### F001 - Sistema Base Laravel
- **Descrição**: Configuração inicial do sistema Laravel 11
- **Status**: ✅ Ativo
- **Responsável**: Sistema
- **Data de Criação**: 2025-10-20
- **Última Modificação**: 2025-10-20
- **Impacto**: Base para todo o sistema
- **Dependências**: Laravel Framework 11.46.1
- **Arquivos Relacionados**:
  - `bootstrap/app.php`
  - `config/*`
  - `public/index.php`

### F002 - Sistema de Documentação Técnica
- **Descrição**: Estrutura completa de documentação versionável e rastreável
- **Status**: ✅ Ativo
- **Responsável**: Sistema
- **Data de Criação**: 2025-10-20
- **Última Modificação**: 2025-10-20
- **Impacto**: Base para consistência e qualidade em todas as implementações futuras
- **Dependências**: Sistema Base Laravel
- **Arquivos Relacionados**:
  - `REGISTRY.md` (registro central)
  - `HISTORICO_COMMITS.md` (histórico de alterações)
  - `docs/DOCUMENTACAO_TECNICA.md` (arquitetura)
  - `docs/PLANO_DE_ACAO.md` (guia de implementação)
  - `docs/QUICK_REFERENCE.md` (comandos e referências)
  - `README.md` (atualizado com navegação)

### F003 - Sistema de Glossário de Domínio
- **Descrição**: Padronização de termos médicos e técnicos do sistema
- **Status**: ✅ Ativo
- **Responsável**: Sistema + Product Owner
- **Data de Criação**: 2025-10-20
- **Última Modificação**: 2025-10-20
- **Impacto**: Elimina ambiguidades e facilita comunicação entre stakeholders
- **Dependências**: Sistema de Documentação
- **Arquivos Relacionados**:
  - `docs/GLOSSARIO_DE_DOMINIO.md` (15 termos iniciais)

### F004 - Sistema de Regras de Negócio
- **Descrição**: Documentação completa de todas as regras funcionais e técnicas
- **Status**: ✅ Ativo
- **Responsável**: Sistema + Tech Lead + Direção Médica
- **Data de Criação**: 2025-10-20
- **Última Modificação**: 2025-10-20
- **Impacto**: Garantia de implementação correta das regras médicas e operacionais
- **Dependências**: Glossário de Domínio
- **Arquivos Relacionados**:
  - `docs/REGRAS_DE_NEGOCIO.md` (25 regras iniciais)

### F005 - Sistema de Fluxos Funcionais
- **Descrição**: Mapeamento de todos os processos e fluxos principais do sistema
- **Status**: ✅ Ativo
- **Responsável**: Sistema + Analistas de Processo
- **Data de Criação**: 2025-10-20
- **Última Modificação**: 2025-10-20
- **Impacto**: Facilita desenvolvimento, testes e auditoria de processos
- **Dependências**: Regras de Negócio, Glossário
- **Arquivos Relacionados**:
  - `docs/FLUXOS_FUNCIONAIS.md` (8 fluxos principais)

### F007 - Sistema de Escala Médica
- **Descrição**: Sistema completo para gestão de escalas médicas com controle de plantões e conflitos
- **Status**: ✅ Ativo
- **Responsável**: Sistema + Direção Médica
- **Data de Criação**: 2025-10-20
- **Última Modificação**: 2025-10-20
- **Impacto**: Core business do sistema - gestão completa de escalas médicas
- **Dependências**: Sistema Base Laravel
- **Arquivos Relacionados**:
  - `database/migrations/` (7 migrations)
  - `app/Models/` (7 models)
  - `app/Http/Controllers/` (3 controllers)
  - `app/Observers/AlocacaoObserver.php`
  - `app/Rules/SemSobreposicaoDeHorario.php`
  - `database/seeders/` (6 seeders)
  - `routes/web.php`

### F008 - Sistema de Views/Frontend Completo
- **Descrição**: Interface web completa para gestão de escalas médicas com CRUD funcional
- **Status**: ✅ Ativo
- **Responsável**: Sistema + UX/UI Designer
- **Data de Criação**: 2025-10-21
- **Última Modificação**: 2025-10-21
- **Impacto**: Interface completa para usuários finais operarem o sistema
- **Dependências**: Sistema de Escala Médica
- **Arquivos Relacionados**:
  - `resources/views/setores/` (4 views CRUD)
  - `resources/views/turnos/` (4 views CRUD)
  - `resources/views/alocacoes/` (4 views CRUD)
  - Bootstrap 5.3.0 (CDN)

### F009 - Sistema de Escala Padrão Rotativa (5 Semanas)
- **Descrição**: Template cíclico de 5 semanas que se repete automaticamente, baseado em melhores práticas hospitalares
- **Status**: ✅ Ativo (Implementação Backend + Frontend + Visualização Completa)
- **Responsável**: Sistema + Direção Médica
- **Data de Criação**: 2025-10-21
- **Última Modificação**: 2025-10-21 (Commit: 1bc44729)
- **Impacto**: Planejamento de longo prazo, distribuição justa de carga, previsibilidade para equipes
- **Dependências**: Sistema de Escala Médica, Unidades, Turnos, Setores
- **Arquivos Relacionados**:
  - `database/migrations/2025_10_21_200000_create_escala_padrao_tables.php`
  - `app/Models/EscalaPadrao.php`
  - `app/Models/SemanaTemplate.php`
  - `app/Models/DiaTemplate.php`
  - `app/Models/ConfiguracaoTurnoSetor.php`
  - `app/Http/Controllers/EscalaPadraoController.php`
  - `resources/views/escalas-padrao/index.blade.php` (visualização 5 semanas × 7 dias)
  - `resources/views/escalas-padrao/create.blade.php` (criação de escala)
  - `resources/views/escalas-padrao/edit-dia.blade.php` (configuração por dia)
  - `resources/views/escalas-padrao/resumo.blade.php` (cards resumo por unidade)
  - `resources/views/escalas-padrao/planilha.blade.php` (planilha 5×7 detalhada)
  - `resources/views/dashboard/index.blade.php` (link no menu lateral)
  - `routes/web.php` (10 rotas dedicadas)
  - `SISTEMA_ESCALA_PADRAO.md` (documentação técnica completa)
  - `GUIA_USO_ESCALA_PADRAO.md` (tutorial de uso)
- **Estrutura**:
  - Cada Unidade tem UMA escala padrão
  - Escala tem 5 semanas template que se repetem ciclicamente
  - Cada semana tem 7 dias configuráveis
  - Cada dia tem turnos + setores + quantidade de médicos
  - Sistema calcula automaticamente qual semana está vigente
- **Funcionalidades Implementadas**:
  - ✅ CRUD completo de escala padrão
  - ✅ Configuração granular por dia/turno/setor
  - ✅ Sistema de cópia entre dias/semanas
  - ✅ Cálculo automático de semana vigente
  - ✅ Visualização em tabs (5 semanas)
  - ✅ Resumo geral com métricas por unidade
  - ✅ Planilha detalhada 5×7 com cabeçalhos agrupados
  - ✅ Métricas: Total de Slots, Preenchidos, Buracos, Taxa
  - ✅ Navegação completa: Dashboard → Resumo → Planilha → Configuração

---

## 🗃️ Entidades e Modelos

### E001 - Plantonista
- **Descrição**: Médicos e profissionais de saúde que fazem plantões
- **Tabela**: `plantonistas`
- **Modelo**: `App\Models\Plantonista`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `hasMany(Alocacao::class)` - alocações do plantonista
- **Campos Principais**: nome, email, telefone, crm, especialidade, status
- **Arquivo**: `app/Models/Plantonista.php`

### E002 - Cidade
- **Descrição**: Cidades onde existem unidades médicas
- **Tabela**: `cidades`
- **Modelo**: `App\Models\Cidade`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `hasMany(Unidade::class)` - unidades da cidade
- **Campos Principais**: nome, uf, codigo_ibge, status
- **Arquivo**: `app/Models/Cidade.php`

### E003 - Unidade
- **Descrição**: Unidades médicas (hospitais, clínicas, postos de saúde)
- **Tabela**: `unidades`
- **Modelo**: `App\Models\Unidade`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `belongsTo(Cidade::class)` - cidade da unidade
  - `hasMany(Setor::class)` - setores da unidade
  - `hasMany(Vaga::class)` - vagas da unidade
- **Campos Principais**: nome, tipo, endereco, telefone, cidade_id, status
- **Arquivo**: `app/Models/Unidade.php`

### E004 - Setor
- **Descrição**: Setores médicos globais (UTI, Emergência, etc.) - NÃO vinculados a unidades específicas
- **Tabela**: `setores`
- **Modelo**: `App\Models\Setor`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `hasMany(Vaga::class)` - vagas que utilizam este setor (através de Unidade + Turno)
- **Campos Principais**: nome (unique), descricao, status
- **Arquivo**: `app/Models/Setor.php`
- **Observações**: Setores são GLOBAIS. A relação com unidades é feita através da tabela `vagas`

### E005 - Turno
- **Descrição**: Turnos de trabalho globais (manhã, tarde, noite, etc.) - NÃO vinculados a unidades específicas
- **Tabela**: `turnos`
- **Modelo**: `App\Models\Turno`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `hasMany(Vaga::class)` - vagas que utilizam este turno (através de Unidade + Setor)
- **Campos Principais**: nome (unique), hora_inicio, hora_fim, duracao_horas, periodo, status
- **Arquivo**: `app/Models/Turno.php`
- **Observações**: Turnos são GLOBAIS. A relação com unidades é feita através da tabela `vagas`

### E006 - Vaga
- **Descrição**: Configuração de vagas: define quais SETORES (globais) operam em quais TURNOS (globais) em cada UNIDADE, e quantos médicos são necessários
- **Tabela**: `vagas`
- **Modelo**: `App\Models\Vaga`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `belongsTo(Unidade::class)` - unidade da vaga
  - `belongsTo(Setor::class)` - setor (global) operando nesta vaga
  - `belongsTo(Turno::class)` - turno (global) desta vaga
  - `hasMany(Alocacao::class)` - alocações da vaga
- **Campos Principais**: unidade_id, setor_id, turno_id, quantidade_necessaria, observacoes, status
- **Arquivo**: `app/Models/Vaga.php`
- **Unique Key**: (unidade_id, setor_id, turno_id) - previne duplicatas
- **Observações**: Esta é a tabela CENTRAL que conecta Unidades com Setores e Turnos globais

### E007 - Alocacao
- **Descrição**: Alocações de plantonistas em vagas específicas
- **Tabela**: `alocacoes`
- **Modelo**: `App\Models\Alocacao`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `belongsTo(Plantonista::class)` - plantonista alocado
  - `belongsTo(Vaga::class)` - vaga ocupada
  - `belongsTo(Turno::class, 'turno_id', 'id')` - turno através da vaga
- **Campos Principais**: plantonista_id, vaga_id, data_plantao, data_hora_inicio, data_hora_fim, observacoes, status
- **Arquivo**: `app/Models/Alocacao.php`
- **Observer**: `AlocacaoObserver` para cálculo automático de datas/horas

### E008 - EscalaPadrao
- **Descrição**: Template mestre de escala rotativa de 5 semanas por unidade
- **Tabela**: `escalas_padrao`
- **Modelo**: `App\Models\EscalaPadrao`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `belongsTo(Unidade::class)` - unidade dona da escala
  - `hasMany(SemanaTemplate::class)` - 5 semanas template
- **Campos Principais**: unidade_id, nome, descricao, status, vigencia_inicio
- **Arquivo**: `app/Models/EscalaPadrao.php`
- **Unique Key**: (unidade_id) - cada unidade tem apenas UMA escala padrão ativa
- **Método Especial**: `getSemanaAtual()` - calcula qual semana (1-5) está vigente hoje

### E009 - SemanaTemplate
- **Descrição**: Uma das 5 semanas do ciclo rotativo
- **Tabela**: `semanas_template`
- **Modelo**: `App\Models\SemanaTemplate`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `belongsTo(EscalaPadrao::class)` - escala pai
  - `hasMany(DiaTemplate::class)` - 7 dias da semana
- **Campos Principais**: escala_padrao_id, numero_semana (1-5), nome, observacoes
- **Arquivo**: `app/Models/SemanaTemplate.php`

### E010 - DiaTemplate
- **Descrição**: Um dia da semana dentro de uma semana template
- **Tabela**: `dias_template`
- **Modelo**: `App\Models\DiaTemplate`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `belongsTo(SemanaTemplate::class)` - semana pai
  - `hasMany(ConfiguracaoTurnoSetor::class)` - configurações de turnos/setores
- **Campos Principais**: semana_template_id, dia_semana (segunda-domingo), observacoes
- **Arquivo**: `app/Models/DiaTemplate.php`

### E011 - ConfiguracaoTurnoSetor
- **Descrição**: Configuração final: Turno + Setor + Quantidade de médicos necessários
- **Tabela**: `configuracoes_turno_setor`
- **Modelo**: `App\Models\ConfiguracaoTurnoSetor`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `belongsTo(DiaTemplate::class)` - dia pai
  - `belongsTo(Turno::class)` - turno configurado
  - `belongsTo(Setor::class)` - setor configurado
- **Campos Principais**: dia_template_id, turno_id, setor_id, quantidade_necessaria, observacoes, status
- **Arquivo**: `app/Models/ConfiguracaoTurnoSetor.php`
- **Unique Key**: (dia_template_id, turno_id, setor_id) - previne duplicatas

### Convenções de Nomenclatura:
- **Modelos**: PascalCase singular (ex: `Plantonista`, `Alocacao`)
- **Tabelas**: snake_case plural (ex: `plantonistas`, `alocacoes`)
- **Campos**: snake_case (ex: `data_plantao`, `plantonista_id`)

---

## 🛣️ Rotas

### R001 - Página Inicial
- **Rota**: `GET /`
- **Nome**: `welcome`
- **Controller**: Closure (função anônima)
- **Middleware**: `web`
- **Descrição**: Página de boas-vindas do Laravel
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php:9`
- **Retorno**: View `welcome`

### R002 - Dashboard
- **Rota**: `GET /dashboard`
- **Nome**: `dashboard`
- **Controller**: `DashboardController@index`
- **Middleware**: `web`
- **Descrição**: Dashboard principal com estatísticas
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php:14`

### R003-R009 - Rotas de Plantonistas
- **Rotas**: Resource `plantonistas`
- **Controller**: `PlantonisταController`
- **Middleware**: `web`
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php:17`
- **Rotas Incluídas**:
  - `GET /plantonistas` - `plantonistas.index`
  - `GET /plantonistas/create` - `plantonistas.create`
  - `POST /plantonistas` - `plantonistas.store`
  - `GET /plantonistas/{plantonista}` - `plantonistas.show`
  - `GET /plantonistas/{plantonista}/edit` - `plantonistas.edit`
  - `PUT/PATCH /plantonistas/{plantonista}` - `plantonistas.update`
  - `DELETE /plantonistas/{plantonista}` - `plantonistas.destroy`

### R010-R016 - Rotas de Cidades
- **Rotas**: Resource `cidades`
- **Controller**: `CidadeController`
- **Middleware**: `web`
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php:20`
- **Rotas Incluídas**:
  - `GET /cidades` - `cidades.index`
  - `GET /cidades/create` - `cidades.create`
  - `POST /cidades` - `cidades.store`
  - `GET /cidades/{cidade}` - `cidades.show`
  - `GET /cidades/{cidade}/edit` - `cidades.edit`
  - `PUT/PATCH /cidades/{cidade}` - `cidades.update`
  - `DELETE /cidades/{cidade}` - `cidades.destroy`

### R017-R023 - Rotas de Unidades
- **Rotas**: Resource `unidades`
- **Controller**: `UnidadeController`
- **Middleware**: `web`
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php:23`
- **Rotas Incluídas**:
  - `GET /unidades` - `unidades.index`
  - `GET /unidades/create` - `unidades.create`
  - `POST /unidades` - `unidades.store`
  - `GET /unidades/{unidade}` - `unidades.show`
  - `GET /unidades/{unidade}/edit` - `unidades.edit`
  - `PUT/PATCH /unidades/{unidade}` - `unidades.update`
  - `DELETE /unidades/{unidade}` - `unidades.destroy`

### R024-R030 - Rotas de Setores
- **Rotas**: Resource `setores`
- **Controller**: `SetorController`
- **Middleware**: `web`
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php:26`
- **Rotas Incluídas**:
  - `GET /setores` - `setores.index`
  - `GET /setores/create` - `setores.create`
  - `POST /setores` - `setores.store`
  - `GET /setores/{setor}` - `setores.show`
  - `GET /setores/{setor}/edit` - `setores.edit`
  - `PUT/PATCH /setores/{setor}` - `setores.update`
  - `DELETE /setores/{setor}` - `setores.destroy`

### R031-R037 - Rotas de Turnos
- **Rotas**: Resource `turnos`
- **Controller**: `TurnoController`
- **Middleware**: `web`
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php:29`
- **Rotas Incluídas**:
  - `GET /turnos` - `turnos.index`
  - `GET /turnos/create` - `turnos.create`
  - `POST /turnos` - `turnos.store`
  - `GET /turnos/{turno}` - `turnos.show`
  - `GET /turnos/{turno}/edit` - `turnos.edit`
  - `PUT/PATCH /turnos/{turno}` - `turnos.update`
  - `DELETE /turnos/{turno}` - `turnos.destroy`

### R038-R044 - Rotas de Alocações
- **Rotas**: Resource `alocacoes`
- **Controller**: `AlocacaoController`
- **Middleware**: `web`
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php:32`
- **Rotas Incluídas**:
  - `GET /alocacoes` - `alocacoes.index`
  - `GET /alocacoes/create` - `alocacoes.create`
  - `POST /alocacoes` - `alocacoes.store`
  - `GET /alocacoes/{alocacao}` - `alocacoes.show`
  - `GET /alocacoes/{alocacao}/edit` - `alocacoes.edit`
  - `PUT/PATCH /alocacoes/{alocacao}` - `alocacoes.update`
  - `DELETE /alocacoes/{alocacao}` - `alocacoes.destroy`

### R045-R054 - Rotas de Escala Padrão
- **Controller**: `EscalaPadraoController`
- **Middleware**: `web`
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php`
- **Rotas Incluídas**:
  - `GET /schedule-patterns` - `schedule-patterns` (resumoGeral) - Cards resumo todas unidades
  - `GET /schedule-patterns/{unidade}/schedule` - `schedule-patterns.schedule` (planilha) - Planilha 5×7 detalhada
  - `GET /unidades/{unidade}/escala-padrao` - `escalas-padrao.index` - Visualização 5 semanas em tabs
  - `GET /unidades/{unidade}/escala-padrao/create` - `escalas-padrao.create` - Formulário criação
  - `POST /unidades/{unidade}/escala-padrao` - `escalas-padrao.store` - Criação com estrutura 5×7
  - `GET /unidades/{unidade}/escala-padrao/{semana}/{dia}/edit` - `escalas-padrao.edit-dia` - Configuração por dia
  - `POST /unidades/{unidade}/escala-padrao/{semana}/{dia}` - `escalas-padrao.store-configuracao` - Adicionar config
  - `DELETE /unidades/{unidade}/escala-padrao/config/{config}` - `escalas-padrao.destroy-configuracao` - Remover config
  - `POST /unidades/{unidade}/escala-padrao/{semana}/{dia}/copiar` - `escalas-padrao.copiar-dia` - Copiar entre dias
  - `GET /dashboard` - Link "Padrões de Escala" no menu lateral

### Convenções de Nomenclatura de Rotas:
- **Recurso simples**: `resource.action` (ex: `setor.show`)
- **Recurso aninhado**: `parent.child.action` (ex: `unidade.setor.edit`)
- **API**: prefixo `api.` (ex: `api.alocacao.index`)

---

## 🧩 Componentes

### C001 - DashboardController
- **Descrição**: Controller para dashboard principal com estatísticas
- **Tipo**: Single Action Controller
- **Status**: ✅ Ativo
- **Arquivo**: `app/Http/Controllers/DashboardController.php`
- **Métodos**: index
- **Funcionalidades**: Estatísticas gerais do sistema
- **Transações**: Não

### C002 - PlantonisταController
- **Descrição**: Controller para gestão completa de plantonistas
- **Tipo**: Resource Controller
- **Status**: ✅ Ativo
- **Arquivo**: `app/Http/Controllers/PlantonisταController.php`
- **Métodos**: index, create, store, show, edit, update, destroy
- **Validações**: CRM único, email único, campos obrigatórios
- **Transações**: Sim (store/update/destroy)
- **Proteções**: Impede exclusão se existem alocações

### C003 - CidadeController
- **Descrição**: Controller para gestão completa de cidades
- **Tipo**: Resource Controller
- **Status**: ✅ Ativo
- **Arquivo**: `app/Http/Controllers/CidadeController.php`
- **Métodos**: index, create, store, show, edit, update, destroy
- **Validações**: nome único
- **Transações**: Sim (store/update/destroy)
- **Proteções**: Impede exclusão se existem unidades

### C004 - UnidadeController
- **Descrição**: Controller para gestão completa de unidades
- **Tipo**: Resource Controller
- **Status**: ✅ Ativo
- **Arquivo**: `app/Http/Controllers/UnidadeController.php`
- **Métodos**: index, create, store, show, edit, update, destroy
- **Validações**: cidade existente, campos obrigatórios
- **Transações**: Sim (store/update/destroy)
- **Proteções**: Impede exclusão se existem vagas

### C005 - SetorController
- **Descrição**: Controller para gestão completa de setores
- **Tipo**: Resource Controller
- **Status**: ✅ Ativo
- **Arquivo**: `app/Http/Controllers/SetorController.php`
- **Métodos**: index, create, store, show, edit, update, destroy
- **Validações**: nome único, unidade existente
- **Transações**: Sim (store/update/destroy)
- **Proteções**: Impede exclusão se existem vagas

### C006 - TurnoController
- **Descrição**: Controller para gestão completa de turnos
- **Tipo**: Resource Controller
- **Status**: ✅ Ativo
- **Arquivo**: `app/Http/Controllers/TurnoController.php`
- **Métodos**: index, create, store, show, edit, update, destroy
- **Validações**: nome único, formato hora válido, cálculo duração
- **Transações**: Sim (store/update/destroy)

### C007 - AlocacaoController
- **Descrição**: Controller para gestão completa de alocações
- **Tipo**: Resource Controller
- **Status**: ✅ Ativo
- **Arquivo**: `app/Http/Controllers/AlocacaoController.php`
- **Métodos**: index, create, store, show, edit, update, destroy
- **Validações**: SemSobreposicaoDeHorario, entidades válidas
- **Transações**: Sim (store/update/destroy)

### C008 - AlocacaoObserver
- **Descrição**: Observer para cálculo automático de datas/horas das alocações
- **Tipo**: Model Observer
- **Status**: ✅ Ativo
- **Arquivo**: `app/Observers/AlocacaoObserver.php`
- **Eventos**: creating, updating
- **Funcionalidades**: Cálculo automático data_hora_inicio/fim, suporte turnos "Corujão"

### C009 - SemSobreposicaoDeHorario
- **Descrição**: Regra de validação para prevenir conflitos de horários
- **Tipo**: Custom Validation Rule
- **Status**: ✅ Ativo
- **Arquivo**: `app/Rules/SemSobreposicaoDeHorario.php`
- **Funcionalidades**: Detecção sobreposição horários, validação cruzada plantonistas

### C010 - DatabaseSeeder
- **Descrição**: Seeder principal que executa todos os seeders do sistema
- **Tipo**: Database Seeder
- **Status**: ✅ Ativo
- **Arquivo**: `database/seeders/DatabaseSeeder.php`
- **Dependências**: Todos os seeders específicos

### C011 - PlantonistasSeeder + CidadesSeeder + UnidadesSeeder
- **Descrição**: Seeders para dados base do sistema
- **Tipo**: Model Seeders
- **Status**: ✅ Ativo
- **Arquivos**: `database/seeders/[Entity]Seeder.php`
- **Registros**: 50+ plantonistas, 10+ cidades, 15+ unidades

### C012 - SetoresSeeder + TurnosSeeder + VagasSeeder
- **Descrição**: Seeders para estrutura operacional
- **Tipo**: Model Seeders
- **Status**: ✅ Ativo
- **Arquivos**: `database/seeders/[Entity]Seeder.php`
- **Registros**: 30+ setores, 6 turnos padrão, 100+ vagas

### C013 - Views do Sistema de Setores
- **Descrição**: Interface completa CRUD para gestão de setores
- **Tipo**: Blade Templates
- **Status**: ✅ Ativo
- **Arquivos**: `resources/views/setores/` (index, create, show, edit)
- **Funcionalidades**: Listagem, criação, visualização, edição com validação
- **Design**: Bootstrap 5.3.0 responsivo

### C014 - Views do Sistema de Turnos
- **Descrição**: Interface completa CRUD para gestão de turnos
- **Tipo**: Blade Templates
- **Status**: ✅ Ativo
- **Arquivos**: `resources/views/turnos/` (index, create, show, edit)
- **Funcionalidades**: Gestão de horários, períodos, cálculo de duração
- **Design**: Bootstrap 5.3.0 responsivo

### C015 - Views do Sistema de Alocações
- **Descrição**: Interface completa CRUD para gestão de alocações
- **Tipo**: Blade Templates
- **Status**: ✅ Ativo
- **Arquivos**: `resources/views/alocacoes/` (index, create, show, edit)
- **Funcionalidades**: Gestão de plantões, prevenção conflitos, relacionamentos
- **Design**: Bootstrap 5.3.0 responsivo

### C016 - Sistema de Layout Responsivo
- **Descrição**: Design system baseado em Bootstrap 5.3.0
- **Tipo**: Frontend Framework
- **Status**: ✅ Ativo
- **Funcionalidades**: Responsividade, alertas, formulários, navegação
- **Componentes**: Cards, tables, buttons, badges, modals
- **Acessibilidade**: Mobile-first, screen readers compatível

### C017 - Views do Sistema de Plantonistas
- **Descrição**: Interface completa CRUD para gestão de plantonistas
- **Tipo**: Blade Templates
- **Status**: ✅ Ativo
- **Arquivos**: `resources/views/plantonistas/` (index, create, show, edit)
- **Funcionalidades**: Cadastro completo, validação CRM/email, listagem de alocações
- **Design**: Bootstrap 5.3.0 responsivo

### C018 - Views do Sistema de Cidades
- **Descrição**: Interface completa CRUD para gestão de cidades
- **Tipo**: Blade Templates
- **Status**: ✅ Ativo
- **Arquivos**: `resources/views/cidades/` (index, create, show, edit)
- **Funcionalidades**: Cadastro simples, listagem de unidades por cidade
- **Design**: Bootstrap 5.3.0 responsivo

### C019 - Views do Sistema de Unidades
- **Descrição**: Interface completa CRUD para gestão de unidades
- **Tipo**: Blade Templates
- **Status**: ✅ Ativo
- **Arquivos**: `resources/views/unidades/` (index, create, show, edit)
- **Funcionalidades**: Cadastro com cidade, endereço, listagem de vagas
- **Design**: Bootstrap 5.3.0 responsivo

### C020 - EscalaPadraoController
- **Descrição**: Controller para gestão completa do sistema de escala padrão 5 semanas
- **Tipo**: Resource Controller + Custom Actions
- **Status**: ✅ Ativo
- **Arquivo**: `app/Http/Controllers/EscalaPadraoController.php`
- **Métodos**: 
  - `resumoGeral()` - Cards resumo de todas as unidades com métricas
  - `planilha($unidade)` - Planilha 5×7 detalhada por unidade
  - `index($unidade)` - Visualização principal com 5 semanas em tabs
  - `create($unidade)` - Formulário criação de escala
  - `store($unidade)` - Criação com estrutura 5×7 automática
  - `editDia($unidade, $semana, $dia)` - Configuração por dia
  - `storeConfiguracao()` - Adicionar config Turno+Setor+Qty
  - `destroyConfiguracao()` - Remover configuração
  - `copiarDia()` - Copiar configs entre dias/semanas
- **Validações**: 
  - Uma escala ativa por unidade
  - Quantidade médicos entre 1-50
  - Combinação Turno+Setor única por dia
- **Transações**: Sim (store/destroy)
- **Métricas Calculadas**:
  - Total de Slots = Σ(ConfiguracaoTurnoSetor.quantidade_necessaria)
  - Preenchidos = 0 (futuro: alocações)
  - Buracos = Total - Preenchidos
  - Taxa = (Preenchidos / Total) × 100%

### C021 - Views do Sistema de Escala Padrão
- **Descrição**: Interface completa para gestão de escala padrão 5 semanas
- **Tipo**: Blade Templates
- **Status**: ✅ Ativo
- **Arquivos**: 
  - `resources/views/escalas-padrao/resumo.blade.php` - Cards resumo todas unidades
  - `resources/views/escalas-padrao/planilha.blade.php` - Planilha 5×7 detalhada
  - `resources/views/escalas-padrao/index.blade.php` - Visualização 5 semanas em tabs
  - `resources/views/escalas-padrao/create.blade.php` - Criação de escala
  - `resources/views/escalas-padrao/edit-dia.blade.php` - Configuração granular por dia
- **Funcionalidades**: 
  - Navegação completa entre views
  - Sistema de tabs para semanas
  - Cards responsivos com métricas
  - Planilha com cabeçalhos agrupados (Turno > Setor)
  - Formulários com validação inline
  - Modal de cópia entre dias/semanas
  - Botão "Atribuição Rápida" (placeholder)
- **Design**: Bootstrap 5.3.0 + Bootstrap Icons responsivo
- **Métricas Visuais**: Badges coloridos, progress bars, alertas contextuais

### Convenções de Nomenclatura:
- **Controllers**: PascalCase + Controller (ex: `SetorController`)
- **Observers**: PascalCase + Observer (ex: `AlocacaoObserver`)
- **Rules**: PascalCase descritivo (ex: `SemSobreposicaoDeHorario`)
- **Seeders**: PascalCase plural + Seeder (ex: `PlantonistasSeeder`)

---

## ⚙️ Serviços

> 📝 **Nenhum serviço personalizado criado ainda.**

### Convenções de Nomenclatura:
- **Services**: PascalCase + "Service" (ex: `UserService`, `PaymentService`)
- **Repositories**: PascalCase + "Repository" (ex: `UserRepository`)
- **Jobs**: PascalCase + "Job" (ex: `SendEmailJob`)

---

## 🗄️ Banco de Dados

### Configuração Atual
- **Driver**: SQLite
- **Arquivo**: `database/database.sqlite` (será criado)
- **Migrações Executadas**: Nenhuma

### Convenções de Nomenclatura:
- **Migrações**: `yyyy_mm_dd_hhmmss_action_table_name.php`
- **Seeders**: `TableNameSeeder.php`
- **Factories**: `ModelNameFactory.php`

---

## 📋 Regras de Negócio

> 📝 **Nenhuma regra de negócio específica definida ainda.**

### Convenções de Documentação:
- **Identificador**: RN### (ex: RN001, RN002)
- **Prioridade**: Alta/Média/Baixa
- **Status**: Ativa/Inativa/Pendente

---

## 🔗 Dependências Externas

### Principais Dependências (composer.json)
- **laravel/framework**: ^11.0
- **laravel/tinker**: ^2.10
- **laravel/sail**: ^1.46

### Dependências de Desenvolvimento
- **fakerphp/faker**: ^1.24
- **laravel/pint**: ^1.25
- **nunomaduro/collision**: ^8.5
- **phpunit/phpunit**: ^10.5
- **spatie/laravel-ignition**: ^2.9

---

## 📝 Notas de Atualização

### Como Atualizar Este Registry:
1. **Antes de implementar**: Consulte este arquivo para verificar nomenclaturas e dependências
2. **Durante a implementação**: Use as convenções estabelecidas
3. **Após implementar**: Atualize as seções relevantes com as novas informações
4. **Commit**: Inclua as alterações deste arquivo no commit

### ⚠️ REGRA CRÍTICA - COMMITS:
**SEMPRE PERGUNTAR AO USUÁRIO ANTES DE COMMITAR**
- ❌ NÃO commitar automaticamente após implementações
- ❌ NÃO fazer push sem autorização explícita
- ✅ IMPLEMENTAR as mudanças solicitadas
- ✅ TESTAR e VALIDAR o código
- ✅ PERGUNTAR: "Posso commitar as alterações agora?"
- ✅ AGUARDAR confirmação do usuário antes de `git commit`
- ✅ Só fazer `git push` após aprovação do commit

### Template para Novas Entradas:
```markdown
### TIPO### - Nome da Funcionalidade
- **Descrição**: Breve descrição
- **Status**: ✅ Ativo | 🚧 Em Desenvolvimento | ❌ Inativo
- **Responsável**: Nome do desenvolvedor
- **Data de Criação**: YYYY-MM-DD
- **Última Modificação**: YYYY-MM-DD
- **Impacto**: Descrição do impacto no sistema
- **Dependências**: Lista de dependências
- **Arquivos Relacionados**: Lista de arquivos
```

---

**📍 Última verificação de integridade**: 2025-10-20  
**🔄 Próxima revisão programada**: A cada nova implementação
