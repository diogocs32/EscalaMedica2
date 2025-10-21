# 🔄 Fluxos Funcionais - EscalaMedica2

> **Objetivo**: Documentar os fluxos principais do sistema, mostrando a sequência de ações, decisões e integrações entre módulos para facilitar desenvolvimento, testes e auditoria.

## 📊 Informações dos Fluxos
- **Sistema**: EscalaMedica2
- **Total de Fluxos**: 8 principais
- **Última Atualização**: 2025-10-20
- **Formato**: Diagrama textual + BPMN descritivo

---

## 📋 Índice
- [🏥 Fluxos de Admissão](#-fluxos-de-admissão)
- [📅 Fluxos de Escalas](#-fluxos-de-escalas)
- [🩺 Fluxos de Atendimento](#-fluxos-de-atendimento)
- [💰 Fluxos Financeiros](#-fluxos-financeiros)
- [🔐 Fluxos de Segurança](#-fluxos-de-segurança)
- [📊 Fluxos de Gestão](#-fluxos-de-gestão)

---

## 🏥 Fluxos de Admissão

### 🚪 F001 - Cadastro de Paciente

```mermaid
flowchart TD
    A[Recepção inicia cadastro] --> B{Paciente existe?}
    B -->|Sim| C[Atualizar dados]
    B -->|Não| D[Novo cadastro]
    
    D --> E[Validar CPF]
    E --> F{CPF válido?}
    F -->|Não| G[Solicitar documento alternativo]
    F -->|Sim| H[Inserir dados pessoais]
    
    H --> I[Selecionar convênio]
    I --> J{Convênio válido?}
    J -->|Não| K[Configurar particular]
    J -->|Sim| L[Validar carteirinha]
    
    C --> M[Dados atualizados]
    K --> M
    L --> M
    
    M --> N[Gerar pulseira]
    N --> O[Paciente cadastrado]
    
    G --> P[Cadastro manual]
    P --> H
```

#### Detalhes do Fluxo:
- **Ator Principal**: Recepcionista
- **Pré-condições**: Sistema funcionando, recepcionista autenticado
- **Pós-condições**: Paciente cadastrado com número único
- **Tempo Médio**: 3-5 minutos
- **Sistemas Integrados**: SUS (validação CPF), Convênios (validação carteirinha)

#### Regras Aplicadas:
- RN003: Validação de CPF obrigatória
- RN016: Verificar cobertura do convênio
- RN019: Log de auditoria para cadastros

#### Pontos de Falha:
- API do SUS indisponível → Permitir cadastro manual
- Sistema de convênio offline → Marcar para validação posterior
- Duplicação de paciente → Merge automático com confirmação

---

### 🏥 F002 - Internação de Paciente

```mermaid
flowchart TD
    A[Médico solicita internação] --> B[Verificar leitos disponíveis]
    B --> C{Há vaga no setor?}
    C -->|Não| D[Listar setores alternativos]
    C -->|Sim| E[Reservar leito]
    
    D --> F{Aceita alternativa?}
    F -->|Não| G[Entrar em fila de espera]
    F -->|Sim| E
    
    E --> H[Validar autorização convênio]
    H --> I{Autorização OK?}
    I -->|Não| J[Solicitar autorização]
    I -->|Sim| K[Confirmar internação]
    
    J --> L{Autorização recebida?}
    L -->|Não| M[Cancelar internação]
    L -->|Sim| K
    
    K --> N[Atualizar status leito]
    N --> O[Gerar pulseira]
    O --> P[Notificar equipe]
    P --> Q[Paciente internado]
    
    G --> R[Aguardar vaga]
    M --> S[Internação cancelada]
```

#### Detalhes do Fluxo:
- **Ator Principal**: Médico assistente
- **Atores Secundários**: Recepção, Enfermagem
- **Tempo Médio**: 15-30 minutos
- **Sistemas Integrados**: Sistema de leitos, Convênios, Notificações

#### Regras Aplicadas:
- RN005: Verificar capacidade do setor
- RN006: Leito ocupado não pode ser usado
- RN017: Autorização para procedimentos caros

---

## 📅 Fluxos de Escalas

### 📋 F003 - Criação de Escala Mensal

```mermaid
flowchart TD
    A[Coordenador inicia escala] --> B[Definir período]
    B --> C[Listar médicos disponíveis]
    C --> D[Verificar preferências]
    D --> E[Gerar escala automática]
    
    E --> F[Validar conflitos]
    F --> G{Há conflitos?}
    G -->|Sim| H[Resolver conflitos]
    G -->|Não| I[Verificar cobertura]
    
    H --> J[Ajustar manualmente]
    J --> F
    
    I --> K{Cobertura completa?}
    K -->|Não| L[Identificar gaps]
    K -->|Sim| M[Salvar rascunho]
    
    L --> N[Solicitar plantões extras]
    N --> O{Médicos aceitaram?}
    O -->|Não| P[Redistribuir plantões]
    O -->|Sim| M
    
    P --> I
    
    M --> Q[Enviar para aprovação]
    Q --> R[Diretor médico avalia]
    R --> S{Aprovado?}
    S -->|Não| T[Retornar para ajustes]
    S -->|Sim| U[Publicar escala]
    
    T --> H
    U --> V[Notificar médicos]
    V --> W[Escala ativa]
```

#### Detalhes do Fluxo:
- **Ator Principal**: Coordenador de escalas
- **Aprovador**: Diretor médico
- **Prazo**: Até 25 do mês anterior
- **Sistemas Integrados**: Email, SMS, Sistema de preferências

#### Regras Aplicadas:
- RN008: Validar plantões sobrepostos
- RN009: Verificar limite de horas
- RN010: Respeitar intervalo entre plantões
- RN011: Garantir cobertura completa

---

### 🔄 F004 - Troca de Plantão

```mermaid
flowchart TD
    A[Médico solicita troca] --> B[Selecionar plantão]
    B --> C[Buscar substituto]
    C --> D{Encontrou substituto?}
    D -->|Não| E[Expandir busca]
    D -->|Sim| F[Validar elegibilidade]
    
    E --> G[Notificar outros médicos]
    G --> H{Alguém se ofereceu?}
    H -->|Não| I[Troca negada]
    H -->|Sim| F
    
    F --> J{Substituto válido?}
    J -->|Não| K[Justificar recusa]
    J -->|Sim| L[Enviar proposta]
    
    L --> M[Substituto avalia]
    M --> N{Aceita troca?}
    N -->|Não| O[Buscar outro]
    N -->|Sim| P[Validar regras]
    
    P --> Q{Troca permitida?}
    Q -->|Não| R[Explicar impedimento]
    Q -->|Sim| S[Supervisor aprova]
    
    S --> T{Aprovado?}
    T -->|Não| U[Troca rejeitada]
    T -->|Sim| V[Efetivar troca]
    
    V --> W[Atualizar escala]
    W --> X[Notificar envolvidos]
    X --> Y[Troca concluída]
    
    K --> C
    O --> C
    I --> Z[Processo encerrado]
    R --> Z
    U --> Z
```

#### Detalhes do Fluxo:
- **Ator Principal**: Médico solicitante
- **Tempo Limite**: 48h antes do plantão
- **Aprovação**: Supervisor do setor

#### Regras Aplicadas:
- RN008: Validar sobreposição
- RN009: Verificar limite de horas de ambos
- RN010: Respeitar intervalo mínimo

---

## 🩺 Fluxos de Atendimento

### 🚑 F005 - Atendimento de Emergência

```mermaid
flowchart TD
    A[Paciente chega na emergência] --> B[Triagem inicial]
    B --> C[Aferir sinais vitais]
    C --> D[Classificar risco]
    D --> E{Classificação}
    
    E -->|Azul| F[Aguardar 4h]
    E -->|Verde| G[Aguardar 2h]
    E -->|Amarelo| H[Aguardar 1h]
    E -->|Laranja| I[Aguardar 10min]
    E -->|Vermelho| J[Atendimento imediato]
    
    F --> K[Verificar médico disponível]
    G --> K
    H --> K
    I --> K
    J --> K
    
    K --> L{Médico livre?}
    L -->|Não| M[Aguardar na fila]
    L -->|Sim| N[Iniciar atendimento]
    
    M --> O[Monitor tempo espera]
    O --> P{Tempo excedido?}
    P -->|Sim| Q[Reclassificar urgência]
    P -->|Não| L
    
    Q --> K
    
    N --> R[Anamnese e exame]
    R --> S[Solicitar exames]
    S --> T{Exames necessários?}
    T -->|Sim| U[Aguardar resultados]
    T -->|Não| V[Definir conduta]
    
    U --> W[Resultados prontos?]
    W -->|Não| X[Continuar aguardando]
    W -->|Sim| V
    
    X --> W
    
    V --> Y{Conduta}
    Y -->|Alta| Z[Prescrever medicamentos]
    Y -->|Internação| AA[Solicitar leito]
    Y -->|Transferência| BB[Contatar hospital]
    Y -->|Cirurgia| CC[Agendar centro cirúrgico]
    
    Z --> DD[Alta do paciente]
    AA --> EE[Aguardar vaga]
    BB --> FF[Aguardar transporte]
    CC --> GG[Aguardar agenda]
    
    EE --> HH[Vaga disponível?]
    HH -->|Sim| II[Transferir para internação]
    HH -->|Não| EE
```

#### Detalhes do Fluxo:
- **Ator Principal**: Equipe de emergência
- **Tempo Crítico**: Conforme classificação Manchester
- **Sistemas Integrados**: Laboratório, Radiologia, Internação

#### Regras Aplicadas:
- RN012: Triagem obrigatória
- RN013: Tempos por classificação
- RN014: Médico da especialidade quando possível

---

### 📅 F006 - Agendamento de Consulta

```mermaid
flowchart TD
    A[Paciente solicita consulta] --> B[Verificar convênio]
    B --> C{Convênio cobre?}
    C -->|Não| D[Orientar sobre particular]
    C -->|Sim| E[Selecionar especialidade]
    
    D --> F{Aceita particular?}
    F -->|Não| G[Cancelar agendamento]
    F -->|Sim| E
    
    E --> H[Buscar médicos disponíveis]
    H --> I{Há vagas?}
    I -->|Não| J[Entrar em lista de espera]
    I -->|Sim| K[Mostrar opções]
    
    K --> L[Paciente escolhe]
    L --> M[Confirmar agendamento]
    M --> N[Gerar comprovante]
    N --> O[Enviar lembrete]
    O --> P[Consulta agendada]
    
    J --> Q[Aguardar cancelamento]
    Q --> R{Vaga liberada?}
    R -->|Sim| S[Notificar paciente]
    R -->|Não| Q
    
    S --> T{Paciente confirma?}
    T -->|Sim| M
    T -->|Não| Q
```

#### Detalhes do Fluxo:
- **Ator Principal**: Recepcionista ou Paciente (online)
- **Antecedência Mínima**: 24 horas
- **Sistema Integrado**: Email, SMS, Convênios

#### Regras Aplicadas:
- RN015: Não agendar no passado
- RN016: Verificar cobertura do convênio

---

## 💰 Fluxos Financeiros

### 💳 F007 - Faturamento Mensal

```mermaid
flowchart TD
    A[Início do mês] --> B[Coletar procedimentos]
    B --> C[Agrupar por convênio]
    C --> D[Validar autorizações]
    D --> E{Autorizações OK?}
    E -->|Não| F[Solicitar pendentes]
    E -->|Sim| G[Calcular valores]
    
    F --> H[Aguardar resposta]
    H --> I{Autorização recebida?}
    I -->|Sim| G
    I -->|Não| J[Excluir do lote]
    
    J --> G
    
    G --> K[Aplicar tabela preços]
    K --> L[Gerar arquivo TISS]
    L --> M[Validar formato]
    M --> N{Arquivo válido?}
    N -->|Não| O[Corrigir erros]
    N -->|Sim| P[Enviar para convênio]
    
    O --> L
    
    P --> Q[Aguardar retorno]
    Q --> R{Aprovado?}
    R -->|Não| S[Analisar glosas]
    R -->|Sim| T[Registrar a receber]
    
    S --> U[Corrigir itens glosados]
    U --> V[Reenviar lote]
    V --> Q
    
    T --> W[Gerar relatório]
    W --> X[Faturamento concluído]
```

#### Detalhes do Fluxo:
- **Responsável**: Equipe financeira
- **Prazo**: Até 5º dia útil
- **Sistemas Integrados**: TISS, Sistema dos convênios

#### Regras Aplicadas:
- RN017: Verificar autorizações necessárias
- RN018: Respeitar prazo de fechamento

---

## 🔐 Fluxos de Segurança

### 🔐 F008 - Login e Autenticação

```mermaid
flowchart TD
    A[Usuário acessa sistema] --> B[Inserir credenciais]
    B --> C[Validar formato]
    C --> D{Formato válido?}
    D -->|Não| E[Mostrar erro formato]
    D -->|Sim| F[Verificar no banco]
    
    F --> G{Usuário existe?}
    G -->|Não| H[Usuário não encontrado]
    G -->|Sim| I[Validar senha]
    
    I --> J{Senha correta?}
    J -->|Não| K[Incrementar tentativas]
    J -->|Sim| L[Verificar status]
    
    K --> M{Max tentativas?}
    M -->|Sim| N[Bloquear conta]
    M -->|Não| O[Tentar novamente]
    
    L --> P{Usuário ativo?}
    P -->|Não| Q[Conta inativa]
    P -->|Sim| R[Verificar 2FA]
    
    R --> S{2FA habilitado?}
    S -->|Não| T[Criar sessão]
    S -->|Sim| U[Solicitar código]
    
    U --> V[Usuário insere código]
    V --> W{Código válido?}
    W -->|Não| X[Código inválido]
    W -->|Sim| T
    
    T --> Y[Registrar login]
    Y --> Z[Redirecionar dashboard]
    
    E --> AA[Retornar ao login]
    H --> AA
    O --> AA
    N --> BB[Contatar admin]
    Q --> BB
    X --> U
```

#### Detalhes do Fluxo:
- **Tempo de Sessão**: 30 minutos inatividade
- **Máximo Tentativas**: 5 tentativas
- **2FA**: Obrigatório para administradores

#### Regras Aplicadas:
- RN003: Senha forte obrigatória
- RN004: Bloqueio por inatividade
- RN021: Expiração de sessão

---

## 📊 Uso dos Fluxos

### 👩‍💻 Para Desenvolvedores
1. **Implementação**: Use os fluxos como base para desenvolvimento
2. **Validação**: Verifique se o código segue o fluxo documentado
3. **Testes**: Crie cenários de teste baseados nos fluxos
4. **Debug**: Use fluxos para rastrear problemas

### 🧪 Para QA
1. **Cenários de Teste**: Cada fluxo gera múltiplos cenários
2. **Caminhos Alternativos**: Teste todos os "Não" dos fluxos
3. **Pontos de Falha**: Foque nos pontos críticos identificados
4. **Integração**: Valide integração entre sistemas

### 📋 Para Product Owners
1. **Validação**: Confirme se fluxo atende requisitos
2. **Otimização**: Identifique gargalos e melhorias
3. **Comunicação**: Use para explicar funcionalidades
4. **Aprovação**: Base para aceitar/rejeitar implementações

### 👥 Para Stakeholders
1. **Entendimento**: Visualize como o sistema funciona
2. **Impacto**: Entenda como mudanças afetam processos
3. **Treinamento**: Base para capacitação de usuários
4. **Auditoria**: Rastree conformidade com processos

---

## 🔄 Manutenção dos Fluxos

### ✅ Quando Atualizar
- Nova funcionalidade implementada
- Mudança em regra de negócio
- Integração com novo sistema
- Otimização de processo
- Feedback dos usuários

### 📝 Como Atualizar
1. **Identificar Mudança**: O que mudou no processo
2. **Mapear Impacto**: Quais fluxos são afetados
3. **Atualizar Diagrama**: Modificar passos e decisões
4. **Validar Regras**: Verificar regras de negócio relacionadas
5. **Comunicar**: Informar equipe sobre mudanças

### 🎯 Métricas dos Fluxos
- **Tempo Médio**: Quanto tempo cada fluxo demora
- **Taxa de Sucesso**: % de fluxos concluídos com sucesso
- **Pontos de Falha**: Onde os fluxos mais falham
- **Gargalos**: Etapas que mais demoram

---

## 📞 Ferramentas Recomendadas

### 🎨 Para Diagramação
- **Mermaid**: Texto → Diagrama (usado neste doc)
- **Draw.io**: Editor visual gratuito
- **Lucidchart**: Ferramenta profissional
- **Bizagi**: Específico para BPMN

### 🔗 Para Integração
- **Postman**: Testar APIs dos fluxos
- **Insomnia**: Alternativa ao Postman
- **Swagger**: Documentar APIs
- **Newman**: Automatizar testes de API

---

**📍 Última atualização**: 2025-10-20  
**👥 Responsáveis**: Product Owner + Tech Lead + Analistas de Processo  
**📋 Status**: Fluxos principais mapeados - detalhar conforme implementação  
**🔄 Próxima revisão**: A cada nova funcionalidade ou mudança de processo

> **💡 Dica**: Use estes fluxos como base para criar user stories e casos de teste!