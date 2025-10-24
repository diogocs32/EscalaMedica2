# 📋 Funcionalidade: Aumentar Vagas Dinamicamente

> **Data de Criação**: 2025-10-24  
> **Versão**: 1.0  
> **Status**: 🚧 Em Desenvolvimento

---

## 🎯 Objetivo

Permitir que o usuário aumente dinamicamente o número de vagas/slots em uma escala publicada quando necessário, sem precisar recriar a escala ou modificar a configuração padrão.

---

## 📊 Análise de Sistemas Similares

### Padrões Identificados em Sistemas de Agendamento:

1. **Google Calendar / Outlook**
   - Permite "adicionar participantes" a um evento existente
   - Usa botão "+" próximo à lista de participantes
   - ✅ **Vantagem**: Visual claro e intuitivo

2. **Trello / Jira**
   - Adiciona cards/issues dinamicamente
   - Botão "Add another card" sempre visível
   - ✅ **Vantagem**: Ação contextual e imediata

3. **Sistemas Hospitalares (HealthScheduler, PerfectServe)**
   - Modo "Administrador" com permissões especiais
   - Botão "Add Shift" ou "Duplicate Slot"
   - ⚠️ **Desvantagem**: Requer modo especial (complexo)

### ✨ Melhor Abordagem para EscalaMedica2:

Baseado na análise, **NÃO recomendo o checkbox global**. Em vez disso, sugiro:

**Solução: Botão "+" Inline por Célula**
- Cada célula turno+setor+dia tem um botão "+" discreto
- Ao clicar, cria um novo slot imediatamente
- Visual limpo, ação contextual, sem mudança de modo

---

## 🏗️ Arquitetura da Solução

### Estrutura de Dados Atual

**Tabela**: `alocacoes_template`

```sql
CREATE TABLE alocacoes_template (
    id BIGINT PRIMARY KEY,
    escala_padrao_id BIGINT,
    semana INT,           -- 1-5
    dia INT,              -- 1-7
    turno_id BIGINT,
    setor_id BIGINT,
    slot INT,             -- número sequencial do slot
    plantonista_id BIGINT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(escala_padrao_id, semana, dia, turno_id, setor_id, slot)
);
```

**Campo Crítico**: `slot`
- Identifica cada vaga dentro de uma combinação (semana, dia, turno, setor)
- Ex: Se há 3 médicos em "Segunda, Manhã, UTI", teremos slot=1, slot=2, slot=3

---

## 🔧 Implementação Proposta

### Opção A: Botão "+" Inline (RECOMENDADA)

**Localização**: Dentro de cada célula da tabela, após os slots existentes

**Fluxo**:
1. Usuário clica no botão "+" em uma célula específica
2. Sistema identifica o próximo número de slot disponível
3. Cria um novo registro em `alocacoes_template` com `plantonista_id = NULL`
4. Recarrega a célula via AJAX (ou recarrega a página)
5. Novo slot vazio aparece na célula

**Vantagens**:
- ✅ Contextual: ação no local exato
- ✅ Intuitiva: não precisa explicar
- ✅ Rápida: 1 clique
- ✅ Segura: não afeta outros slots

**Desvantagens**:
- ⚠️ Precisa de AJAX para melhor UX
- ⚠️ Pode ficar visualmente poluído se muitos slots

---

### Opção B: Checkbox Global "Modo Aumentar Vagas" (SUA PROPOSTA INICIAL)

**Localização**: Toggle no topo da página

**Fluxo**:
1. Usuário ativa checkbox "Aumentar Vagas"
2. Sistema desabilita os selects de plantonistas
3. Usuário clica em qualquer slot
4. Sistema cria novo slot vazio naquela célula
5. Usuário desativa checkbox para voltar ao modo normal

**Vantagens**:
- ✅ Separação clara de modos (editar vs. aumentar)
- ✅ Previne cliques acidentais
- ✅ Pode adicionar vários slots sem trocar de modo

**Desvantagens**:
- ❌ Requer troca de modo (fricção extra)
- ❌ Usuário pode esquecer de desativar
- ❌ Menos intuitivo para usuários novos
- ❌ Clique em slot pode ser confuso (qual slot?)

---

### Opção C: Botão de Contexto no Modal (HÍBRIDA)

**Localização**: Dentro do modal de cada slot

**Fluxo**:
1. Usuário clica em um slot (abre modal)
2. Modal tem botão "Adicionar Outro Slot Aqui"
3. Sistema cria novo slot e fecha modal
4. Página recarrega mostrando novo slot

**Vantagens**:
- ✅ Confirmação implícita (usuário abre modal)
- ✅ Não precisa de modo especial
- ✅ Texto explicativo no botão

**Desvantagens**:
- ⚠️ Requer 2 cliques (abrir modal + clicar botão)
- ⚠️ Usuário precisa fechar modal para ver resultado

---

## 🎨 Design da Interface

### Opção A Implementada (RECOMENDADA)

```html
<td class="slot-cell">
    <!-- Slots existentes -->
    <div class="d-flex flex-column gap-1">
        <span class="slot-badge preenchido">Dr. João</span>
        <span class="slot-badge vago">Vago</span>
        
        <!-- Botão + inline -->
        <button class="btn btn-sm btn-outline-success btn-add-slot"
                data-semana="1"
                data-dia="1"
                data-turno="3"
                data-setor="5">
            <i class="bi bi-plus-circle"></i> Adicionar Vaga
        </button>
    </div>
</td>
```

**CSS**:
```css
.btn-add-slot {
    font-size: 0.75rem;
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    border-style: dashed;
    transition: all 0.2s;
}

.btn-add-slot:hover {
    background: rgba(25, 135, 84, 0.1);
    border-style: solid;
}
```

---

## 🔌 Backend - Rota e Controller

### Nova Rota

```php
// routes/web.php
Route::post('/escalas-publicadas/{escalaPublicada}/slots/add', 
    [EscalaPublicadaController::class, 'addSlot'])
    ->name('escalas-publicadas.slots.add');
```

### Método do Controller

```php
// app/Http/Controllers/EscalaPublicadaController.php

/**
 * Adiciona um novo slot vazio em uma célula específica
 * 
 * @param Request $request
 * @param EscalaPublicada $escalaPublicada
 * @return \Illuminate\Http\RedirectResponse
 */
public function addSlot(Request $request, EscalaPublicada $escalaPublicada)
{
    // Validação
    $validated = $request->validate([
        'semana' => 'required|integer|min:1|max:5',
        'dia' => 'required|integer|min:1|max:7',
        'turno_id' => 'required|exists:turnos,id',
        'setor_id' => 'required|exists:setores,id',
    ]);

    DB::beginTransaction();
    
    try {
        // Encontrar o próximo número de slot disponível
        $maxSlot = AlocacaoTemplate::where('escala_padrao_id', $escalaPublicada->escala_padrao_id)
            ->where('semana', $validated['semana'])
            ->where('dia', $validated['dia'])
            ->where('turno_id', $validated['turno_id'])
            ->where('setor_id', $validated['setor_id'])
            ->max('slot');
        
        $nextSlot = ($maxSlot ?? 0) + 1;
        
        // Criar novo slot vazio
        AlocacaoTemplate::create([
            'escala_padrao_id' => $escalaPublicada->escala_padrao_id,
            'semana' => $validated['semana'],
            'dia' => $validated['dia'],
            'turno_id' => $validated['turno_id'],
            'setor_id' => $validated['setor_id'],
            'slot' => $nextSlot,
            'plantonista_id' => null, // Vago
        ]);
        
        // Atualizar métricas da escala publicada
        $escalaPublicada->refresh();
        $escalaPublicada->recalcularMetricas(); // método helper
        
        DB::commit();
        
        return redirect()
            ->route('escalas-publicadas.edit', $escalaPublicada)
            ->with('success', 'Nova vaga adicionada com sucesso!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        return back()
            ->withErrors(['error' => 'Erro ao adicionar vaga: ' . $e->getMessage()]);
    }
}
```

---

## 🔄 Método Helper para Recalcular Métricas

```php
// app/Models/EscalaPublicada.php

/**
 * Recalcula as métricas da escala (total_slots, preenchidos, buracos, taxa)
 */
public function recalcularMetricas(): void
{
    $slots = AlocacaoTemplate::where('escala_padrao_id', $this->escala_padrao_id)->get();
    
    $this->total_slots = $slots->count();
    $this->preenchidos = $slots->whereNotNull('plantonista_id')->count();
    $this->buracos = $this->total_slots - $this->preenchidos;
    $this->taxa = $this->total_slots > 0 
        ? round(($this->preenchidos / $this->total_slots) * 100, 2) 
        : 0;
    
    $this->save();
}
```

---

## 🧪 Casos de Teste

### Teste 1: Adicionar Primeira Vaga
- **Cenário**: Célula sem slots (nova combinação turno+setor)
- **Entrada**: semana=1, dia=1, turno_id=3, setor_id=5
- **Esperado**: Criar slot=1, plantonista_id=NULL
- **Status**: ✅ Deve funcionar

### Teste 2: Adicionar Vaga em Célula com Slots Existentes
- **Cenário**: Célula já tem slot=1 e slot=2
- **Entrada**: mesma combinação
- **Esperado**: Criar slot=3
- **Status**: ✅ Deve funcionar

### Teste 3: Verificar Unique Constraint
- **Cenário**: Tentar criar slot=2 quando já existe
- **Entrada**: duplicar mesmo slot
- **Esperado**: Erro de unique constraint
- **Status**: ⚠️ Validação no backend previne

### Teste 4: Atualização de Métricas
- **Cenário**: Adicionar vaga e verificar total_slots
- **Entrada**: qualquer combinação
- **Esperado**: total_slots += 1, buracos += 1
- **Status**: ✅ Método recalcularMetricas()

---

## 📝 Validações e Regras de Negócio

1. **Limite Máximo de Slots**
   - Considerar limite de 10 slots por célula?
   - Evita criação descontrolada
   - Implementar: `max_slots_per_cell = 10` em config

2. **Permissões**
   - Apenas usuários autorizados podem adicionar vagas
   - Verificar middleware de autenticação

3. **Auditoria**
   - Registrar quem adicionou a vaga e quando
   - Adicionar campos `created_by`, `updated_by`

4. **Reversão**
   - Permitir remover vagas vazias?
   - Botão "🗑️" ao lado do slot vago

---

## 🚀 Roadmap de Implementação

### Fase 1: Backend (2-3 horas)
1. ✅ Criar rota `escalas-publicadas.slots.add`
2. ✅ Implementar método `addSlot()` no controller
3. ✅ Criar método `recalcularMetricas()` no model
4. ✅ Adicionar validações
5. ✅ Adicionar transação de banco

### Fase 2: Frontend (2-3 horas)
6. ✅ Adicionar botão "+" em cada célula da tabela
7. ✅ Estilizar botão (CSS dashed border)
8. ✅ Criar formulário inline com campos hidden
9. ✅ Testar em diferentes tamanhos de tela

### Fase 3: UX Avançada (Opcional - 3-4 horas)
10. 🔄 Implementar AJAX para adicionar sem reload
11. 🔄 Mostrar loading spinner durante criação
12. 🔄 Animação de fade-in para novo slot
13. 🔄 Toast notification de sucesso

### Fase 4: Refinamentos (1-2 horas)
14. 🔄 Adicionar confirmação antes de criar
15. 🔄 Limitar número máximo de slots
16. 🔄 Botão para remover vagas vazias
17. 🔄 Auditoria (created_by)

---

## ⚠️ Pontos de Atenção

### Críticos:
1. **Unique Constraint**: Garantir que `slot` seja sempre único na combinação
2. **Transaction**: Usar DB::transaction para evitar inconsistências
3. **Métricas**: Sempre recalcular após adicionar/remover slots

### Importantes:
4. **UX**: Feedback visual imediato após adicionar
5. **Performance**: Se muitas células, considerar lazy loading
6. **Mobile**: Botão "+" deve ser clicável em telas pequenas

### Desejáveis:
7. **Auditoria**: Log de quem adicionou cada vaga
8. **Notificações**: Email para coordenador quando vagas aumentam
9. **Relatórios**: Dashboard mostrando variação de vagas

---

## 🔗 Arquivos Relacionados

- **Migration**: `database/migrations/2025_10_21_235914_create_alocacoes_template_table.php`
- **Model**: `app/Models/AlocacaoTemplate.php`
- **Controller**: `app/Http/Controllers/EscalaPublicadaController.php`
- **View**: `resources/views/escalas-publicadas/edit.blade.php`
- **Routes**: `routes/web.php`

---

## 📚 Referências

- [Laravel Database Transactions](https://laravel.com/docs/11.x/database#database-transactions)
- [Bootstrap Icons](https://icons.getbootstrap.com/)
- [AJAX with Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch)

---

## 📄 Changelog

| Data | Versão | Alteração | Autor |
|------|--------|-----------|-------|
| 2025-10-24 | 1.0 | Documentação inicial da funcionalidade | Sistema |

---

**Status Final**: 📋 Documentação completa. Pronto para implementação.
