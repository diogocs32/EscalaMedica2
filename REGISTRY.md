# 📋 REGISTRY - FONTE CENTRAL DE VERDADE - EscalaMedica2

> **⚠️ ATENÇÃO: Este arquivo é a FONTE CENTRAL DE VERDADE. Deve ser consultado ANTES de qualquer implementação e atualizado APÓS qualquer alteração no sistema.**

> **🎯 FUNÇÃO**: Servir como índice central para localizar entidades, rotas, componentes, regras e nomenclaturas em seus respectivos documentos especializados.

## 📊 ESTATÍSTICAS DO SISTEMA
- **Última Atualização**: 2025-10-21 
- **Status Geral**: ✅ **100% FUNCIONAL**
- **Total de Funcionalidades**: 9 ✅
- **Total de Entidades**: 7
- **Total de Rotas**: 20 ✅
- **Total de Controllers**: 4 ✅
- **Total de Views**: 13 ✅
- **Total de Componentes**: 24 ✅
- **Total de Documentos**: 12 ✅
- **Total de Nomenclaturas Registradas**: 47 ✅
- **Bugs Corrigidos Hoje**: 4 ✅
- **Versão Laravel**: 11.46.1
- **Versão PHP**: 8.2.12
- **Dashboard Status**: ✅ **IMPLEMENTADO**

---

## 📂 GUIA DE DOCUMENTAÇÃO

### 🎯 Para cada necessidade, consulte:

| Necessidade | Documento | Descrição |
|------------|-----------|----------|
| **Arquitetura & Padrões** | `DOCUMENTACAO_TECNICA.md` | Estrutura técnica, tecnologias e padrões |
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
- **Descrição**: Setores dentro das unidades (UTI, Emergência, etc.)
- **Tabela**: `setores`
- **Modelo**: `App\Models\Setor`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `belongsTo(Unidade::class)` - unidade do setor
  - `hasMany(Vaga::class)` - vagas do setor
- **Campos Principais**: nome, descricao, unidade_id, status
- **Arquivo**: `app/Models/Setor.php`

### E005 - Turno
- **Descrição**: Turnos de trabalho (manhã, tarde, noite, etc.)
- **Tabela**: `turnos`
- **Modelo**: `App\Models\Turno`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `hasMany(Vaga::class)` - vagas do turno
- **Campos Principais**: nome, hora_inicio, hora_fim, duracao_horas, periodo, status
- **Arquivo**: `app/Models/Turno.php`

### E006 - Vaga
- **Descrição**: Vagas de plantão disponíveis
- **Tabela**: `vagas`
- **Modelo**: `App\Models\Vaga`
- **Status**: ✅ Ativo
- **Relacionamentos**:
  - `belongsTo(Unidade::class)` - unidade da vaga
  - `belongsTo(Setor::class)` - setor da vaga
  - `belongsTo(Turno::class)` - turno da vaga
  - `hasMany(Alocacao::class)` - alocações da vaga
- **Campos Principais**: unidade_id, setor_id, turno_id, descricao, observacoes, status
- **Arquivo**: `app/Models/Vaga.php`

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
- **Arquivo**: `routes/web.php:6`
- **Retorno**: View `welcome`

### R002-R007 - Rotas de Setores
- **Rotas**: Resource `setores`
- **Controller**: `SetorController`
- **Middleware**: `web`
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php:11`
- **Rotas Incluídas**:
  - `GET /setores` - `setores.index`
  - `GET /setores/create` - `setores.create`
  - `POST /setores` - `setores.store`
  - `GET /setores/{setor}` - `setores.show`
  - `GET /setores/{setor}/edit` - `setores.edit`
  - `PUT/PATCH /setores/{setor}` - `setores.update`
  - `DELETE /setores/{setor}` - `setores.destroy`

### R008-R013 - Rotas de Turnos
- **Rotas**: Resource `turnos`
- **Controller**: `TurnoController`
- **Middleware**: `web`
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php:14`
- **Rotas Incluídas**:
  - `GET /turnos` - `turnos.index`
  - `GET /turnos/create` - `turnos.create`
  - `POST /turnos` - `turnos.store`
  - `GET /turnos/{turno}` - `turnos.show`
  - `GET /turnos/{turno}/edit` - `turnos.edit`
  - `PUT/PATCH /turnos/{turno}` - `turnos.update`
  - `DELETE /turnos/{turno}` - `turnos.destroy`

### R014-R019 - Rotas de Alocações
- **Rotas**: Resource `alocacoes`
- **Controller**: `AlocacaoController`
- **Middleware**: `web`
- **Status**: ✅ Ativo
- **Arquivo**: `routes/web.php:17`
- **Rotas Incluídas**:
  - `GET /alocacoes` - `alocacoes.index`
  - `GET /alocacoes/create` - `alocacoes.create`
  - `POST /alocacoes` - `alocacoes.store`
  - `GET /alocacoes/{alocacao}` - `alocacoes.show`
  - `GET /alocacoes/{alocacao}/edit` - `alocacoes.edit`
  - `PUT/PATCH /alocacoes/{alocacao}` - `alocacoes.update`
  - `DELETE /alocacoes/{alocacao}` - `alocacoes.destroy`

### Convenções de Nomenclatura de Rotas:
- **Recurso simples**: `resource.action` (ex: `setor.show`)
- **Recurso aninhado**: `parent.child.action` (ex: `unidade.setor.edit`)
- **API**: prefixo `api.` (ex: `api.alocacao.index`)

---

## 🧩 Componentes

### C001 - SetorController
- **Descrição**: Controller para gestão completa de setores
- **Tipo**: Resource Controller
- **Status**: ✅ Ativo
- **Arquivo**: `app/Http/Controllers/SetorController.php`
- **Métodos**: index, create, store, show, edit, update, destroy
- **Validações**: nome único, unidade existente
- **Transações**: Sim (store/update/destroy)

### C002 - TurnoController
- **Descrição**: Controller para gestão completa de turnos
- **Tipo**: Resource Controller
- **Status**: ✅ Ativo
- **Arquivo**: `app/Http/Controllers/TurnoController.php`
- **Métodos**: index, create, store, show, edit, update, destroy
- **Validações**: nome único, formato hora válido, cálculo duração
- **Transações**: Sim (store/update/destroy)

### C003 - AlocacaoController
- **Descrição**: Controller para gestão completa de alocações
- **Tipo**: Resource Controller
- **Status**: ✅ Ativo
- **Arquivo**: `app/Http/Controllers/AlocacaoController.php`
- **Métodos**: index, create, store, show, edit, update, destroy
- **Validações**: SemSobreposicaoDeHorario, entidades válidas
- **Transações**: Sim (store/update/destroy)

### C004 - AlocacaoObserver
- **Descrição**: Observer para cálculo automático de datas/horas das alocações
- **Tipo**: Model Observer
- **Status**: ✅ Ativo
- **Arquivo**: `app/Observers/AlocacaoObserver.php`
- **Eventos**: creating, updating
- **Funcionalidades**: Cálculo automático data_hora_inicio/fim, suporte turnos "Corujão"

### C005 - SemSobreposicaoDeHorario
- **Descrição**: Regra de validação para prevenir conflitos de horários
- **Tipo**: Custom Validation Rule
- **Status**: ✅ Ativo
- **Arquivo**: `app/Rules/SemSobreposicaoDeHorario.php`
- **Funcionalidades**: Detecção sobreposição horários, validação cruzada plantonistas

### C006 - DatabaseSeeder
- **Descrição**: Seeder principal que executa todos os seeders do sistema
- **Tipo**: Database Seeder
- **Status**: ✅ Ativo
- **Arquivo**: `database/seeders/DatabaseSeeder.php`
- **Dependências**: Todos os seeders específicos

### C007 - PlantonistasSeeder + CidadesSeeder + UnidadesSeeder
- **Descrição**: Seeders para dados base do sistema
- **Tipo**: Model Seeders
- **Status**: ✅ Ativo
- **Arquivos**: `database/seeders/[Entity]Seeder.php`
- **Registros**: 50+ plantonistas, 10+ cidades, 15+ unidades

### C008 - SetoresSeeder + TurnosSeeder + VagasSeeder
- **Descrição**: Seeders para estrutura operacional
- **Tipo**: Model Seeders
- **Status**: ✅ Ativo
- **Arquivos**: `database/seeders/[Entity]Seeder.php`
- **Registros**: 30+ setores, 6 turnos padrão, 100+ vagas

### C009 - Views do Sistema de Setores
- **Descrição**: Interface completa CRUD para gestão de setores
- **Tipo**: Blade Templates
- **Status**: ✅ Ativo
- **Arquivos**: `resources/views/setores/` (index, create, show, edit)
- **Funcionalidades**: Listagem, criação, visualização, edição com validação
- **Design**: Bootstrap 5.3.0 responsivo

### C010 - Views do Sistema de Turnos
- **Descrição**: Interface completa CRUD para gestão de turnos
- **Tipo**: Blade Templates
- **Status**: ✅ Ativo
- **Arquivos**: `resources/views/turnos/` (index, create, show, edit)
- **Funcionalidades**: Gestão de horários, períodos, cálculo de duração
- **Design**: Bootstrap 5.3.0 responsivo

### C011 - Views do Sistema de Alocações
- **Descrição**: Interface completa CRUD para gestão de alocações
- **Tipo**: Blade Templates
- **Status**: ✅ Ativo
- **Arquivos**: `resources/views/alocacoes/` (index, create, show, edit)
- **Funcionalidades**: Gestão de plantões, prevenção conflitos, relacionamentos
- **Design**: Bootstrap 5.3.0 responsivo

### C012 - Sistema de Layout Responsivo
- **Descrição**: Design system baseado em Bootstrap 5.3.0
- **Tipo**: Frontend Framework
- **Status**: ✅ Ativo
- **Funcionalidades**: Responsividade, alertas, formulários, navegação
- **Componentes**: Cards, tables, buttons, badges, modals
- **Acessibilidade**: Mobile-first, screen readers compatível

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
