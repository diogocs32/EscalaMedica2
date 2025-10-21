# 📋 REGISTRY - Registro Central do Sistema

> **⚠️ ATENÇÃO: Este arquivo deve ser consultado ANTES de qualquer implementação e atualizado APÓS qualquer alteração no sistema.**

## 📊 Estatísticas do Sistema
- **Última Atualização**: 2025-10-20 
- **Total de Funcionalidades**: 2
- **Total de Entidades**: 0
- **Total de Rotas**: 1
- **Total de Componentes**: 0
- **Total de Documentos**: 9
- **Total de Termos no Glossário**: 15
- **Total de Regras de Negócio**: 25
- **Total de Fluxos Mapeados**: 8
- **Cobertura de Testes Meta**: 80%
- **Versão Laravel**: 11.46.1
- **Versão PHP**: 8.2.12

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

### F006 - Sistema de Estratégia de Testes
- **Descrição**: Framework completo para garantia de qualidade e testes automatizados
- **Status**: ✅ Ativo
- **Responsável**: Sistema + Tech Lead + QA Team
- **Data de Criação**: 2025-10-20
- **Última Modificação**: 2025-10-20
- **Impacto**: Assegura qualidade, segurança e confiabilidade do sistema médico
- **Dependências**: Todos os sistemas anteriores
- **Arquivos Relacionados**:
  - `docs/ESTRATEGIA_DE_TESTES.md` (cobertura 80%+)

---

## 🗃️ Entidades e Modelos

> 📝 **Nenhuma entidade personalizada criada ainda.**

### Convenções de Nomenclatura:
- **Modelos**: PascalCase singular (ex: `User`, `Product`)
- **Tabelas**: snake_case plural (ex: `users`, `products`)
- **Campos**: snake_case (ex: `created_at`, `user_name`)

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

### Convenções de Nomenclatura de Rotas:
- **Recurso simples**: `resource.action` (ex: `user.show`)
- **Recurso aninhado**: `parent.child.action` (ex: `user.profile.edit`)
- **API**: prefixo `api.` (ex: `api.user.index`)

---

## 🧩 Componentes

> 📝 **Nenhum componente personalizado criado ainda.**

### Convenções de Nomenclatura:
- **Componentes Blade**: kebab-case (ex: `user-card`, `navigation-menu`)
- **Componentes Vue/React**: PascalCase (ex: `UserCard`, `NavigationMenu`)

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
