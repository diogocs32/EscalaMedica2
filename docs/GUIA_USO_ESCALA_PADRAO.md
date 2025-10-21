# 📖 Guia de Uso: Sistema de Escala Padrão

## Visão Geral

O Sistema de Escala Padrão permite criar um modelo de 5 semanas que se repete automaticamente, facilitando o planejamento de longo prazo das escalas médicas.

---

## 🚀 Como Usar

### 1. Criar uma Escala Padrão

**URL:** `/unidades/{id}/escala-padrao/create`

1. Acesse a página da unidade desejada
2. Clique em "Criar Escala Padrão"
3. Preencha:
   - **Nome**: Identificação da escala (ex: "Escala Padrão 2025")
   - **Descrição**: Informações adicionais (opcional)
   - **Data de Vigência**: A partir de quando a escala começa a contar
4. Clique em "Criar Escala Padrão"

> 📝 **Nota**: O sistema cria automaticamente uma estrutura de 5 semanas × 7 dias = 35 dias vazios, prontos para serem configurados.

---

### 2. Visualizar a Escala Padrão

**URL:** `/unidades/{id}/escala-padrao`

A tela principal mostra:
- **5 abas** (uma para cada semana)
- **7 cards por semana** (um para cada dia da semana)
- **Badge "ATUAL"** na semana que está ativa no momento
- **Configurações de cada dia** (Turno + Setor + Quantidade)

**Indicadores:**
- 🟢 Semana atual destacada em azul claro
- 📅 Cada card mostra os turnos e setores configurados
- ➕ Botão "+ Configurar" em cada dia

---

### 3. Configurar um Dia Específico

**URL:** `/unidades/{id}/escala-padrao/{semana}/{dia}/edit`

**Exemplo:** `/unidades/1/escala-padrao/1/segunda/edit`

#### Adicionar Configuração:
1. Selecione o **Turno** (ex: "Manhã 07:00-13:00")
2. Selecione o **Setor** (ex: "Emergência")
3. Defina a **Quantidade de Médicos** (ex: 3)
4. Adicione **Observações** (opcional)
5. Clique em "Adicionar Configuração"

> ⚠️ **Importante**: Não é possível adicionar a mesma combinação Turno + Setor duas vezes no mesmo dia.

#### Remover Configuração:
- Clique no botão "🗑️ Remover" na configuração desejada
- Confirme a exclusão

---

### 4. Copiar Configurações Entre Dias

**Funcionalidade:** Copiar todas as configurações de um dia para outros dias/semanas.

**Passos:**
1. Configure um dia completamente (ex: Segunda da Semana 1)
2. Clique em "📋 Copiar para outros dias"
3. Selecione:
   - **Semana de Destino** (pode ser a mesma ou outra)
   - **Dias de Destino** (selecione múltiplos dias)
   - **Sobrescrever?** (marque para substituir configs existentes)
4. Clique em "Copiar"

**Casos de Uso:**
- Copiar Segunda para Terça da mesma semana
- Copiar toda Semana 1 para Semana 2
- Criar padrão de dias úteis (seg-sex) de uma vez

---

## 🔄 Como Funciona a Rotação de 5 Semanas

O sistema calcula automaticamente qual semana está ativa:

```
Data de Vigência: 01/10/2025 (Semana 1)
- 01/10 a 07/10: Semana 1
- 08/10 a 14/10: Semana 2
- 15/10 a 21/10: Semana 3
- 22/10 a 28/10: Semana 4
- 29/10 a 04/11: Semana 5
- 05/11 a 11/11: Semana 1 (reinicia o ciclo)
```

**Método de Cálculo:**
```php
$escala = $unidade->escalaPadraoAtiva();
$semanaAtual = $escala->getSemanaAtual(); // Retorna 1, 2, 3, 4 ou 5
```

---

## 📊 Exemplos Práticos

### Exemplo 1: Escala de 24 Horas

**Segunda-feira da Semana 1:**
- Turno Manhã (07:00-13:00) → Emergência → 2 médicos
- Turno Tarde (13:00-19:00) → Emergência → 2 médicos
- Turno Noite (19:00-07:00) → Emergência → 1 médico
- Turno Manhã (07:00-13:00) → UTI → 1 médico

**Copiar para:**
- Todos os dias da Semana 1 (seg-dom)
- Depois copiar Semana 1 inteira para Semanas 2-5

---

### Exemplo 2: Escala com Variação Semanal

**Semana 1 (Normal):**
- Segunda a Sexta: 3 médicos por turno
- Sábado e Domingo: 2 médicos por turno

**Semana 2 (Reduzida):**
- Segunda a Sexta: 2 médicos por turno
- Sábado e Domingo: 1 médico por turno

**Semanas 3-5:**
- Copiar configuração da Semana 1

---

## 🎯 Fluxo Completo Recomendado

### 1️⃣ Planejamento Inicial
- Defina quantos turnos sua unidade tem
- Liste os setores que funcionam
- Determine quantos médicos são necessários por combinação

### 2️⃣ Criação da Escala
1. Acesse `/unidades/{id}/escala-padrao/create`
2. Crie a escala padrão com a data de início

### 3️⃣ Configuração da Semana 1
1. Configure completamente a **Segunda-feira** da Semana 1
2. Use "Copiar" para replicar para Terça-Sexta (se for padrão)
3. Configure separadamente Sábado e Domingo (se forem diferentes)

### 4️⃣ Configuração das Semanas 2-5
- **Se todas as semanas são iguais:**
  - Copie cada dia da Semana 1 para as Semanas 2-5
  
- **Se há variação entre semanas:**
  - Configure Semana 2 manualmente
  - Copie padrões quando possível

### 5️⃣ Revisão Final
1. Navegue pelas 5 abas
2. Verifique cada dia
3. Ajuste quantidades conforme necessário

---

## 🔍 Troubleshooting

### "Esta unidade já possui uma escala padrão ativa"
- Só é permitida uma escala ativa por unidade
- Para criar uma nova, desative a anterior primeiro

### "Esta combinação de Turno + Setor já está configurada"
- Não é possível duplicar a mesma configuração no mesmo dia
- Remova a existente antes de adicionar novamente

### "Semana atual não está aparecendo correta"
- Verifique a Data de Vigência da escala
- O cálculo é feito automaticamente a partir dessa data
- Use `$escala->getSemanaAtual()` para verificar

---

## 🎨 Interface

### Cores e Badges
- 🔵 **Azul**: Semana atual em destaque
- 🟢 **Verde**: Botão de copiar configurações
- 🔴 **Vermelho**: Botão de remover
- 📊 **Badge numérico**: Quantidade de médicos necessários

### Navegação
- **Breadcrumb**: Voltar para escala principal
- **Tabs superiores**: Alternar entre semanas
- **Cards de dia**: Cada dia é um card independente

---

## 💾 Dados Armazenados

### Tabelas Utilizadas
1. `escalas_padrao` - Master da escala
2. `semanas_template` - 5 semanas (1-5)
3. `dias_template` - 7 dias por semana
4. `configuracoes_turno_setor` - Configurações finais (Turno + Setor + Qty)

### Estrutura de Dados
```
Unidade
└── EscalaPadrao
    ├── SemanaTemplate (1)
    │   ├── DiaTemplate (segunda)
    │   │   ├── ConfiguracaoTurnoSetor #1
    │   │   └── ConfiguracaoTurnoSetor #2
    │   ├── DiaTemplate (terca)
    │   └── ... (5 dias restantes)
    ├── SemanaTemplate (2)
    └── ... (3 semanas restantes)
```

---

## 🚦 Status e Validações

### Status Disponíveis
- **ativo**: Escala em uso
- **inativo**: Escala desativada

### Validações Automáticas
- ✓ Única escala ativa por unidade
- ✓ Quantidade de médicos entre 1 e 50
- ✓ Combinação Turno + Setor única por dia
- ✓ Data de vigência obrigatória

---

## 📈 Próximos Passos

Funcionalidades planejadas:
- [ ] Gerar alocações reais a partir do template
- [ ] Exportar escala para PDF/Excel
- [ ] Histórico de alterações
- [ ] Notificações de mudanças
- [ ] API para integração mobile

---

## 🆘 Suporte

Para dúvidas ou problemas:
1. Consulte a documentação técnica em `SISTEMA_ESCALA_PADRAO.md`
2. Verifique os logs em `storage/logs/laravel.log`
3. Execute `php artisan route:list --path=escala` para ver todas as rotas
