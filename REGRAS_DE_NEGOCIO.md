# 🏛️ REGRAS DE NEGÓCIO - EscalaMedica2

> **Referenciado por**: `REGISTRY.md` → "Políticas e Restrições"

---

## 🎯 REGRAS FUNDAMENTAIS

### **RN001: Unicidade de Plantonista**
- **Regra**: Cada plantonista pode ter apenas UMA alocação ativa por período de tempo
- **Validação**: Sistema impede criação de alocações conflitantes
- **Exceção**: Cancelamentos liberam o horário para nova alocação
- **Impacto**: Evita sobrecarga e garante qualidade do atendimento

### **RN002: Validação de Horários**
- **Regra**: Hora de início deve ser anterior à hora de fim
- **Validação**: Verificação automática no formulário e backend
- **Exceção**: Plantões noturnos podem cruzar a meia-noite
- **Impacto**: Consistência temporal das alocações

### **RN003: Integridade Referencial**
- **Regra**: Não é possível excluir registros referenciados por outros
- **Validação**: Verificação de dependências antes da exclusão
- **Exceção**: Exclusão em cascata quando apropriado
- **Impacto**: Mantém consistência dos dados

---

## 👥 REGRAS DE PLANTONISTAS

### **RN101: Registro Obrigatório**
- **Regra**: Plantonista deve ter nome, CRM e especialização
- **Validação**: Campos obrigatórios no formulário
- **Exceção**: CRM pode ser temporário para estagiários
- **Impacto**: Identificação única e rastreabilidade

### **RN102: CRM Único**
- **Regra**: Cada CRM pode estar associado a apenas um plantonista
- **Validação**: Verificação de unicidade no banco
- **Exceção**: Reativação de plantonistas inativos
- **Impacto**: Evita duplicidades e conflitos

### **RN103: Especialização Válida**
- **Regra**: Especialização deve ser reconhecida pelo sistema
- **Validação**: Lista predefinida de especializações
- **Exceção**: "Clínico Geral" como especialização padrão
- **Impacto**: Padronização e filtros adequados

---

## 🏥 REGRAS DE UNIDADES E SETORES

### **RN201: Unidade Ativa**
- **Regra**: Apenas unidades ativas podem receber alocações
- **Validação**: Status verificado durante criação de alocações
- **Exceção**: Emergências podem temporariamente reativar unidades
- **Impacto**: Evita alocações em locais indisponíveis

### **RN202: Setor Vinculado**
- **Regra**: Setor deve pertencer à unidade selecionada
- **Validação**: Verificação de relacionamento no formulário
- **Exceção**: Transferências internas podem alterar vinculação
- **Impacto**: Coerência organizacional

### **RN203: Capacidade Máxima**
- **Regra**: Setor não pode exceder número máximo de plantonistas por turno
- **Validação**: Contagem automática de alocações ativas
- **Exceção**: Situações de emergência podem aumentar capacidade
- **Impacto**: Evita superlotação e garante qualidade

---

## ⏰ REGRAS DE TURNOS

### **RN301: Duração Mínima**
- **Regra**: Turno deve ter duração mínima de 4 horas
- **Validação**: Cálculo automático entre hora início e fim
- **Exceção**: Turnos especiais podem ter durações menores
- **Impacto**: Produtividade e viabilidade operacional

### **RN302: Duração Máxima**
- **Regra**: Turno não pode exceder 24 horas consecutivas
- **Validação**: Limite máximo na interface e backend
- **Exceção**: Plantões especiais com aprovação específica
- **Impacto**: Saúde do profissional e qualidade do atendimento

### **RN303: Intervalo Entre Plantões**
- **Regra**: Mínimo de 12 horas entre fim de um plantão e início do próximo
- **Validação**: Verificação automática durante agendamento
- **Exceção**: Emergências podem reduzir intervalo com justificativa
- **Impacto**: Descanso adequado do profissional

---

## 📅 REGRAS DE ALOCAÇÕES

### **RN401: Data Futura**
- **Regra**: Alocações só podem ser criadas para datas futuras
- **Validação**: Comparação com data atual do sistema
- **Exceção**: Administradores podem criar alocações retroativas
- **Impacto**: Evita inconsistências temporais

### **RN402: Antecedência Mínima**
- **Regra**: Alocações devem ser criadas com mínimo 24h de antecedência
- **Validação**: Verificação de diferença temporal
- **Exceção**: Emergências podem criar alocações imediatas
- **Impacto**: Planejamento adequado dos profissionais

### **RN403: Cancelamento com Justificativa**
- **Regra**: Cancelamentos devem incluir motivo obrigatório
- **Validação**: Campo obrigatório no formulário de cancelamento
- **Exceção**: Cancelamentos automáticos por conflitos não requerem justificativa
- **Impacto**: Rastreabilidade e auditoria

---

## 🔄 REGRAS DE MARKETPLACE

### **RN501: Apenas Alocações Próprias**
- **Regra**: Plantonista só pode oferecer suas próprias alocações
- **Validação**: Verificação de propriedade antes da oferta
- **Exceção**: Administradores podem gerenciar todas as alocações
- **Impacto**: Segurança e controle de propriedade

### **RN502: Status Válido para Troca**
- **Regra**: Apenas alocações "Confirmadas" podem ser oferecidas
- **Validação**: Verificação de status durante criação da oferta
- **Exceção**: Ofertas podem ser feitas para alocações "Pendentes"
- **Impacto**: Evita trocas de alocações inválidas

### **RN503: Prazo para Aceite**
- **Regra**: Ofertas de troca expiram em 72 horas se não aceitas
- **Validação**: Verificação automática de tempo decorrido
- **Exceção**: Prazo pode ser estendido por acordo mútuo
- **Impacto**: Agilidade nas negociações

---

## 📊 REGRAS DE SCORE E AVALIAÇÃO

### **RN601: Cálculo Automático**
- **Regra**: Score é calculado automaticamente com base nos plantões realizados
- **Validação**: Atualização após conclusão de cada plantão
- **Exceção**: Scores podem ser ajustados manualmente por administradores
- **Impacto**: Transparência e meritocracia

### **RN602: Pontuação por Cumprimento**
- **Regra**: +10 pontos por plantão cumprido integralmente
- **Validação**: Verificação automática de conclusão
- **Exceção**: Plantões parciais recebem pontuação proporcional
- **Impacto**: Incentivo ao cumprimento integral

### **RN603: Penalização por Faltas**
- **Regra**: -20 pontos por plantão não cumprido sem justificativa
- **Validação**: Aplicação automática após prazo de tolerância
- **Exceção**: Faltas justificadas não geram penalização
- **Impacto**: Responsabilidade e confiabilidade

---

## 🔐 REGRAS DE SEGURANÇA

### **RN701: Dados Pessoais Protegidos**
- **Regra**: Informações pessoais só são visíveis para o próprio usuário e administradores
- **Validação**: Controle de acesso em todas as rotas
- **Exceção**: Dados públicos podem ser compartilhados (nome, especialização)
- **Impacto**: Privacidade e conformidade LGPD

### **RN702: Logs de Auditoria**
- **Regra**: Todas as operações críticas devem ser registradas
- **Validação**: Log automático de criação, edição e exclusão
- **Exceção**: Consultas simples não precisam ser logadas
- **Impacto**: Rastreabilidade e auditoria

### **RN703: Backup Automático**
- **Regra**: Dados devem ser salvos automaticamente a cada alteração
- **Validação**: Confirmação de salvamento antes de continuar
- **Exceção**: Rascunhos podem ser salvos temporariamente
- **Impacto**: Prevenção de perda de dados

---

## ⚡ REGRAS DE PERFORMANCE

### **RN801: Paginação Obrigatória**
- **Regra**: Listas com mais de 50 itens devem ser paginadas
- **Validação**: Implementação automática em controllers
- **Exceção**: Relatórios podem exibir todos os itens
- **Impacto**: Performance e usabilidade

### **RN802: Cache de Consultas**
- **Regra**: Consultas frequentes devem ser cacheadas por 5 minutos
- **Validação**: Implementação automática em queries pesadas
- **Exceção**: Dados críticos sempre consultam banco diretamente
- **Impacto**: Velocidade de resposta

### **RN803: Validação no Frontend**
- **Regra**: Validações básicas devem ocorrer no frontend antes do envio
- **Validação**: JavaScript preventivo em todos os formulários
- **Exceção**: Validações complexas apenas no backend
- **Impacto**: Experiência do usuário e redução de tráfego

---

## 🚨 REGRAS DE EXCEÇÃO

### **RN901: Modo Emergência**
- **Regra**: Administradores podem suspender regras em situações críticas
- **Validação**: Log especial para todas as exceções aplicadas
- **Exceção**: Apenas situações documentadas como emergência real
- **Impacto**: Flexibilidade para casos extremos

### **RN902: Aprovação Superior**
- **Regra**: Certas exceções requerem aprovação de nível hierárquico superior
- **Validação**: Workflow de aprovação implementado
- **Exceção**: Emergências médicas podem pular aprovações
- **Impacto**: Controle e responsabilidade

### **RN903: Revisão Posterior**
- **Regra**: Todas as exceções aplicadas devem ser revisadas em 7 dias
- **Validação**: Notificação automática para revisão
- **Exceção**: Exceções aprovadas podem ter prazo estendido
- **Impacto**: Aprendizado e melhoria contínua

---

## 📝 MATRIZ DE PRIORIDADES

| Tipo de Regra | Prioridade | Flexibilidade | Impacto no Sistema |
|----------------|------------|---------------|-------------------|
| **Segurança** | Crítica | Baixa | Alto |
| **Integridade** | Alta | Baixa | Alto |
| **Negócio** | Alta | Média | Médio |
| **Performance** | Média | Alta | Médio |
| **Usabilidade** | Média | Alta | Baixo |

---

*Regras de negócio completas do EscalaMedica2*
*Última atualização: 2024-12-28*
*Total de regras documentadas: 27*