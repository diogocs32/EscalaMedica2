# 🏗️ ARQUITETURA DE CONFIGURAÇÃO - Sistema de Vagas

> **Data**: 2025-10-21  
> **Versão**: 2.0 - Setores e Turnos Globais

## 📋 Visão Geral

O sistema utiliza uma arquitetura **flexível e escalável** onde **Setores** e **Turnos** são **entidades globais**, e cada **Unidade** configura quais setores operam em quais turnos através da tabela **Vagas**.

---

## 🎯 Estrutura Central

### Entidades Globais (Independentes)

#### 1️⃣ **Setores** (Globais)
- 📍 **Tabela**: `setores`
- 🔑 **Chave**: `nome` (unique)
- 📝 **Campos**: nome, descricao, status
- 💡 **Exemplos**: UTI, Emergência, Cardiologia, Pediatria, Ortopedia

**Característica**: Um setor cadastrado está disponível para TODAS as unidades do sistema.

#### 2️⃣ **Turnos** (Globais)
- 📍 **Tabela**: `turnos`
- 🔑 **Chave**: `nome` (unique)
- 📝 **Campos**: nome, hora_inicio, hora_fim, duracao_horas, periodo, status
- 💡 **Exemplos**: Manhã (07:00-13:00), Tarde (13:00-19:00), Noite (19:00-07:00)

**Característica**: Um turno cadastrado está disponível para TODAS as unidades do sistema.

---

## ⚙️ Tabela de Configuração: VAGAS

A tabela **vagas** é o **coração da configuração** do sistema. Ela conecta:

```
Unidade + Setor (Global) + Turno (Global) + Quantidade de Médicos
```

### Estrutura da Tabela `vagas`

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | bigint | Identificador único |
| `unidade_id` | bigint | FK para unidades |
| `setor_id` | bigint | FK para setores (globais) |
| `turno_id` | bigint | FK para turnos (globais) |
| `quantidade_necessaria` | int | Quantos médicos são necessários |
| `observacoes` | text | Observações específicas |
| `status` | enum | ativo/inativo |

**Unique Key**: `(unidade_id, setor_id, turno_id)` - Previne duplicatas

---

## 🔄 Fluxo de Configuração

### Passo 1: Cadastrar Entidades Globais

```
1. Cadastrar SETORES (globais):
   - UTI
   - Emergência
   - Cardiologia
   - Pediatria
   
2. Cadastrar TURNOS (globais):
   - Manhã: 07:00 - 13:00 (6h)
   - Tarde: 13:00 - 19:00 (6h)
   - Noite: 19:00 - 07:00 (12h)
   - Plantão 24h: 07:00 - 07:00 (24h)
```

### Passo 2: Configurar Unidades

Para cada **Unidade**, definir através da tabela **Vagas**:

```
Hospital Central (Unidade #1):
├─ UTI
│  ├─ Manhã: 2 médicos necessários
│  ├─ Tarde: 2 médicos necessários
│  └─ Noite: 1 médico necessário
│
├─ Emergência
│  ├─ Manhã: 3 médicos necessários
│  ├─ Tarde: 3 médicos necessários
│  └─ Noite: 2 médicos necessários
│
└─ Cardiologia
   ├─ Manhã: 1 médico necessário
   └─ Tarde: 1 médico necessário
   (Não opera à noite)

Clínica da Família (Unidade #2):
├─ Emergência
│  └─ Manhã: 1 médico necessário
│
└─ Pediatria
   ├─ Manhã: 1 médico necessário
   └─ Tarde: 1 médico necessário
```

---

## 📊 Exemplos Práticos

### Exemplo 1: Configurar UTI no Hospital Central

```php
// 1. Setor "UTI" já existe globalmente (ID: 1)
// 2. Turno "Manhã" já existe globalmente (ID: 1)
// 3. Unidade "Hospital Central" existe (ID: 1)

// Criar vaga:
Vaga::create([
    'unidade_id' => 1,           // Hospital Central
    'setor_id' => 1,             // UTI (global)
    'turno_id' => 1,             // Manhã (global)
    'quantidade_necessaria' => 2, // 2 médicos
    'status' => 'ativo'
]);
```

### Exemplo 2: Mesmo Setor em Unidades Diferentes

```php
// UTI no Hospital Central - Manhã - 2 médicos
Vaga::create([
    'unidade_id' => 1, // Hospital Central
    'setor_id' => 1,   // UTI
    'turno_id' => 1,   // Manhã
    'quantidade_necessaria' => 2
]);

// UTI na Clínica Norte - Manhã - 1 médico
Vaga::create([
    'unidade_id' => 2, // Clínica Norte
    'setor_id' => 1,   // UTI (mesmo setor global)
    'turno_id' => 1,   // Manhã (mesmo turno global)
    'quantidade_necessaria' => 1 // Quantidade diferente
]);
```

---

## 🔍 Consultas Úteis

### Listar todos os setores de uma unidade

```php
$unidade = Unidade::find(1);

// Setores únicos operando nesta unidade
$setores = $unidade->vagas()
    ->with('setor')
    ->get()
    ->unique('setor_id')
    ->pluck('setor');
```

### Listar todos os turnos de um setor em uma unidade

```php
$unidade = Unidade::find(1);
$setor = Setor::find(1);

$turnos = Vaga::where('unidade_id', $unidade->id)
    ->where('setor_id', $setor->id)
    ->with('turno')
    ->get()
    ->pluck('turno');
```

### Verificar quantos médicos são necessários

```php
$vaga = Vaga::where('unidade_id', 1)
    ->where('setor_id', 1)
    ->where('turno_id', 1)
    ->first();

echo "Necessário: {$vaga->quantidade_necessaria} médicos";
```

### Listar unidades que usam um setor específico

```php
$setor = Setor::find(1);

$unidades = Vaga::where('setor_id', $setor->id)
    ->with('unidade')
    ->get()
    ->unique('unidade_id')
    ->pluck('unidade');
```

---

## ✅ Vantagens da Arquitetura

### 1. **Reutilização**
- Setores e turnos são cadastrados **uma única vez**
- Utilizados em **múltiplas unidades**

### 2. **Flexibilidade**
- Cada unidade configura **seus próprios** setores e turnos
- Unidades pequenas podem usar **poucos setores**
- Hospitais grandes podem usar **todos os setores**

### 3. **Granularidade**
- Controle preciso de **quantos médicos** em **cada setor** em **cada turno**
- Diferentes unidades = diferentes necessidades

### 4. **Escalabilidade**
- Adicionar novo setor = disponível para **todas as unidades**
- Adicionar nova unidade = escolher quais setores/turnos usar

### 5. **Manutenção**
- Alterar horário de um turno = atualiza em **todas as unidades**
- Renomear setor = reflete em **todo o sistema**

---

## 🚫 Restrições

### Unique Constraint: Uma vaga por combinação

```sql
UNIQUE (unidade_id, setor_id, turno_id)
```

❌ **Não é possível**:
- Criar 2 vagas para UTI + Manhã na mesma unidade

✅ **Solução**:
- Use o campo `quantidade_necessaria` para múltiplos médicos

---

## 🔄 Relacionamentos

```
Setor (Global)
  └─ hasMany(Vaga) - vagas que usam este setor

Turno (Global)
  └─ hasMany(Vaga) - vagas que usam este turno

Unidade
  ├─ hasMany(Vaga) - configurações desta unidade
  └─ through Vaga:
      ├─ Acessa Setores disponíveis
      └─ Acessa Turnos disponíveis

Vaga (Tabela Central)
  ├─ belongsTo(Unidade)
  ├─ belongsTo(Setor) - setor global
  ├─ belongsTo(Turno) - turno global
  └─ hasMany(Alocacao) - alocações de plantonistas
```

---

## 📝 Próximos Passos

1. ✅ Estrutura de Setores/Turnos globais implementada
2. ⏳ Interface para configuração de vagas por unidade
3. ⏳ Dashboard mostrando configurações
4. ⏳ Validações avançadas de alocações

---

**Última atualização**: 2025-10-21  
**Autor**: Sistema EscalaMedica2
