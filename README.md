# 🏥 EscalaMedica2 - Sistema de Gestão Médica

> **Sistema de gestão médica desenvolvido em Laravel 11 com foco em escalas, agendamentos e gestão hospitalar.**

[![Laravel](https://img.shields.io/badge/Laravel-11.46.1-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2.12-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 📋 Navegação da Documentação

### 🚨 **LEITURA OBRIGATÓRIA ANTES DE QUALQUER IMPLEMENTAÇÃO**

1. **[📋 REGISTRY.md](REGISTRY.md)** - **CONSULTE SEMPRE PRIMEIRO!**
   - Registro central de todas as funcionalidades, entidades, rotas e componentes
   - Fonte única de verdade para nomenclaturas e dependências
   - **⚠️ OBRIGATÓRIO consultar antes de implementar qualquer funcionalidade**

2. **[📅 HISTORICO_COMMITS.md](HISTORICO_COMMITS.md)**
   - Histórico detalhado de todas as alterações do sistema
   - Contexto e motivação das decisões tomadas
   - Rastreabilidade completa das implementações

3. **[🏗️ docs/DOCUMENTACAO_TECNICA.md](docs/DOCUMENTACAO_TECNICA.md)**
   - Arquitetura completa do sistema
   - Padrões de desenvolvimento e estrutura de pastas
   - Relacionamentos entre módulos e componentes

4. **[� docs/GLOSSARIO_DE_DOMINIO.md](docs/GLOSSARIO_DE_DOMINIO.md)**
   - Padronização de termos médicos e técnicos
   - Definições funcionais e técnicas com exemplos
   - Elimina ambiguidades na comunicação

5. **[�📋 docs/REGRAS_DE_NEGOCIO.md](docs/REGRAS_DE_NEGOCIO.md)**
   - Todas as regras de negócio implementadas ou planejadas
   - Justificativas e contexto das decisões
   - Validações, cálculos e restrições do sistema

6. **[🔄 docs/FLUXOS_FUNCIONAIS.md](docs/FLUXOS_FUNCIONAIS.md)**
   - Fluxos principais do sistema com diagramas
   - Sequência de ações e decisões
   - Facilita testes e auditoria de processos

7. **[🧪 docs/ESTRATEGIA_DE_TESTES.md](docs/ESTRATEGIA_DE_TESTES.md)**
   - Estratégia completa de testes automatizados
   - Padrões, ferramentas e cobertura mínima
   - Cenários críticos para sistema médico

8. **[📋 docs/PLANO_DE_ACAO.md](docs/PLANO_DE_ACAO.md)**
   - Templates e exemplos práticos de implementação
   - Snippets reutilizáveis e boas práticas
   - Workflow de desenvolvimento obrigatório

9. **[⚡ docs/QUICK_REFERENCE.md](docs/QUICK_REFERENCE.md)**
   - Comandos essenciais para o dia a dia
   - Referências rápidas e troubleshooting
   - Guia de emergência

---

## 🚀 Quick Start

### 📋 Pré-requisitos
- PHP 8.2+
- Composer
- XAMPP (Windows) ou servidor web equivalente
- Node.js (para assets)

### ⚡ Instalação Rápida
```bash
# 1. Clone o repositório
git clone [url-do-repositorio] EscalaMedica2
cd EscalaMedica2

# 2. Instale dependências
composer install

# 3. Configure ambiente
cp .env.example .env
php artisan key:generate

# 4. Configure banco de dados
touch database/database.sqlite

# 5. Execute migrations
php artisan migrate

# 6. Inicie o servidor
php artisan serve
```

### 🌐 Acesso
- **Aplicação**: http://localhost:8000
- **Documentação**: Consulte os arquivos .md listados acima

---

## 📊 Status do Projeto

### ✅ Funcionalidades Implementadas
- [x] Setup inicial do Laravel 11
- [x] Configuração de ambiente de desenvolvimento
- [x] Estrutura de documentação técnica completa
- [x] Glossário de domínio médico (15 termos)
- [x] Regras de negócio documentadas (25 regras)
- [x] Fluxos funcionais mapeados (8 fluxos principais)
- [x] Estratégia de testes definida (80%+ cobertura)
- [x] Padrões de desenvolvimento estabelecidos

### 🚧 Em Desenvolvimento
- [ ] Sistema de autenticação
- [ ] Gestão de usuários
- [ ] Módulo de escalas médicas
- [ ] Dashboard principal

### 📋 Roadmap
- [ ] Sistema de agendamentos
- [ ] Relatórios gerenciais
- [ ] API REST
- [ ] Notificações
- [ ] Mobile app

---

## 🏗️ Arquitetura do Sistema

```
EscalaMedica2/
├── 📋 REGISTRY.md              # ⚠️ SEMPRE CONSULTE PRIMEIRO
├── 📅 HISTORICO_COMMITS.md     # Histórico de alterações
├── 📁 docs/                    # Documentação técnica completa
│   ├── DOCUMENTACAO_TECNICA.md  # Arquitetura e padrões
│   ├── GLOSSARIO_DE_DOMINIO.md  # Termos padronizados
│   ├── REGRAS_DE_NEGOCIO.md     # Regras funcionais
│   ├── FLUXOS_FUNCIONAIS.md     # Processos mapeados
│   ├── ESTRATEGIA_DE_TESTES.md  # Framework de qualidade
│   ├── PLANO_DE_ACAO.md         # Guia de implementação
│   └── QUICK_REFERENCE.md       # Comandos essenciais
├── 📁 app/                     # Código da aplicação
├── 📁 database/                # Migrations, seeds, factories
├── 📁 resources/               # Views, assets
├── 📁 routes/                  # Definição de rotas
└── 📁 tests/                   # Testes automatizados
```

---

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 11.46.1** - Framework PHP
- **PHP 8.2.12** - Linguagem de programação
- **SQLite** (desenvolvimento) / **MySQL** (produção)
- **Eloquent ORM** - Mapeamento objeto-relacional

### Frontend
- **Blade Templates** - Engine de templates
- **Vite** - Build tool para assets
- **Bootstrap** (planejado) - Framework CSS

### Ferramentas de Desenvolvimento
- **Composer** - Gerenciador de dependências PHP
- **PHPUnit** - Framework de testes
- **Laravel Pint** - Code style fixer
- **XAMPP** - Ambiente de desenvolvimento local

---

## 🤝 Contribuição

### 📋 Workflow Obrigatório

1. **ANTES de implementar qualquer funcionalidade**:
   - ✅ Leia o [REGISTRY.md](REGISTRY.md)
   - ✅ Consulte o [HISTORICO_COMMITS.md](HISTORICO_COMMITS.md)
   - ✅ Revise o [PLANO_DE_ACAO.md](docs/PLANO_DE_ACAO.md)

2. **Durante a implementação**:
   - ✅ Siga os padrões definidos na documentação
   - ✅ Use as convenções de nomenclatura estabelecidas
   - ✅ Crie testes para novas funcionalidades

3. **APÓS a implementação**:
   - ✅ Atualize o [REGISTRY.md](REGISTRY.md) com as novas funcionalidades
   - ✅ Documente no [HISTORICO_COMMITS.md](HISTORICO_COMMITS.md)
   - ✅ Commit com mensagem descritiva

### 🔧 Comandos Essenciais
```bash
# Verificar qualidade do código
php artisan test
./vendor/bin/pint

# Gerar documentação de API (futuro)
php artisan l5-swagger:generate

# Verificar status do sistema
php artisan about
```

---

## 🛡️ Segurança

- **Autenticação**: Laravel Breeze (planejado)
- **Autorização**: Gates e Policies
- **Validação**: Form Requests
- **Proteção CSRF**: Habilitada
- **Sanitização**: Blade templates automático

Para reportar vulnerabilidades de segurança, entre em contato com [definir contato].

---

## 📞 Suporte

### 📚 Documentação
- **Técnica**: [docs/DOCUMENTACAO_TECNICA.md](docs/DOCUMENTACAO_TECNICA.md)
- **Comandos**: [docs/QUICK_REFERENCE.md](docs/QUICK_REFERENCE.md)
- **Laravel**: https://laravel.com/docs/11.x

### 🆘 Emergência
- **Troubleshooting**: [docs/QUICK_REFERENCE.md#troubleshooting](docs/QUICK_REFERENCE.md#troubleshooting)
- **Contatos**: [Definir contatos de emergência]

---

## 📄 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

---

## 📈 Estatísticas

- **Iniciado em**: 2025-10-20
- **Laravel Version**: 11.46.1
- **PHP Version**: 8.2.12
- **Total de Commits**: 3
- **Funcionalidades Ativas**: 1

---

**📍 Última atualização**: 2025-10-20  
**👥 Mantenedores**: [Definir equipe]  
**📋 Status**: Em desenvolvimento ativo

> **⚠️ IMPORTANTE**: Sempre consulte o [REGISTRY.md](REGISTRY.md) antes de implementar qualquer funcionalidade!
