# 📖 GLOSSÁRIO DE DOMÍNIO - EscalaMedica2

> **Referenciado por**: `REGISTRY.md` → "Termos e Definições"

---

## 🏥 TERMOS MÉDICOS

### **Plantonista**
Profissional de saúde (médico, enfermeiro, técnico) responsável por atender pacientes durante períodos específicos de trabalho.

### **Plantão**
Período de trabalho em que um profissional de saúde permanece disponível para atendimento em uma unidade hospitalar.

### **Escala Médica**
Organização sistemática dos horários de trabalho dos profissionais de saúde, garantindo cobertura adequada em todos os períodos.

### **CRM**
Conselho Regional de Medicina. Número de registro profissional obrigatório para médicos.

### **Especialização**
Área específica da medicina em que o profissional possui formação adicional (ex: Cardiologia, Pediatria, Cirurgia).

---

## 🏗️ TERMOS ORGANIZACIONAIS

### **Unidade**
Estabelecimento de saúde (hospital, clínica, posto de saúde) onde os profissionais prestam serviços.

### **Setor**
Departamento ou área específica dentro de uma unidade de saúde (ex: UTI, Emergência, Enfermaria).

### **Turno**
Período padronizado de trabalho com horário de início e fim definidos (ex: Matutino, Vespertino, Noturno).

### **Alocação**
Designação específica de um plantonista para trabalhar em determinado setor, turno e data.

---

## ⏰ TERMOS TEMPORAIS

### **Hora Início**
Horário em que o plantonista deve iniciar suas atividades.

### **Hora Fim**
Horário em que o plantonista encerra suas atividades (calculado automaticamente pelo sistema).

### **Duração**
Período total de trabalho em horas (ex: 12h, 24h, 6h).

### **Plantão Noturno**
Turno de trabalho que se estende entre 22h e 06h, podendo cruzar a meia-noite.

### **Sobreposição**
Conflito onde um mesmo plantonista possui dois plantões com horários que se interceptam.

---

## 🎯 TERMOS DO SISTEMA

### **CRUD**
Create (Criar), Read (Ler), Update (Atualizar), Delete (Excluir) - operações básicas do sistema.

### **Dashboard**
Painel principal que exibe estatísticas, resumos e acesso rápido às funcionalidades.

### **Score**
Pontuação calculada com base na frequência e qualidade dos plantões realizados pelo profissional.

### **Marketplace**
Seção onde plantonistas podem oferecer ou buscar trocas de plantões.

### **Quick Access**
Acesso rápido às funcionalidades mais utilizadas do sistema.

---

## 🔧 TERMOS TÉCNICOS

### **Observer**
Padrão de design que monitora alterações em modelos e executa ações automáticas.

### **Validation Rule**
Regra customizada que valida dados antes de serem salvos no banco.

### **Migration**
Script que define a estrutura das tabelas do banco de dados.

### **Seeder**
Script que popula o banco com dados iniciais para teste ou produção.

### **Eloquent**
ORM (Object-Relational Mapping) do Laravel para interação com banco de dados.

---

## 📊 TERMOS DE STATUS

### **Status da Alocação**
- **Pendente**: Alocação criada mas não confirmada
- **Confirmada**: Alocação aprovada e válida
- **Cancelada**: Alocação cancelada por algum motivo

### **Tipo de Turno**
- **Diurno**: Turnos que ocorrem durante o dia
- **Noturno**: Turnos que ocorrem durante a noite
- **Misto**: Turnos que podem ocorrer em qualquer período

---

## 🚨 TERMOS DE VALIDAÇÃO

### **Conflito de Horários**
Situação onde um plantonista possui dois ou mais plantões com horários sobrepostos.

### **Validação Preventiva**
Verificação automática que impede a criação de alocações com conflitos.

### **Integridade Referencial**
Garantia de que relacionamentos entre tabelas permaneçam consistentes.

---

## 📱 TERMOS DE INTERFACE

### **Hero Section**
Seção principal da página inicial com título, descrição e chamadas para ação.

### **Card**
Componente visual que agrupa informações relacionadas em formato de cartão.

### **Sidebar**
Barra lateral de navegação com links para as principais funcionalidades.

### **Responsivo**
Interface que se adapta automaticamente a diferentes tamanhos de tela.

---

## 🔄 TERMOS DE WORKFLOW

### **Sprint**
Período de desenvolvimento focado em implementar funcionalidades específicas.

### **Commit**
Registro de alterações no sistema com descrição detalhada.

### **Deploy**
Processo de colocar o sistema em produção para uso real.

### **Rollback**
Retorno a uma versão anterior do sistema em caso de problemas.

---

## 📋 ABREVIAÇÕES COMUNS

| Abreviação | Significado | Contexto |
|------------|-------------|----------|
| **UTI** | Unidade de Terapia Intensiva | Setor hospitalar |
| **PA** | Pronto Atendimento | Setor de emergência |
| **CC** | Centro Cirúrgico | Setor operatório |
| **UI/UX** | User Interface / User Experience | Interface do usuário |
| **API** | Application Programming Interface | Integração de sistemas |
| **CRUD** | Create, Read, Update, Delete | Operações básicas |
| **ORM** | Object-Relational Mapping | Mapeamento objeto-relacional |
| **MVC** | Model-View-Controller | Padrão arquitetural |

---

## 📝 CONVENÇÕES DE NOMENCLATURA

### Controllers
- Sempre PascalCase + "Controller"
- Singular do nome da entidade
- Exemplo: `PlantonistaController`

### Models
- Sempre PascalCase
- Singular
- Exemplo: `Plantonista`

### Views
- Sempre snake_case
- Plural para índices, singular para ações
- Exemplo: `plantonistas/create.blade.php`

### Rotas
- Sempre snake_case
- Seguir padrão RESTful
- Exemplo: `plantonistas.store`

---

*Glossário completo dos termos utilizados no EscalaMedica2*
*Última atualização: 2024-12-28*
*Total de termos definidos: 45*