# 📋 Documento de Requisitos do Sistema

## Sistema de Gestão de Ponto Eletrônico e Banco de Horas

**Projeto:** TCC - Trabalho de Conclusão de Curso  
**Tecnologia:** Laravel 12  

---

## 📑 Índice

1. [Visão Geral](#-visão-geral)
2. [Requisitos Funcionais](#-requisitos-funcionais)
3. [Requisitos Não Funcionais](#-requisitos-não-funcionais)
4. [Regras de Negócio](#-regras-de-negócio)
5. [Casos de Uso](#-casos-de-uso)

---

## 🎯 Visão Geral

Sistema web para gestão de ponto eletrônico e controle de banco de horas em conformidade com a CLT (Consolidação das Leis do Trabalho) brasileira, com foco em jornadas de trabalho de 44 horas semanais. O sistema atende dois perfis de usuários: **Administradores/Gestores** e **Colaboradores**.

### Objetivos Principais

- Automatizar o registro de ponto eletrônico
- Calcular banco de horas seguindo a legislação CLT
- Permitir solicitações de correção de ponto
- Fornecer transparência total aos colaboradores
- Facilitar a gestão administrativa de recursos humanos

---

## ✅ Requisitos Funcionais

### RF01 - Autenticação e Controle de Acesso

#### RF01.1 - Login Dual
- **Descrição:** O sistema deve permitir autenticação separada para Administradores (guard 'user') e Colaboradores (guard 'collaborator')
- **Critérios de Aceitação:**
  - Login com email e senha
  - Redirecionamento correto por perfil
  - Sessões isoladas por guard
  - Recuperação de senha via email

#### RF01.2 - Controle de Acesso por Perfil
- **Descrição:** Diferentes permissões por tipo de usuário
- **Critérios de Aceitação:**
  - Administrador acessa todas as funcionalidades
  - Colaborador acessa apenas funcionalidades permitidas
  - Middleware protege rotas por guard

---

### RF02 - Gestão de Cadastros (Admin)

#### RF02.1 - Gerenciamento de Departamentos
- **Descrição:** CRUD completo de departamentos
- **Critérios de Aceitação:**
  - Criar, editar, listar e excluir departamentos
  - Nome do departamento único
  - Validação de exclusão (verificar vínculos)

#### RF02.2 - Gerenciamento de Cargos
- **Descrição:** CRUD completo de cargos vinculados a departamentos
- **Critérios de Aceitação:**
  - Criar, editar, listar e excluir cargos
  - Vincular cargo a departamento
  - Nome do cargo único
  - Validação de exclusão (verificar vínculos)

#### RF02.3 - Gerenciamento de Jornadas de Trabalho
- **Descrição:** CRUD de jornadas de trabalho com horários por dia da semana
- **Critérios de Aceitação:**
  - Configurar 7 dias da semana individualmente
  - Definir até 2 turnos por dia (manhã/tarde ou contínuo)
  - Campos: entrada_1, saída_1, entrada_2, saída_2 por dia
  - Calcular automaticamente total semanal
  - Validar limite de 44h semanais (CLT)
  - Status ativo/inativo
  - Suporte a turnos noturnos

#### RF02.4 - Gerenciamento de Colaboradores
- **Descrição:** CRUD completo de colaboradores
- **Critérios de Aceitação:**
  - Criar, editar, listar e excluir colaboradores
  - Campos obrigatórios: nome, email, CPF, senha, data admissão, telefone, endereço completo
  - Vincular a cargo e jornada de trabalho
  - CPF e email únicos
  - Status ativo/inativo
  - Validação: não excluir se houver registros de ponto
  - Sugerir inativação ao invés de exclusão

---

### RF03 - Registro de Ponto

#### RF03.1 - Registro de Ponto pelo Colaborador
- **Descrição:** Colaborador registra próprio ponto
- **Critérios de Aceitação:**
  - 4 marcações diárias: entrada manhã, saída almoço, retorno almoço, saída tarde
  - Data e hora automáticas (servidor)
  - Um registro por dia por colaborador
  - Observação opcional por marcação (máx. 30 caracteres)
  - Validação cronológica automática
  - Cálculo automático de horas trabalhadas
  - Status: ausente, incompleto, completo 

#### RF03.2 - Registro de Ponto pelo Admin
- **Descrição:** Admin registra ponto de qualquer colaborador
- **Critérios de Aceitação:**
  - Selecionar colaborador
  - Informar data e hora
  - Detectar automaticamente próximo tipo de marcação
  - Observação opcional por marcação
  - Validação cronológica dos horários
  - Indicação visual do próximo registro

#### RF03.3 - Edição de Ponto (Admin)
- **Descrição:** Admin edita horários já registrados
- **Critérios de Aceitação:**
  - Editar qualquer horário (entrada_1, saída_1, entrada_2, saída_2)
  - Validação cronológica ao editar
  - Observação opcional
  - Marcar registro como "editado"

#### RF03.4 - Cancelamento de Ponto
- **Descrição:** Cancelar último registro do dia
- **Critérios de Aceitação:**
  - Admin e colaborador podem cancelar
  - Cancela sempre o último horário registrado
  - Se cancelar entrada manhã, marca dia como "ausente"
  - Registro mantém histórico (não deleta)

#### RF03.5 - Restauração de Ponto
- **Descrição:** Restaurar registro cancelado
- **Critérios de Aceitação:**
  - Apenas registros com status "ausente"
  - Atualiza status para "incompleto" ou "completo"

#### RF03.6 - Visualização de Registros
- **Descrição:** Listar registros de ponto
- **Critérios de Aceitação:**
  - Admin: visualiza todos os colaboradores
  - Colaborador: visualiza apenas próprios registros
  - Filtros: colaborador, período (últimos 30 dias)
  - Ordenação: data, colaborador
  - Exibir status e total de horas

---

### RF04 - Sistema de Solicitações

#### RF04.1 - Criar Solicitação (Colaborador)
- **Descrição:** Colaborador solicita correção de ponto
- **Critérios de Aceitação:**
  - Selecionar registro dos últimos 30 dias
  - Escolher período: manhã (entrada_1/saída_1) ou tarde (entrada_2/saída_2)
  - Preencher horários antigos automaticamente via AJAX
  - Informar novos horários desejados
  - Motivo obrigatório (máx. 500 caracteres)
  - Status inicial: "pendente"

#### RF04.2 - Listar Solicitações
- **Descrição:** Visualizar solicitações
- **Critérios de Aceitação:**
  - Admin: visualiza todas as solicitações
  - Colaborador: visualiza apenas próprias
  - Ordenação: data de criação
  - Estatísticas: pendentes, aprovadas, rejeitadas, canceladas

#### RF04.3 - Aprovar Solicitação (Admin)
- **Descrição:** Admin aprova correção de ponto
- **Critérios de Aceitação:**
  - Apenas solicitações "pendentes"
  - Atualizar automaticamente registro de ponto (time_tracking)
  - Determinar período correto (manhã/tarde)
  - Comentário do admin opcional
  - Status final: "aprovada"

#### RF04.4 - Rejeitar Solicitação (Admin)
- **Descrição:** Admin rejeita correção de ponto
- **Critérios de Aceitação:**
  - Apenas solicitações "pendentes"
  - Comentário obrigatório (mín. 10 caracteres)
  - Status final: "rejeitada"
  - Registro de ponto permanece inalterado

#### RF04.5 - Cancelar Solicitação (Colaborador)
- **Descrição:** Colaborador cancela própria solicitação
- **Critérios de Aceitação:**
  - Apenas solicitações "pendentes"
  - Status final: "cancelada"

---

### RF05 - Banco de Horas

#### RF05.1 - Banco de Horas do Admin
- **Descrição:** Visualização consolidada de banco de horas de todos os colaboradores
- **Critérios de Aceitação:**
  - Filtros: colaborador, mês
  - Cálculo semanal (domingo a sábado)
  - Limite definido pela jornada de trabalho
  - Registros de horas extras
  - Resumo geral: total positivo/negativo
  - Detalhamento por semana: trabalhadas, esperadas, saldo
  - Detalhamento por dia
  - Baseado na jornada individual de cada colaborador

#### RF05.2 - Banco de Horas do Colaborador
- **Descrição:** Visualização individual do próprio banco de horas
- **Critérios de Aceitação:**
  - Filtro: mês (máximo mês atual)
  - Visualiza apenas próprios dados
  - Cards de resumo: horas trabalhadas, horas esperadas, dias trabalhados, saldo
  - Lista de dias com dropdown expansível
  - Dropdown mostra:
    - Período manhã: entrada, saída, total
    - Período tarde: entrada, saída, total
    - Observações (se houver)
    - Mensagem para dias ausentes
  - Cálculo baseado na jornada individual
  - Análise semanal CLT (44h limite)

---

### RF06 - Dashboard

#### RF06.1 - Dashboard Admin
- **Descrição:** Painel administrativo com métricas
- **Critérios de Aceitação:**
  - Total de colaboradores
  - Colaboradores por departamento
  - Registros de ponto recentes
  - Solicitações pendentes
  - Gráficos e estatísticas

#### RF06.2 - Dashboard Colaborador
- **Descrição:** Painel do colaborador
- **Critérios de Aceitação:**
  - Resumo do banco de horas atual
  - Próximo tipo de marcação
  - Últimos registros
  - Solicitações recentes

---

#### RF07 - Recuperação de Senha
- **Descrição:** Redefinir senha via email
- **Critérios de Aceitação:**
  - Envio de email com token único
  - Link de redefinição com expiração
  - Validação de token
  - Atualização segura de senha

---

## 🔧 Requisitos Não Funcionais

### RNF01 - Segurança

#### RNF01.1 - Autenticação
- **Descrição:** Mecanismos de autenticação seguros
- **Critério:**
  - Hash de senhas (bcrypt)
  - Guards separados por perfil
  - Sessões seguras
  - Proteção contra CSRF

#### RNF01.2 - Autorização
- **Descrição:** Controle de acesso por perfil
- **Critério:**
  - Middlewares em todas as rotas protegidas
  - Validação de propriedade de recursos
  - Isolamento de dados por usuário

#### RNF01.3 - Validação de Dados
- **Descrição:** Validação de entrada de dados
- **Critério:**
  - Form Requests para todas as submissões
  - Validação server-side obrigatória
  - Validação client-side complementar

#### RNF01.4 - Proteção de Dados Sensíveis
- **Descrição:** Proteção de informações pessoais
- **Critério:**
  - Senhas sempre hasheadas
  - CPF armazenado apenas números
  - HTTPS em produção
  - Logs não expõem dados sensíveis (Laravel logs)

---

### RNF02 - Usabilidade

#### RNF02.1 - Interface Responsiva
- **Descrição:** Adaptação a diferentes dispositivos
- **Critério:**
  - Design mobile-first
  - Breakpoints: mobile, tablet, desktop
  - Testado em Chrome, Firefox, Edge, Safari

#### RNF02.2 - Acessibilidade
- **Descrição:** Interface acessível
- **Critério:**
  - Contraste adequado de cores
  - Tema dark/light
  - Labels em formulários
  - Mensagens de erro claras

#### RNF02.3 - Feedback Visual
- **Descrição:** Comunicação clara com usuário
- **Critério:**
  - Mensagens de sucesso/erro
  - Loading states
  - Validação em tempo real
  - Confirmação para ações destrutivas

#### RNF02.4 - Navegação Intuitiva
- **Descrição:** Fácil localização de funcionalidades
- **Critério:**
  - Menu lateral organizado
  - Breadcrumbs em páginas internas
  - Ícones intuitivos (Font Awesome)
  - Estrutura lógica de páginas

---

### RNF03 - Manutenibilidade

#### RNF03.1 - Código Limpo
- **Descrição:** Código legível e organizado
- **Critério:**
  - Seguir princípios SOLID e Arquitetura Limpa
  - Nomenclatura descritiva
  - Funções com responsabilidade única
  - PHPdocs (documentação de métodos e funções)

#### RNF03.2 - Arquitetura MVC
- **Descrição:** Separação de responsabilidades
- **Critério:**
  - Models: lógica de dados
  - Controllers: lógica de aplicação
  - Views: apresentação
  - Services: lógicas complexas

#### RNF03.3 - Versionamento
- **Descrição:** Controle de versão do código
- **Critério:**
  - Git para versionamento
  - Commits descritivos

---

#### RNF04.1 - Tratamento de Erros
- **Descrição:** Gestão de erros e exceções
- **Critério:**
  - Try-catch em operações críticas
  - Logs de erros (Laravel logs)
  - Mensagens amigáveis ao usuário

---
