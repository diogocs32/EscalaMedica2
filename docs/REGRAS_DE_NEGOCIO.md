# 📋 Regras de Negócio - EscalaMedica2

> **Objetivo**: Documentar todas as regras de negócio implementadas ou planejadas, explicando o "porquê" por trás de cada validação, cálculo, restrição ou fluxo funcional.

## 📊 Informações das Regras
- **Sistema**: EscalaMedica2
- **Total de Regras**: 25 (inicial)
- **Última Atualização**: 2025-10-20
- **Status**: Base inicial - expandir com implementações

---

## 📋 Índice
- [👤 Gestão de Usuários](#-gestão-de-usuários)
- [🏥 Gestão Hospitalar](#-gestão-hospitalar)
- [📅 Escalas e Plantões](#-escalas-e-plantões)
- [🩺 Atendimentos](#-atendimentos)
- [💰 Faturamento](#-faturamento)
- [🔐 Segurança e Acesso](#-segurança-e-acesso)
- [📊 Relatórios e Auditoria](#-relatórios-e-auditoria)

---

## 👤 Gestão de Usuários

### RN001 - Validação de CRM para Médicos
- **Regra**: Todo médico deve ter CRM válido e ativo no conselho regional
- **Justificativa**: Exigência legal para exercício da medicina no Brasil
- **Implementação**: Validação no cadastro via API do CFM
- **Validação**: Campo obrigatório, formato numérico, verificação online
- **Exceções**: Médicos estrangeiros com CRM provisório
- **Impacto**: Bloqueia criação de usuário médico sem CRM válido
- **Arquivo**: `app/Rules/ValidCrmRule.php`
- **Status**: 🚧 Planejado

### RN002 - Limite de Especialidades por Médico
- **Regra**: Um médico pode ter no máximo 3 especialidades registradas
- **Justificativa**: Evitar sobrecarga e garantir qualidade especializada
- **Implementação**: Validação no relacionamento User-Especialidade
- **Validação**: Verificar count() antes de adicionar nova especialidade
- **Exceções**: Residentes podem ter mais especialidades temporariamente
- **Impacto**: Interface limitará seleção de especialidades
- **Arquivo**: `app/Models/User.php` método `addEspecialidade()`
- **Status**: 🚧 Planejado

### RN003 - Senha Forte Obrigatória
- **Regra**: Senhas devem ter 8+ caracteres, maiúscula, minúscula, número e símbolo
- **Justificativa**: Segurança de dados médicos sensíveis (LGPD)
- **Implementação**: Validação personalizada no FormRequest
- **Validação**: Regex pattern + verificação de força
- **Exceções**: Nenhuma
- **Impacto**: Usuário deve criar senha segura obrigatoriamente
- **Arquivo**: `app/Rules/StrongPasswordRule.php`
- **Status**: 🚧 Planejado

### RN004 - Bloqueio de Usuário Inativo
- **Regra**: Usuário sem login por 90 dias é automaticamente bloqueado
- **Justificativa**: Segurança e conformidade com políticas hospitalares
- **Implementação**: Job diário que verifica last_login_at
- **Validação**: Comparar data atual com último login
- **Exceções**: Médicos em licença médica ficam isentos
- **Impacto**: Usuário precisa solicitar desbloqueio ao admin
- **Arquivo**: `app/Jobs/CheckInactiveUsersJob.php`
- **Status**: 🚧 Planejado

---

## 🏥 Gestão Hospitalar

### RN005 - Capacidade Máxima de Leitos por Setor
- **Regra**: Cada setor tem capacidade máxima de leitos definida na criação
- **Justificativa**: Limitação física e regulamentação sanitária
- **Implementação**: Validação before create no modelo Leito
- **Validação**: count(leitos) < setor.capacidade_maxima
- **Exceções**: UTI pode ter leitos extras em emergências (aprovação diretor)
- **Impacto**: Sistema bloqueia criação de leitos excedentes
- **Arquivo**: `app/Models/Leito.php` observer `creating`
- **Status**: 🚧 Planejado

### RN006 - Leito Ocupado Não Pode Ser Excluído
- **Regra**: Leito com paciente internado não pode ser removido do sistema
- **Justificativa**: Preservar histórico médico e evitar inconsistências
- **Implementação**: Soft delete apenas, verificar status ocupação
- **Validação**: if (leito.status === 'ocupado') throw exception
- **Exceções**: Admin pode forçar exclusão com justificativa
- **Impacto**: Usuário deve primeiro dar alta/transferir paciente
- **Arquivo**: `app/Models/Leito.php` método `delete()`
- **Status**: 🚧 Planejado

### RN007 - Transferência de Paciente Entre Setores
- **Regra**: Transferência só é permitida se setor destino tiver vaga
- **Justificativa**: Gestão eficiente de capacidade hospitalar
- **Implementação**: Verificar vagas antes de criar transferência
- **Validação**: setor_destino.vagas_disponiveis > 0
- **Exceções**: Emergências podem ser forçadas com aprovação médica
- **Impacto**: Sistema sugere setores com vagas disponíveis
- **Arquivo**: `app/Services/TransferenciaService.php`
- **Status**: 🚧 Planejado

---

## 📅 Escalas e Plantões

### RN008 - Médico Não Pode Ter Plantões Sobrepostos
- **Regra**: Um médico não pode estar escalado em dois plantões simultaneamente
- **Justificativa**: Impossibilidade física e responsabilidade médica
- **Implementação**: Validação de conflito de horários no agendamento
- **Validação**: Verificar intersecção de data/hora antes de salvar
- **Exceções**: Sobreaviso pode sobrepor com plantão regular
- **Impacto**: Sistema alertará sobre conflito e impedirá duplicação
- **Arquivo**: `app/Services/EscalaService.php` método `validarConflitos()`
- **Status**: 🚧 Planejado

### RN009 - Limite de Horas Mensais por Médico
- **Regra**: Médico não pode ultrapassar 240 horas de plantão por mês
- **Justificativa**: Legislação trabalhista e qualidade do atendimento
- **Implementação**: Soma horas do mês antes de adicionar novo plantão
- **Validação**: soma_horas_mes + horas_novo_plantao <= 240
- **Exceções**: Plantões extras podem ser aprovados pela direção
- **Impacto**: Sistema impedirá agendamento que exceda limite
- **Arquivo**: `app/Models/Plantao.php` método `validarLimiteHoras()`
- **Status**: 🚧 Planejado

### RN010 - Intervalo Mínimo Entre Plantões
- **Regra**: Deve haver no mínimo 12 horas entre o fim de um plantão e início do próximo
- **Justificativa**: Descanso médico obrigatório para segurança do paciente
- **Implementação**: Validar diferença entre fim e início de plantões
- **Validação**: proximo_inicio - plantao_anterior.fim >= 12 horas
- **Exceções**: Emergências podem reduzir para 8 horas com aprovação
- **Impacto**: Sistema sugerirá horários respeitando intervalo
- **Arquivo**: `app/Rules/IntervaloPlantaoRule.php`
- **Status**: 🚧 Planejado

### RN011 - Escala Deve Ter Cobertura Completa
- **Regra**: Todo período (24/7) deve ter ao menos um médico de cada especialidade crítica
- **Justificativa**: Garantir atendimento contínuo em emergências
- **Implementação**: Validação na finalização da escala mensal
- **Validação**: Verificar gaps de cobertura por especialidade/período
- **Exceções**: Feriados podem ter cobertura reduzida
- **Impacto**: Escala não pode ser aprovada sem cobertura completa
- **Arquivo**: `app/Services/ValidacaoEscalaService.php`
- **Status**: 🚧 Planejado

---

## 🩺 Atendimentos

### RN012 - Triagem Obrigatória na Emergência
- **Regra**: Todo paciente da emergência deve passar por triagem antes do atendimento
- **Justificativa**: Protocolo Manchester de classificação de risco
- **Implementação**: Status obrigatório no fluxo de emergência
- **Validação**: Não permitir atendimento sem classificação de risco
- **Exceções**: Parada cardíaca vai direto para atendimento
- **Impacto**: Enfermeiro deve classificar antes de encaminhar
- **Arquivo**: `app/Models/AtendimentoEmergencia.php`
- **Status**: 🚧 Planejado

### RN013 - Tempo Máximo por Classificação de Risco
- **Regra**: Cada cor tem tempo máximo para primeiro atendimento médico
- **Justificativa**: Protocolo de segurança e qualidade assistencial
- **Implementação**: Timer automático com alertas
- **Tempos**: Azul (240min), Verde (120min), Amarelo (60min), Laranja (10min), Vermelho (imediato)
- **Exceções**: Pacientes podem aguardar mais se não houver médico disponível
- **Impacto**: Sistema alertará enfermagem sobre tempos vencidos
- **Arquivo**: `app/Services/TriagemService.php`
- **Status**: 🚧 Planejado

### RN014 - Médico Só Atende Sua Especialidade
- **Regra**: Médico só pode criar consultas/atendimentos de suas especialidades
- **Justificativa**: Responsabilidade profissional e qualidade técnica
- **Implementação**: Verificar especialidades do médico ao agendar
- **Validação**: medico.especialidades.contains(consulta.especialidade)
- **Exceções**: Emergências permitem atendimento fora da especialidade
- **Impacto**: Interface filtra apenas especialidades do médico logado
- **Arquivo**: `app/Policies/ConsultaPolicy.php`
- **Status**: 🚧 Planejado

### RN015 - Consulta Não Pode Ser Agendada No Passado
- **Regra**: Data/hora da consulta deve ser futura em relação ao momento do agendamento
- **Justificativa**: Lógica temporal básica do sistema
- **Implementação**: Validação de data no FormRequest
- **Validação**: consulta.data_agendamento > now()
- **Exceções**: Admin pode agendar no passado para regularização
- **Impacto**: Interface bloqueia seleção de datas passadas
- **Arquivo**: `app/Requests/StoreConsultaRequest.php`
- **Status**: 🚧 Planejado

---

## 💰 Faturamento

### RN016 - Convênio Deve Cobrir Procedimento
- **Regra**: Procedimento só pode ser faturado se estiver coberto pelo convênio do paciente
- **Justificativa**: Evitar glosas e problemas financeiros
- **Implementação**: Validar cobertura antes de registrar procedimento
- **Validação**: convenio.procedimentos_cobertos.contains(procedimento.codigo)
- **Exceções**: Particular pode realizar qualquer procedimento
- **Impacto**: Sistema alertará sobre procedimentos não cobertos
- **Arquivo**: `app/Services/FaturamentoService.php`
- **Status**: 🚧 Planejado

### RN017 - Limite de Autorização para Procedimentos
- **Regra**: Procedimentos acima de R$ 1.000 precisam de autorização prévia do convênio
- **Justificativa**: Controle financeiro e redução de glosas
- **Implementação**: Verificar valor antes de executar procedimento
- **Validação**: if (procedimento.valor > 1000) require autorização
- **Exceções**: Emergências podem ser feitas sem autorização
- **Impacto**: Sistema solicitará número de autorização
- **Arquivo**: `app/Models/Procedimento.php` método `requireAutorizacao()`
- **Status**: 🚧 Planejado

### RN018 - Prazo para Fechamento de Faturamento
- **Regra**: Faturamento mensal deve ser fechado até o 5º dia útil do mês seguinte
- **Justificativa**: Fluxo de caixa e acordos com convênios
- **Implementação**: Job automático + notificações
- **Validação**: Verificar se data atual > prazo limite
- **Exceções**: Diretor pode estender prazo em casos excepcionais
- **Impacto**: Sistema impedirá alterações após fechamento
- **Arquivo**: `app/Jobs/FechamentoFaturamentoJob.php`
- **Status**: 🚧 Planejado

---

## 🔐 Segurança e Acesso

### RN019 - Log de Auditoria Obrigatório
- **Regra**: Todas as ações sensíveis devem ser registradas em log de auditoria
- **Justificativa**: Rastreabilidade para auditorias e conformidade LGPD
- **Implementação**: Observer em models críticos + middleware
- **Ações**: Create, Update, Delete, View de dados médicos
- **Dados**: user_id, action, model, old_values, new_values, timestamp
- **Impacto**: Pequeno overhead de performance
- **Arquivo**: `app/Observers/AuditLogObserver.php`
- **Status**: 🚧 Planejado

### RN020 - Acesso a Dados Por Setor
- **Regra**: Usuário só pode acessar dados de pacientes do seu setor de atuação
- **Justificativa**: Privacidade médica e princípio do menor privilégio
- **Implementação**: Policy com verificação de setor
- **Validação**: user.setores.contains(paciente.setor_atual)
- **Exceções**: Médicos assistentes podem acessar qualquer setor
- **Impacto**: Queries filtradas automaticamente por setor
- **Arquivo**: `app/Policies/PacientePolicy.php`
- **Status**: 🚧 Planejado

### RN021 - Sessão Expira Por Inatividade
- **Regra**: Sessão do usuário expira após 30 minutos de inatividade
- **Justificativa**: Segurança em computadores compartilhados
- **Implementação**: Configuração de session timeout + middleware
- **Validação**: Verificar last_activity vs current_time
- **Exceções**: Médicos em atendimento podem ter sessão estendida
- **Impacto**: Usuário será deslogado automaticamente
- **Arquivo**: `config/session.php` + `app/Middleware/CheckSessionTimeout.php`
- **Status**: 🚧 Planejado

---

## 📊 Relatórios e Auditoria

### RN022 - Relatórios Sensíveis Requerem Aprovação
- **Regra**: Relatórios com dados financeiros ou estatísticas críticas precisam de aprovação do diretor
- **Justificativa**: Controle de informações estratégicas
- **Implementação**: Workflow de aprovação para relatórios específicos
- **Validação**: Verificar tipo de relatório vs permissões do usuário
- **Tipos Sensíveis**: Faturamento, produtividade médica, custos
- **Impacto**: Relatório fica pendente até aprovação
- **Arquivo**: `app/Services/RelatorioService.php`
- **Status**: 🚧 Planejado

### RN023 - Backup Automático de Dados Críticos
- **Regra**: Backup automático diário dos dados de pacientes e faturamento
- **Justificativa**: Continuidade do negócio e recuperação de desastres
- **Implementação**: Job noturno que faz backup incremental
- **Frequência**: Diário às 02:00h
- **Retenção**: 30 dias local + 365 dias na nuvem
- **Impacto**: Pequeno impacto na performance noturna
- **Arquivo**: `app/Jobs/BackupDadosJob.php`
- **Status**: 🚧 Planejado

### RN024 - Alertas Automáticos de Anomalias
- **Regra**: Sistema deve alertar automaticamente sobre padrões anômalos
- **Justificativa**: Detecção precoce de problemas e fraudes
- **Anomalias**: Picos de internação, uso excessivo de recursos, padrões de faturamento
- **Implementação**: Análise estatística + machine learning básico
- **Impacto**: Notificações para gestores sobre situações atípicas
- **Arquivo**: `app/Services/DeteccaoAnomaliaService.php`
- **Status**: 🚧 Planejado

### RN025 - Retenção de Logs Conforme LGPD
- **Regra**: Logs pessoais devem ser anonimizados após 2 anos, logs de auditoria mantidos por 5 anos
- **Justificativa**: Conformidade com LGPD e necessidades regulatórias
- **Implementação**: Job mensal que anonimiza/remove dados antigos
- **Anonimização**: Substituir dados pessoais por hash irreversível
- **Exceções**: Processos judiciais podem requerer retenção maior
- **Impacto**: Histórico antigo fica disponível mas anonimizado
- **Arquivo**: `app/Jobs/LimpezaLogsJob.php`
- **Status**: 🚧 Planejado

---

## 🔄 Gestão das Regras de Negócio

### 📝 Como Adicionar Nova Regra

#### Template para Nova Regra:
```markdown
### RN### - Nome da Regra
- **Regra**: Descrição clara e objetiva da regra
- **Justificativa**: Por que esta regra existe (legal, operacional, técnica)
- **Implementação**: Como será implementada tecnicamente
- **Validação**: Como verificar se a regra está sendo cumprida
- **Exceções**: Casos onde a regra pode ser flexibilizada
- **Impacto**: Como afeta a experiência do usuário
- **Arquivo**: Onde no código a regra será implementada
- **Status**: 🚧 Planejado | ✅ Implementado | ❌ Removido
```

### 🔍 Classificação de Regras

#### Por Criticidade:
- 🔴 **Crítica**: Impacta segurança ou legalidade
- 🟡 **Importante**: Afeta operação ou qualidade
- 🟢 **Desejável**: Melhora experiência ou eficiência

#### Por Origem:
- ⚖️ **Legal**: Exigida por lei ou regulamentação
- 🏥 **Médica**: Protocolo médico ou segurança do paciente
- 💼 **Operacional**: Processo interno do hospital
- 🔧 **Técnica**: Limitação ou padrão tecnológico

### ✅ Checklist de Implementação

#### Antes de Implementar:
- [ ] Regra está documentada neste arquivo
- [ ] Justificativa está clara
- [ ] Impacto foi avaliado
- [ ] Exceções foram consideradas
- [ ] Aprovação do Product Owner

#### Durante Implementação:
- [ ] Validação implementada corretamente
- [ ] Testes unitários criados
- [ ] Mensagens de erro são claras
- [ ] Logs de auditoria configurados
- [ ] Documentação técnica atualizada

#### Após Implementação:
- [ ] Testes funcionais passando
- [ ] Performance não foi impactada negativamente
- [ ] Usuários foram treinados (se necessário)
- [ ] Status atualizado para ✅ Implementado
- [ ] REGISTRY.md atualizado

### 🔄 Evolução das Regras

#### Alteração de Regra Existente:
1. **Avaliação**: Impacto da mudança
2. **Aprovação**: Stakeholders + Product Owner
3. **Versionamento**: Manter histórico da alteração
4. **Migração**: Se dados existentes são afetados
5. **Comunicação**: Informar usuários sobre mudança

#### Remoção de Regra:
1. **Justificativa**: Por que não é mais necessária
2. **Verificação**: Nenhuma dependência técnica
3. **Backup**: Manter histórico da regra removida
4. **Status**: Alterar para ❌ Removido
5. **Limpeza**: Remover código relacionado

---

## 📞 Contatos e Responsabilidades

### 👥 Responsáveis por Tipo de Regra

- **Regras Médicas**: Dr. [Nome] - Diretor Médico
- **Regras Legais**: [Nome] - Compliance/Jurídico
- **Regras Operacionais**: [Nome] - Gerente Operacional
- **Regras Técnicas**: [Nome] - Tech Lead

### 🆘 Processo de Exceção

1. **Solicitação**: Usuário solicita exceção via sistema
2. **Avaliação**: Responsável pela regra avalia pedido
3. **Aprovação**: Superior hierárquico aprova/rejeita
4. **Execução**: Sistema permite ação com log especial
5. **Auditoria**: Exceção é registrada para revisão

---

**📍 Última atualização**: 2025-10-20  
**👥 Responsáveis**: Product Owner + Tech Lead + Direção Médica  
**📋 Status**: Base inicial - expandir com cada nova funcionalidade  
**🔄 Próxima revisão**: A cada sprint ou implementação significativa

> **⚠️ IMPORTANTE**: Toda regra deve ter implementação técnica correspondente e rastreabilidade completa!