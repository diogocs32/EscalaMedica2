# 🔗 MAPA DE RELACIONAMENTOS - EscalaMedica2

> **🎯 OBJETIVO**: Documentar TODOS os relacionamentos entre entidades, controllers, views e suas dependências para facilitar refatorações e evitar erros de referências quebradas.

> **⚠️ CRÍTICO**: Ao modificar qualquer relacionamento, consulte este documento para identificar TODOS os pontos de impacto.

---

## 📊 ÍNDICE GERAL

1. [Relacionamentos de Models](#relacionamentos-de-models)
2. [Eager Loading em Controllers](#eager-loading-em-controllers)
3. [Referências em Views](#referências-em-views)
4. [Validações com Foreign Keys](#validações-com-foreign-keys)
5. [Matriz de Impacto](#matriz-de-impacto)

---

## 🗃️ RELACIONAMENTOS DE MODELS

### E001 - Plantonista

**Model**: `App\Models\Plantonista`  
**Tabela**: `plantonistas`

#### Relacionamentos Definidos:
```php
// app/Models/Plantonista.php
hasMany(Alocacao::class, 'plantonista_id') → Alocacao
```

#### Usado Por:
- ✅ `AlocacaoController` - eager loading `'plantonista'`
- ✅ `views/alocacoes/*` - acessa `$alocacao->plantonista`
- ✅ `views/plantonistas/show.blade.php` - lista alocações via `$plantonista->alocacoes`

#### Dependências Reversas:
- Se remover/alterar: ⚠️ **IMPACTO ALTO** - quebra sistema de alocações

---

### E002 - Cidade

**Model**: `App\Models\Cidade`  
**Tabela**: `cidades`

#### Relacionamentos Definidos:
```php
// app/Models/Cidade.php
hasMany(Unidade::class, 'cidade_id') → Unidade
```

#### Usado Por:
- ✅ `UnidadeController@index` - eager loading `'cidade'`
- ✅ `UnidadeController@create` - carrega cidades para select
- ✅ `UnidadeController@edit` - carrega cidades para select
- ✅ `views/unidades/*` - acessa `$unidade->cidade->nome`
- ✅ `views/cidades/show.blade.php` - lista unidades via `$cidade->unidades`

#### Dependências Reversas:
- Se remover/alterar: ⚠️ **IMPACTO MÉDIO** - quebra cadastro de unidades

---

### E003 - Unidade

**Model**: `App\Models\Unidade`  
**Tabela**: `unidades`

#### Relacionamentos Definidos:
```php
// app/Models/Unidade.php
belongsTo(Cidade::class, 'cidade_id') → Cidade
hasMany(Vaga::class, 'unidade_id') → Vaga
```

#### ❌ Relacionamentos REMOVIDOS (Reestruturação 2025-10-21):
```php
// REMOVIDO: hasMany(Setor::class, 'unidade_id') → Setor
// MOTIVO: Setores agora são globais, relação via tabela 'vagas'
```

#### Usado Por:
- ✅ `VagaController` - eager loading `'unidade'`
- ✅ `views/vagas/*` - acessa `$vaga->unidade->nome`
- ✅ `views/unidades/show.blade.php` - lista vagas via `$unidade->vagas`
- ✅ `views/setores/show.blade.php` - acessa via `$vaga->unidade` (através de vagas)

#### Dependências Reversas:
- Se remover/alterar: ⚠️ **IMPACTO ALTO** - quebra sistema de vagas

---

### E004 - Setor ⚠️ **ATENÇÃO: ENTIDADE GLOBAL**

**Model**: `App\Models\Setor`  
**Tabela**: `setores`

#### Relacionamentos Definidos:
```php
// app/Models/Setor.php
hasMany(Vaga::class, 'setor_id') → Vaga
```

#### ❌ Relacionamentos REMOVIDOS (Reestruturação 2025-10-21):
```php
// REMOVIDO: belongsTo(Unidade::class, 'unidade_id') → Unidade
// MOTIVO: Setores são globais, não pertencem a unidades específicas
// MIGRAÇÃO: database/migrations/2025_10_21_004032_create_setores_table.php
// COMMIT: Reestruturação Arquitetural - Setores Globais
```

#### ⚠️ PONTOS DE ATENÇÃO:
**NÃO tentar carregar**:
```php
❌ $setor->unidade        // RelationNotFoundException
❌ $setor->load('unidade') // RelationNotFoundException
```

**FORMA CORRETA de acessar unidades**:
```php
✅ $setor->vagas->pluck('unidade')->unique()
✅ $setor->load(['vagas.unidade', 'vagas.turno'])
```

#### Usado Por:
- ✅ `SetorController@show` - eager loading `['vagas.turno', 'vagas.unidade']`
- ✅ `VagaController` - eager loading `'setor'`
- ✅ `views/vagas/*` - acessa `$vaga->setor->nome`
- ✅ `views/setores/show.blade.php` - lista vagas com suas unidades

#### Dependências Reversas:
- Se remover/alterar: ⚠️ **IMPACTO ALTO** - quebra sistema de vagas

---

### E005 - Turno ⚠️ **ATENÇÃO: ENTIDADE GLOBAL**

**Model**: `App\Models\Turno`  
**Tabela**: `turnos`

#### Relacionamentos Definidos:
```php
// app/Models/Turno.php
hasMany(Vaga::class, 'turno_id') → Vaga
```

#### Usado Por:
- ✅ `VagaController` - eager loading `'turno'`
- ✅ `AlocacaoController` - eager loading via `'vaga.turno'`
- ✅ `views/vagas/*` - acessa `$vaga->turno->nome`
- ✅ `views/turnos/show.blade.php` - lista vagas via `$turno->vagas`
- ✅ `AlocacaoObserver` - usa `$alocacao->vaga->turno` para calcular horários

#### Dependências Reversas:
- Se remover/alterar: ⚠️ **IMPACTO CRÍTICO** - quebra sistema de alocações e cálculo de horários

---

### E006 - Vaga ⚠️ **TABELA CENTRAL DE CONFIGURAÇÃO**

**Model**: `App\Models\Vaga`  
**Tabela**: `vagas`

#### Relacionamentos Definidos:
```php
// app/Models/Vaga.php
belongsTo(Unidade::class, 'unidade_id') → Unidade
belongsTo(Setor::class, 'setor_id') → Setor (GLOBAL)
belongsTo(Turno::class, 'turno_id') → Turno (GLOBAL)
hasMany(Alocacao::class, 'vaga_id') → Alocacao
```

#### Usado Por:
- ✅ `AlocacaoController` - carrega vagas para select, eager loading `'vaga'`
- ✅ `SetorController@show` - acessa via `$setor->vagas`
- ✅ `TurnoController@show` - acessa via `$turno->vagas`
- ✅ `UnidadeController@show` - acessa via `$unidade->vagas`
- ✅ `views/alocacoes/*` - acessa `$alocacao->vaga`
- ✅ `views/setores/show.blade.php` - itera `$setor->vagas`
- ✅ `AlocacaoObserver` - usa para calcular horários

#### Dependências Reversas:
- Se remover/alterar: 🚨 **IMPACTO CRÍTICO** - quebra TODO o sistema

---

### E007 - Alocacao

**Model**: `App\Models\Alocacao`  
**Tabela**: `alocacoes`

#### Relacionamentos Definidos:
```php
// app/Models/Alocacao.php
belongsTo(Plantonista::class, 'plantonista_id') → Plantonista
belongsTo(Vaga::class, 'vaga_id') → Vaga
```

#### Relacionamentos Calculados (via Vaga):
```php
// Acesso indireto através da vaga
$alocacao->vaga->turno
$alocacao->vaga->setor
$alocacao->vaga->unidade
```

#### Usado Por:
- ✅ `AlocacaoObserver` - auto-calcula datas/horários
- ✅ `PlantonisταController@show` - lista via `$plantonista->alocacoes`
- ✅ `views/alocacoes/*` - acessa todos os relacionamentos
- ✅ `DashboardController` - estatísticas

#### Dependências Reversas:
- Se remover/alterar: ⚠️ **IMPACTO ALTO** - quebra histórico de plantões

---

## 🎮 EAGER LOADING EM CONTROLLERS

### SetorController

```php
// app/Http/Controllers/SetorController.php

// ✅ CORRETO (após reestruturação 2025-10-21)
public function show(Setor $setor)
{
    $setor->load(['vagas.turno', 'vagas.unidade']);
    return view('setores.show', compact('setor'));
}

// ❌ INCORRETO (causava RelationNotFoundException)
// $setor->load(['unidade', 'vagas.turno']);
```

**Regra**: Setor NÃO tem relacionamento direto com Unidade

---

### UnidadeController

```php
// app/Http/Controllers/UnidadeController.php

public function index()
{
    $unidades = Unidade::with('cidade')->paginate(15);
    return view('unidades.index', compact('unidades'));
}

public function show(Unidade $unidade)
{
    $unidade->load(['vagas.setor', 'vagas.turno']);
    return view('unidades.show', compact('unidade'));
}
```

---

### AlocacaoController

```php
// app/Http/Controllers/AlocacaoController.php

public function index()
{
    $alocacoes = Alocacao::with([
        'plantonista',
        'vaga.setor',
        'vaga.turno',
        'vaga.unidade'
    ])->paginate(15);
    
    return view('alocacoes.index', compact('alocacoes'));
}
```

---

## 👁️ REFERÊNCIAS EM VIEWS

### views/setores/show.blade.php

```blade
✅ CORRETO:
@foreach($setor->vagas as $vaga)
    {{ $vaga->unidade->nome }}
    {{ $vaga->turno->nome }}
@endforeach

❌ INCORRETO:
{{ $setor->unidade->nome }}  <!-- RelationNotFoundException -->
```

---

### views/unidades/show.blade.php

```blade
✅ CORRETO:
{{ $unidade->cidade->nome }}

@foreach($unidade->vagas as $vaga)
    {{ $vaga->setor->nome }}
    {{ $vaga->turno->nome }}
@endforeach
```

---

### views/alocacoes/*.blade.php

```blade
✅ CORRETO:
{{ $alocacao->plantonista->nome }}
{{ $alocacao->vaga->setor->nome }}
{{ $alocacao->vaga->turno->nome }}
{{ $alocacao->vaga->unidade->nome }}
```

---

## ✅ VALIDAÇÕES COM FOREIGN KEYS

### SetorController

```php
// ❌ REMOVIDO (2025-10-21):
'unidade_id' => 'required|exists:unidades,id'

// ✅ VALIDAÇÕES ATUAIS:
'nome' => 'required|string|max:255|unique:setores,nome'
```

---

### UnidadeController

```php
// ✅ MANTIDO:
'cidade_id' => 'required|exists:cidades,id'
```

---

### VagaController

```php
// ✅ VALIDAÇÕES:
'unidade_id' => 'required|exists:unidades,id',
'setor_id' => 'required|exists:setores,id',
'turno_id' => 'required|exists:turnos,id',

// Unique constraint:
'unique:vagas,unidade_id,NULL,id,setor_id,' . $request->setor_id . ',turno_id,' . $request->turno_id
```

---

## 📊 MATRIZ DE IMPACTO

### Se alterar: **Setor** (relacionamentos)

| Arquivo | Tipo | Ação Necessária | Prioridade |
|---------|------|-----------------|------------|
| `app/Models/Setor.php` | Model | Atualizar relacionamentos | 🚨 CRÍTICA |
| `app/Http/Controllers/SetorController.php` | Controller | Ajustar eager loading | 🚨 CRÍTICA |
| `views/setores/show.blade.php` | View | Ajustar acessos | ⚠️ ALTA |
| `views/setores/index.blade.php` | View | Ajustar listagem | ⚠️ ALTA |
| `database/migrations/*_setores_table.php` | Migration | Atualizar schema | 🚨 CRÍTICA |
| `MAPA_RELACIONAMENTOS.md` | Doc | Documentar mudança | ⚠️ ALTA |

---

### Se alterar: **Vaga** (relacionamentos)

| Arquivo | Tipo | Ação Necessária | Prioridade |
|---------|------|-----------------|------------|
| `app/Models/Vaga.php` | Model | Atualizar relacionamentos | 🚨 CRÍTICA |
| `app/Http/Controllers/VagaController.php` | Controller | Ajustar validações | 🚨 CRÍTICA |
| `app/Http/Controllers/AlocacaoController.php` | Controller | Ajustar eager loading | 🚨 CRÍTICA |
| `app/Http/Controllers/SetorController.php` | Controller | Ajustar acesso vagas | ⚠️ ALTA |
| `app/Http/Controllers/UnidadeController.php` | Controller | Ajustar acesso vagas | ⚠️ ALTA |
| `app/Http/Controllers/TurnoController.php` | Controller | Ajustar acesso vagas | ⚠️ ALTA |
| `app/Observers/AlocacaoObserver.php` | Observer | Ajustar cálculos | 🚨 CRÍTICA |
| `views/alocacoes/*` | Views | Ajustar acessos | ⚠️ ALTA |
| `views/setores/show.blade.php` | View | Ajustar listagem | ⚠️ ALTA |
| `views/unidades/show.blade.php` | View | Ajustar listagem | ⚠️ ALTA |
| `views/turnos/show.blade.php` | View | Ajustar listagem | ⚠️ ALTA |
| `database/migrations/*_vagas_table.php` | Migration | Atualizar schema | 🚨 CRÍTICA |

---

## 🔄 HISTÓRICO DE MUDANÇAS

### 2025-10-21: Reestruturação Setores e Turnos Globais

#### Mudanças:
1. **Setor**: Removido `belongsTo(Unidade::class, 'unidade_id')`
2. **Migration**: Removido coluna `unidade_id` da tabela `setores`
3. **SetorController**: Removido eager loading `'unidade'`
4. **Views**: Ajustadas para acessar unidades via `$setor->vagas->pluck('unidade')`

#### Arquivos Impactados:
- ✅ `database/migrations/2025_10_21_004032_create_setores_table.php`
- ✅ `app/Models/Setor.php`
- ✅ `app/Http/Controllers/SetorController.php`
- ✅ `resources/views/setores/*.blade.php`
- ✅ `BUGS_CORRIGIDOS.md`
- ✅ `REGISTRY.md`
- ✅ `PROGRESSO_ATUAL.md`

#### Motivo:
Permitir que setores sejam reutilizados em múltiplas unidades sem duplicação.

---

## 📝 CHECKLIST DE REFATORAÇÃO

Ao modificar qualquer relacionamento, seguir este checklist:

### 1️⃣ **Planejamento**
- [ ] Consultar `MAPA_RELACIONAMENTOS.md`
- [ ] Identificar todos os pontos de impacto na Matriz
- [ ] Criar backup do código atual
- [ ] Documentar motivo da mudança

### 2️⃣ **Implementação - Models**
- [ ] Atualizar relacionamentos no Model
- [ ] Remover imports desnecessários
- [ ] Verificar sintaxe com `get_errors`

### 3️⃣ **Implementação - Migrations**
- [ ] Atualizar schema da tabela
- [ ] Criar/remover foreign keys
- [ ] Testar rollback

### 4️⃣ **Implementação - Controllers**
- [ ] Ajustar eager loading em `index()`
- [ ] Ajustar eager loading em `show()`
- [ ] Ajustar validações em `store()`
- [ ] Ajustar validações em `update()`
- [ ] Remover carregamento de relacionamentos removidos
- [ ] Verificar sintaxe com `get_errors`

### 5️⃣ **Implementação - Views**
- [ ] Ajustar acessos a relacionamentos
- [ ] Atualizar loops `@foreach`
- [ ] Verificar null safety (`??`, `?->`)
- [ ] Testar renderização

### 6️⃣ **Implementação - Observers/Rules**
- [ ] Atualizar acessos em Observers
- [ ] Atualizar validações customizadas
- [ ] Testar lógica de negócio

### 7️⃣ **Documentação**
- [ ] Atualizar `MAPA_RELACIONAMENTOS.md`
- [ ] Atualizar `REGISTRY.md`
- [ ] Atualizar `BUGS_CORRIGIDOS.md`
- [ ] Adicionar entrada no histórico de mudanças

### 8️⃣ **Testes**
- [ ] Rodar `php artisan route:list`
- [ ] Testar CRUD completo no navegador
- [ ] Verificar logs de erro
- [ ] Testar eager loading no Tinker

### 9️⃣ **Commit**
- [ ] Mensagem descritiva
- [ ] Incluir todos os arquivos modificados
- [ ] Referenciar issue se houver

---

## 🚨 ALERTAS CRÍTICOS

### ⚠️ Relacionamentos GLOBAIS (não vinculados a unidades):
- **Setor**: Acessar unidades APENAS via `$setor->vagas->pluck('unidade')`
- **Turno**: Acessar unidades APENAS via `$turno->vagas->pluck('unidade')`

### ⚠️ Tabela CENTRAL:
- **Vaga**: Qualquer mudança aqui impacta TODO o sistema

### ⚠️ Observers Ativos:
- **AlocacaoObserver**: Depende de `vaga->turno` para cálculos automáticos

### ⚠️ Parâmetros de Rotas Resource:
- **Problema de Pluralização**: Laravel pode singularizar incorretamente nomes de recursos
- **Exemplo**: `Route::resource('setores')` gerava `{setore}` ao invés de `{setor}`
- **Solução**: Sempre usar `->parameters(['plural' => 'singular'])` explicitamente
- **Atual**:
  ```php
  Route::resource('setores', SetorController::class)->parameters(['setores' => 'setor']);
  ```

---

**📍 Última atualização**: 2025-10-21  
**🔄 Próxima revisão**: A cada mudança em relacionamentos
