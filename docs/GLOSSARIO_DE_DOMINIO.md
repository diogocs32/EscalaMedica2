# 📚 Glossário de Domínio - EscalaMedica2

> **Objetivo**: Padronizar termos e conceitos utilizados no sistema médico, evitando ambiguidades e garantindo comunicação clara entre equipe técnica, stakeholders e usuários finais.

## 📊 Informações do Glossário
- **Sistema**: EscalaMedica2
- **Domínio**: Gestão Médica e Hospitalar
- **Última Atualização**: 2025-10-20
- **Total de Termos**: 15 (inicial)

---

## 📋 Índice
- [👥 Personas e Usuários](#-personas-e-usuários)
- [🏥 Entidades Médicas](#-entidades-médicas)
- [📅 Gestão de Escalas](#-gestão-de-escalas)
- [📋 Procedimentos e Atendimentos](#-procedimentos-e-atendimentos)
- [💼 Gestão Administrativa](#-gestão-administrativa)
- [🔧 Termos Técnicos](#-termos-técnicos)
- [📊 Métricas e Indicadores](#-métricas-e-indicadores)

---

## 👥 Personas e Usuários

### 👨‍⚕️ Médico
- **Definição Funcional**: Profissional de saúde responsável pelo diagnóstico, tratamento e acompanhamento de pacientes
- **Definição Técnica**: Usuário do sistema com perfil `medico` e permissões específicas para escalas e atendimentos
- **Atributos**: CRM, especialidade, status_ativo, escalas_atribuidas
- **Exemplo de Uso**: "O médico João Silva (CRM 12345) está escalado para o plantão noturno"
- **Sinônimos**: Doutor, Profissional Médico
- **Relacionamentos**: Pertence a uma ou mais especialidades, tem múltiplas escalas

### 👩‍⚕️ Enfermeiro
- **Definição Funcional**: Profissional de enfermagem responsável pelos cuidados diretos aos pacientes
- **Definição Técnica**: Usuário com perfil `enfermeiro` e acesso a módulos de cuidados e procedimentos
- **Atributos**: COREN, turno_preferencial, setor_atuacao
- **Exemplo de Uso**: "A enfermeira Maria está responsável pela UTI no turno da manhã"
- **Relacionamentos**: Atua em setores específicos, participa de escalas

### 🧑‍💼 Administrador
- **Definição Funcional**: Responsável pela gestão administrativa e configuração do sistema
- **Definição Técnica**: Usuário com perfil `admin` e acesso total às funcionalidades
- **Atributos**: nivel_acesso, modulos_gerenciados
- **Exemplo de Uso**: "O administrador configurou as escalas do mês"
- **Relacionamentos**: Gerencia usuários e configurações

### 🏥 Paciente
- **Definição Funcional**: Pessoa que recebe atendimento médico ou está internada
- **Definição Técnica**: Entidade central do sistema com dados pessoais e histórico médico
- **Atributos**: CPF, nome_completo, data_nascimento, convenio, status_internacao
- **Exemplo de Uso**: "Paciente José Santos foi internado na enfermaria"
- **Sinônimos**: Cliente, Usuário Final
- **Relacionamentos**: Tem múltiplas consultas, pode ter internações

---

## 🏥 Entidades Médicas

### 🏢 Hospital
- **Definição Funcional**: Instituição de saúde onde ocorrem os atendimentos
- **Definição Técnica**: Entidade principal que agrupa setores, usuários e equipamentos
- **Atributos**: CNPJ, razao_social, endereco, capacidade_leitos
- **Exemplo de Uso**: "Hospital São Lucas tem 200 leitos disponíveis"
- **Relacionamentos**: Contém múltiplos setores e usuários

### 🏥 Setor
- **Definição Funcional**: Divisão específica do hospital (UTI, Emergência, Enfermaria)
- **Definição Técnica**: Unidade organizacional que agrupa leitos e profissionais
- **Atributos**: nome, tipo_setor, capacidade, status_operacional
- **Exemplo de Uso**: "Setor UTI tem 10 leitos, 8 ocupados"
- **Sinônimos**: Ala, Departamento, Unidade
- **Relacionamentos**: Pertence a um hospital, tem múltiplos leitos

### 🛏️ Leito
- **Definição Funcional**: Espaço físico destinado à internação de um paciente
- **Definição Técnica**: Recurso físico com status de ocupação e equipamentos
- **Atributos**: numero, tipo_leito, status_ocupacao, equipamentos_disponiveis
- **Exemplo de Uso**: "Leito 15 da UTI está ocupado pelo paciente João"
- **Estados**: ocupado, livre, manutencao, bloqueado
- **Relacionamentos**: Pertence a um setor, pode ter um paciente

### 🩺 Especialidade
- **Definição Funcional**: Área específica de atuação médica
- **Definição Técnica**: Categoria que define competências e permissões médicas
- **Atributos**: nome, codigo_cbhpm, descricao
- **Exemplo de Uso**: "Cardiologia é uma especialidade do Dr. João"
- **Relacionamentos**: Médicos têm especialidades, consultas requerem especialidades

---

## 📅 Gestão de Escalas

### 📋 Escala
- **Definição Funcional**: Cronograma de trabalho dos profissionais de saúde
- **Definição Técnica**: Estrutura que define quem trabalha quando e onde
- **Atributos**: data_inicio, data_fim, tipo_escala, status_aprovacao
- **Exemplo de Uso**: "Escala de dezembro foi aprovada com 30 médicos"
- **Tipos**: mensal, semanal, plantao_extra
- **Estados**: rascunho, aprovada, publicada, finalizada

### ⏰ Plantão
- **Definição Funcional**: Período específico de trabalho de um profissional
- **Definição Técnica**: Unidade mínima de tempo de trabalho com responsabilidades
- **Atributos**: data, hora_inicio, hora_fim, tipo_plantao, profissional_id
- **Exemplo de Uso**: "Plantão noturno de 19h às 7h na emergência"
- **Tipos**: diurno, noturno, sobreaviso, extra
- **Relacionamentos**: Pertence a uma escala, tem um profissional responsável

### 🔄 Troca de Plantão
- **Definição Funcional**: Substituição de um profissional por outro em um plantão
- **Definição Técnica**: Processo de alteração de responsabilidade com aprovações
- **Atributos**: plantao_original, profissional_origem, profissional_destino, motivo
- **Exemplo de Uso**: "Dr. João trocou plantão com Dr. Pedro por motivo pessoal"
- **Estados**: solicitada, aprovada, rejeitada, efetivada

---

## 📋 Procedimentos e Atendimentos

### 🩺 Consulta
- **Definição Funcional**: Atendimento médico ambulatorial agendado
- **Definição Técnica**: Evento de atendimento com data, profissional e paciente
- **Atributos**: data_agendamento, medico_id, paciente_id, tipo_consulta, status
- **Exemplo de Uso**: "Consulta de cardiologia agendada para 15/11/2025"
- **Tipos**: primeira_vez, retorno, urgencia, teleconsulta
- **Estados**: agendada, em_andamento, concluida, cancelada

### 🚑 Atendimento de Emergência
- **Definição Funcional**: Atendimento médico não agendado de caráter urgente
- **Definição Técnica**: Evento de prioridade alta com triagem e classificação
- **Atributos**: data_entrada, classificacao_risco, medico_responsavel
- **Exemplo de Uso**: "Paciente chegou na emergência com classificação vermelha"
- **Classificações**: verde, amarelo, laranja, vermelho, azul
- **Relacionamentos**: Pode gerar internação ou alta

### 🏥 Internação
- **Definição Funcional**: Permanência do paciente no hospital por período prolongado
- **Definição Técnica**: Estado do paciente com leito atribuído e cuidados contínuos
- **Atributos**: data_internacao, leito_id, medico_responsavel, motivo
- **Exemplo de Uso**: "Paciente internado na UTI por complicação cardíaca"
- **Estados**: ativa, alta_medica, alta_administrativa, transferencia

---

## 💼 Gestão Administrativa

### 📄 Convênio
- **Definição Funcional**: Acordo comercial entre hospital e operadora de saúde
- **Definição Técnica**: Entidade que define cobertura e regras de faturamento
- **Atributos**: nome_operadora, codigo_ans, tipos_cobertura, status_ativo
- **Exemplo de Uso**: "Paciente atendido pelo convênio Unimed"
- **Relacionamentos**: Pacientes têm convênios, procedimentos têm cobertura

### 💰 Faturamento
- **Definição Funcional**: Processo de cobrança dos serviços prestados
- **Definição Técnica**: Agregação de procedimentos em lotes para cobrança
- **Atributos**: periodo_referencia, valor_total, status_envio, convenio_id
- **Exemplo de Uso**: "Faturamento de outubro totaliza R$ 150.000"
- **Estados**: em_preparacao, enviado, pago, contestado

---

## 🔧 Termos Técnicos

### 🔐 Perfil de Acesso
- **Definição Funcional**: Conjunto de permissões que define o que um usuário pode fazer
- **Definição Técnica**: Role-based access control (RBAC) implementado no sistema
- **Atributos**: nome_perfil, permissoes[], modulos_acessiveis[]
- **Exemplo de Uso**: "Médico tem perfil que permite criar consultas"
- **Tipos**: admin, medico, enfermeiro, recepcao, financeiro

### 📊 Dashboard
- **Definição Funcional**: Painel visual com informações gerenciais do sistema
- **Definição Técnica**: Interface que agrega métricas e KPIs em tempo real
- **Componentes**: widgets, graficos, alertas, indicadores
- **Exemplo de Uso**: "Dashboard mostra 85% de ocupação dos leitos"

### 🔔 Notificação
- **Definição Funcional**: Comunicação automática do sistema para usuários
- **Definição Técnica**: Evento disparado por regras de negócio específicas
- **Tipos**: email, sms, push_notification, in_app
- **Exemplo de Uso**: "Notificação enviada sobre vencimento de plantão"

---

## 📊 Métricas e Indicadores

### 📈 Taxa de Ocupação
- **Definição Funcional**: Percentual de leitos ocupados em relação ao total
- **Fórmula**: (Leitos Ocupados / Total de Leitos) × 100
- **Exemplo**: "Taxa de ocupação da UTI: 80%"
- **Meta**: Manter entre 70-85% para eficiência operacional

### ⏱️ Tempo Médio de Atendimento
- **Definição Funcional**: Tempo médio gasto em consultas ou procedimentos
- **Fórmula**: Soma dos tempos / Número de atendimentos
- **Exemplo**: "Tempo médio de consulta cardiológica: 45 minutos"
- **Meta**: Otimizar sem comprometer qualidade

### 🎯 Satisfação do Paciente
- **Definição Funcional**: Indicador da qualidade percebida pelos pacientes
- **Medição**: Escala de 1-5 ou NPS (Net Promoter Score)
- **Exemplo**: "Satisfação média: 4.2/5.0"
- **Meta**: Manter acima de 4.0

---

## 🔄 Convenções de Uso

### 📝 Como Usar Este Glossário

#### Para Desenvolvedores:
1. **Antes de nomear**: Consulte se já existe termo similar
2. **Ao criar entidades**: Use a definição técnica como base
3. **Em documentação**: Referencie os termos padronizados
4. **Em código**: Use os mesmos nomes de atributos definidos

#### Para Product Owners:
1. **Em requisitos**: Use apenas termos do glossário
2. **Em discussões**: Esclareça ambiguidades consultando definições
3. **Em validações**: Verifique se implementação segue as definições

#### Para QA:
1. **Em testes**: Use cenários baseados nas definições funcionais
2. **Em validações**: Confirme se comportamento está conforme glossário
3. **Em bugs**: Referencie os termos corretos nas descrições

### ➕ Como Adicionar Novos Termos

```markdown
### 🔹 Nome do Termo
- **Definição Funcional**: O que significa para o usuário/negócio
- **Definição Técnica**: Como é implementado no sistema
- **Atributos**: campos, propriedades, características
- **Exemplo de Uso**: Situação real de aplicação
- **Sinônimos**: Outros nomes (evitar usar)
- **Estados**: Se aplicável (ativo, inativo, etc.)
- **Relacionamentos**: Como se conecta com outras entidades
```

### 🚫 Termos a Evitar (Ambíguos)

- **"Cliente"** → Use "Paciente" ou "Convênio" conforme contexto
- **"Usuário"** → Use "Médico", "Enfermeiro", "Administrador" ou "Paciente"
- **"Registro"** → Use "Consulta", "Internação", "Procedimento" conforme contexto
- **"Item"** → Use o nome específico da entidade

---

## 📞 Manutenção do Glossário

### ✅ Checklist de Atualização
- [ ] Novo termo tem definição funcional e técnica
- [ ] Exemplos são claros e aplicáveis
- [ ] Não há conflito com termos existentes
- [ ] Relacionamentos estão documentados
- [ ] Atributos técnicos estão alinhados com implementação

### 🔄 Processo de Evolução
1. **Identificação**: Novo termo surge em discussão/desenvolvimento
2. **Definição**: Product Owner + Tech Lead definem significado
3. **Documentação**: Adicionar ao glossário seguindo template
4. **Comunicação**: Informar equipe sobre novo termo
5. **Validação**: Revisar uso em próximas implementações

---

**📍 Última atualização**: 2025-10-20  
**👥 Responsáveis**: Product Owner + Tech Lead  
**📋 Status**: Base inicial - expandir conforme necessidade do domínio  
**🔄 Próxima revisão**: A cada nova funcionalidade implementada

> **💡 Dica**: Use Ctrl+F para buscar rapidamente termos específicos!