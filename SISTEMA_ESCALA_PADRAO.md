# 📅 SISTEMA DE ESCALA PADRÃO ROTATIVA - 5 SEMANAS

## 🎯 Visão Geral

Sistema profissional de escala médica baseado em **template cíclico de 5 semanas**, utilizado em hospitais de referência mundial.

---

## 🏗️ Arquitetura Hierárquica

```
🏙️ CIDADE
   └─ 🏥 UNIDADE (Hospital, Clínica, Posto)
        └─ 📋 ESCALA PADRÃO (Template de 5 semanas que se repete)
             ├─ 📅 SEMANA 1
             │    ├─ Segunda
             │    │    ├─ ⏰ Turno Manhã
             │    │    │    ├─ 🏢 Setor UTI (3 médicos)
             │    │    │    └─ 🏢 Setor Emergência (2 médicos)
             │    │    ├─ ⏰ Turno Tarde
             │    │    │    └─ 🏢 Setor Teleconsulta (1 médico)
             │    │    └─ ⏰ Turno Noite
             │    │         └─ 🏢 Setor Emergência (2 médicos)
             │    ├─ Terça ... (mesma estrutura)
             │    ├─ Quarta ...
             │    ├─ Quinta ...
             │    ├─ Sexta ...
             │    ├─ Sábado ...
             │    └─ Domingo ...
             ├─ 📅 SEMANA 2 (pode ter configuração diferente)
             ├─ 📅 SEMANA 3
             ├─ 📅 SEMANA 4
             └─ 📅 SEMANA 5
                  (Após semana 5, volta para semana 1 - ciclo infinito)
```

---

## 📊 Estrutura de Dados

### Tabela: `escalas_padrao`
- **Função**: Template mestre da unidade
- **Campos**:
  - `unidade_id` - Qual unidade
  - `nome` - Ex: "Escala Padrão 2025"
  - `status` - ativo/inativo/arquivado
  - `vigencia_inicio` - Quando começou a valer

### Tabela: `semanas_template`
- **Função**: 5 semanas do ciclo
- **Campos**:
  - `numero_semana` - 1, 2, 3, 4 ou 5
  - `nome` - Ex: "Semana A", "Semana de Alta Demanda"

### Tabela: `dias_template`
- **Função**: 7 dias de cada semana
- **Campos**:
  - `dia_semana` - segunda, terca, ..., domingo

### Tabela: `configuracoes_turno_setor`
- **Função**: Configuração final: Turno + Setor + Qtd Médicos
- **Campos**:
  - `turno_id` - Manhã, Tarde, Noite, etc.
  - `setor_id` - UTI, Emergência, Teleconsulta, etc.
  - `quantidade_necessaria` - Quantos médicos necessários

---

## ✨ Vantagens do Sistema

### 1. **Ciclo Rotativo Automático**
```
Semana Real  |  Template Usado
-------------|----------------
Semana 1     →  Template 1
Semana 2     →  Template 2
Semana 3     →  Template 3
Semana 4     →  Template 4
Semana 5     →  Template 5
Semana 6     →  Template 1 (reinicia)
Semana 7     →  Template 2
...          →  ...
```

### 2. **Flexibilidade por Semana**
- Semana 1: Pode ter 5 médicos na UTI
- Semana 2: Pode ter 3 médicos na UTI
- Semana 3: Pode não ter UTI funcionando
- Semana 4: UTI 24h com escala reforçada
- Semana 5: Configuração balanceada

### 3. **Configuração Independente por Dia**
- Segunda: UTI + Emergência + Teleconsulta
- Terça: Só Emergência
- Sábado: Plantão reduzido
- Domingo: Emergência 24h

### 4. **Turnos Dinâmicos**
- Manhã: Setores X, Y, Z
- Tarde: Setores A, B
- Noite: Só Emergência

---

## 🔄 Como Funciona na Prática

### Exemplo Real: Hospital Municipal

**Data de Início**: 01/01/2025 (Segunda-feira)

#### Estrutura da Escala:

**SEMANA 1** (01-07/01): Semana Normal
- Segunda a Sexta:
  - Manhã: UTI (3 médicos) + Emergência (2)
  - Tarde: Teleconsulta (1)
  - Noite: Emergência (2)
- Sábado/Domingo:
  - Só Emergência 24h (1 médico por turno)

**SEMANA 2** (08-14/01): Semana Reforçada
- Segunda a Sexta:
  - Manhã: UTI (5 médicos) + Emergência (3)
  - Tarde: Teleconsulta (2) + Ambulatório (2)
  - Noite: UTI (2) + Emergência (3)
- Fim de semana: Escala normal

**SEMANA 3** (15-21/01): Semana Reduzida
- Segunda a Sexta:
  - Manhã: Emergência (2)
  - Tarde: Teleconsulta (1)
  - Noite: Emergência (1)
- Fim de semana: Plantão mínimo

**SEMANA 4** (22-28/01): Volta escala normal

**SEMANA 5** (29/01-04/02): Escala balanceada

**SEMANA 6** (05-11/02): **REPETE SEMANA 1** automaticamente!

---

## 💡 Casos de Uso

### 1. **Hospitais com Alta Demanda Sazonal**
```
Verão (Jan-Mar): Semanas 1, 2 reforçadas
Inverno (Jun-Ago): Semanas 3, 4 normais
```

### 2. **Clínicas com Especialidades Rotativas**
```
Semana 1: Cardiologia + Ortopedia
Semana 2: Pediatria + Ginecologia
Semana 3: Cardiologia + Pediatria
Semana 4: Ortopedia + Ginecologia
Semana 5: Todas as especialidades
```

### 3. **Postos de Saúde com Programas Semanais**
```
Semana 1: Vacinação + Consultas
Semana 2: Só Consultas
Semana 3: Exames + Consultas
Semana 4: Vacinação + Exames
Semana 5: Programas especiais
```

---

## 🛠️ Como Configurar (Exemplo Prático)

### Passo 1: Criar Escala Padrão
```php
$escalaPadrao = EscalaPadrao::create([
    'unidade_id' => 1, // Hospital Municipal
    'nome' => 'Escala 2025',
    'vigencia_inicio' => '2025-01-01',
    'status' => 'ativo'
]);
```

### Passo 2: Criar Estrutura Automática (5 semanas x 7 dias)
```php
$escalaPadrao->criarEstruturaPadrao();
```

### Passo 3: Configurar Turnos/Setores por Dia
```php
// SEMANA 1 - SEGUNDA - MANHÃ
$segunda = DiaTemplate::where('dia_semana', 'segunda')
    ->whereHas('semanaTemplate', function($q) {
        $q->where('numero_semana', 1);
    })->first();

$segunda->configuracoes()->create([
    'turno_id' => 1, // Manhã
    'setor_id' => 1, // UTI
    'quantidade_necessaria' => 3,
    'status' => 'ativo'
]);
```

---

## 📈 Benefícios Gerenciais

### ✅ Planejamento de Longo Prazo
- Define uma vez, funciona por meses/anos
- Fácil visualização de padrões

### ✅ Distribuição Justa de Carga
- Rotação automática equilibra trabalho
- Evita sobrecarga de profissionais

### ✅ Previsibilidade
- Equipes sabem com antecedência
- Facilita férias e folgas

### ✅ Adaptabilidade
- Ajusta template conforme demanda
- Não precisa refazer tudo

---

## 🔍 Consultas Úteis

### Qual semana está vigente hoje?
```php
$escala = EscalaPadrao::where('unidade_id', 1)->first();
$semanaAtual = $escala->getSemanaAtual(); // Retorna 1 a 5
```

### Configuração de um dia específico:
```php
$config = ConfiguracaoTurnoSetor::whereHas('diaTemplate', function($q) {
    $q->whereHas('semanaTemplate', function($q2) {
        $q2->where('numero_semana', 1); // Semana 1
    })->where('dia_semana', 'segunda'); // Segunda
})->where('turno_id', 1) // Manhã
  ->where('setor_id', 1)  // UTI
  ->first();
```

---

## 📚 Próximos Passos

1. ✅ **Estrutura de dados criada**
2. ⏳ **Controller para gerenciar escala padrão**
3. ⏳ **Interface visual (calendário 5 semanas)**
4. ⏳ **Copiar configuração entre semanas**
5. ⏳ **Gerar alocações reais a partir do template**

---

**Documentação criada em**: 2025-10-21  
**Versão**: 1.0  
**Status**: ✅ Implementado e funcionando
