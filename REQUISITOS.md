# 📋 Documento de Requisitos do Sistema

## Sistema de Gestão de Ponto Eletrônico e Banco de Horas

**Versão:** 1.0  
**Data:** 09 de outubro de 2025  
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
- **Descrição:** O sistema deve permitir autenticação separada para Administradores (guard 'web') e Colaboradores (guard 'collaborator')
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Login com email e senha
  - Redirecionamento correto por perfil
  - Sessões isoladas por guard
  - Recuperação de senha via email

#### RF01.2 - Controle de Acesso por Perfil
- **Descrição:** Diferentes permissões por tipo de usuário
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Administrador acessa todas as funcionalidades
  - Colaborador acessa apenas funcionalidades permitidas
  - Middleware protege rotas por guard

---

### RF02 - Gestão de Cadastros (Admin)

#### RF02.1 - Gerenciamento de Departamentos
- **Descrição:** CRUD completo de departamentos
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Criar, editar, listar e excluir departamentos
  - Nome do departamento único
  - Validação de exclusão (verificar vínculos)

#### RF02.2 - Gerenciamento de Cargos
- **Descrição:** CRUD completo de cargos vinculados a departamentos
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Criar, editar, listar e excluir cargos
  - Vincular cargo a departamento
  - Nome do cargo único
  - Validação de exclusão (verificar vínculos)

#### RF02.3 - Gerenciamento de Jornadas de Trabalho
- **Descrição:** CRUD de jornadas de trabalho com horários por dia da semana
- **Prioridade:** Alta
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
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Criar, editar, listar e excluir colaboradores
  - Campos obrigatórios: nome, email, CPF, senha, data admissão
  - Campos opcionais: telefone, endereço completo
  - Vincular a cargo e jornada de trabalho
  - CPF e email únicos
  - Status ativo/inativo
  - Validação: não excluir se houver registros de ponto
  - Sugerir inativação ao invés de exclusão

---

### RF03 - Registro de Ponto

#### RF03.1 - Registro de Ponto pelo Colaborador
- **Descrição:** Colaborador registra próprio ponto
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - 4 marcações diárias: entrada manhã, saída almoço, retorno almoço, saída tarde
  - Data e hora automáticas (servidor)
  - Um registro por dia por colaborador
  - Observação opcional por marcação (máx. 30 caracteres)
  - Validação cronológica automática
  - Cálculo automático de horas trabalhadas
  - Status: incompleto → completo

#### RF03.2 - Registro de Ponto pelo Admin
- **Descrição:** Admin registra ponto de qualquer colaborador
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Selecionar colaborador
  - Informar data e hora
  - Detectar automaticamente próximo tipo de marcação
  - Observação opcional por marcação
  - Validação cronológica dos horários
  - Indicação visual do próximo registro

#### RF03.3 - Edição de Ponto (Admin)
- **Descrição:** Admin edita horários já registrados
- **Prioridade:** Média
- **Critérios de Aceitação:**
  - Editar qualquer horário (entrada_1, saída_1, entrada_2, saída_2)
  - Validação cronológica ao editar
  - Observação opcional
  - Marcar registro como "editado"

#### RF03.4 - Cancelamento de Ponto
- **Descrição:** Cancelar último registro do dia
- **Prioridade:** Média
- **Critérios de Aceitação:**
  - Admin e colaborador podem cancelar
  - Cancela sempre o último horário registrado
  - Se cancelar entrada manhã, marca dia como "ausente"
  - Registro mantém histórico (não deleta)

#### RF03.5 - Restauração de Ponto
- **Descrição:** Restaurar registro cancelado
- **Prioridade:** Baixa
- **Critérios de Aceitação:**
  - Apenas registros com status "ausente"
  - Atualiza status para "incompleto" ou "completo"

#### RF03.6 - Visualização de Registros
- **Descrição:** Listar registros de ponto
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Admin: visualiza todos os colaboradores
  - Colaborador: visualiza apenas próprios registros
  - Filtros: colaborador, período (últimos 30 dias)
  - Ordenação: data, colaborador
  - Paginação: 15 registros por página
  - Exibir status e total de horas

---

### RF04 - Sistema de Solicitações

#### RF04.1 - Criar Solicitação (Colaborador)
- **Descrição:** Colaborador solicita correção de ponto
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Selecionar registro dos últimos 30 dias
  - Escolher período: manhã (entrada_1/saída_1) ou tarde (entrada_2/saída_2)
  - Preencher horários antigos automaticamente via AJAX
  - Informar novos horários desejados
  - Motivo obrigatório (máx. 500 caracteres)
  - Status inicial: "pendente"

#### RF04.2 - Listar Solicitações
- **Descrição:** Visualizar solicitações
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Admin: visualiza todas as solicitações
  - Colaborador: visualiza apenas próprias
  - Filtros: status, colaborador, período
  - Ordenação: data de criação
  - Estatísticas: pendentes, aprovadas, rejeitadas, canceladas
  - Paginação: 15 registros por página

#### RF04.3 - Aprovar Solicitação (Admin)
- **Descrição:** Admin aprova correção de ponto
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Apenas solicitações "pendentes"
  - Atualizar automaticamente registro de ponto (time_tracking)
  - Determinar período correto (manhã/tarde)
  - Comentário do admin opcional
  - Status final: "aprovada"
  - Log de auditoria

#### RF04.4 - Rejeitar Solicitação (Admin)
- **Descrição:** Admin rejeita correção de ponto
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Apenas solicitações "pendentes"
  - Comentário obrigatório (mín. 10 caracteres)
  - Status final: "rejeitada"
  - Registro de ponto permanece inalterado

#### RF04.5 - Cancelar Solicitação (Colaborador)
- **Descrição:** Colaborador cancela própria solicitação
- **Prioridade:** Média
- **Critérios de Aceitação:**
  - Apenas solicitações "pendentes"
  - Status final: "cancelada"

---

### RF05 - Banco de Horas

#### RF05.1 - Banco de Horas do Admin
- **Descrição:** Visualização consolidada de banco de horas de todos os colaboradores
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Filtros: colaborador, mês
  - Cálculo semanal (domingo a sábado)
  - Limite CLT de 44h por semana
  - Resumo geral: total positivo/negativo
  - Detalhamento por semana: trabalhadas, esperadas, saldo
  - Detalhamento por dia
  - Baseado na jornada individual de cada colaborador
  - Exibir últimos 10 dias trabalhados
  - Suporte AJAX para filtros

#### RF05.2 - Banco de Horas do Colaborador
- **Descrição:** Visualização individual do próprio banco de horas
- **Prioridade:** Alta
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
  - Suporte AJAX para filtro de mês

---

### RF06 - Dashboard

#### RF06.1 - Dashboard Admin
- **Descrição:** Painel administrativo com métricas
- **Prioridade:** Média
- **Critérios de Aceitação:**
  - Total de colaboradores
  - Colaboradores por departamento
  - Registros de ponto recentes
  - Solicitações pendentes
  - Gráficos e estatísticas

#### RF06.2 - Dashboard Colaborador
- **Descrição:** Painel do colaborador
- **Prioridade:** Média
- **Critérios de Aceitação:**
  - Resumo do banco de horas atual
  - Próximo tipo de marcação
  - Últimos registros
  - Solicitações recentes

---

### RF07 - Perfil e Configurações

#### RF07.1 - Editar Cadastro (Colaborador)
- **Descrição:** Colaborador edita próprios dados
- **Prioridade:** Média
- **Critérios de Aceitação:**
  - Editar: nome, telefone, endereço
  - Não pode editar: email, senha, jornada, cargo
  - Validações de campos

#### RF07.2 - Recuperação de Senha
- **Descrição:** Redefinir senha via email
- **Prioridade:** Alta
- **Critérios de Aceitação:**
  - Envio de email com token único
  - Link de redefinição com expiração
  - Validação de token
  - Atualização segura de senha

---

## 🔧 Requisitos Não Funcionais

### RNF01 - Desempenho

#### RNF01.1 - Tempo de Resposta
- **Descrição:** Tempo máximo de resposta para operações
- **Critério:** 
  - Operações simples (CRUD): < 2 segundos
  - Cálculos de banco de horas: < 5 segundos
  - Carregamento de páginas: < 3 segundos

#### RNF01.2 - Otimização de Consultas
- **Descrição:** Uso eficiente do banco de dados
- **Critério:**
  - Eager loading para relacionamentos
  - Índices em campos frequentemente filtrados
  - Paginação em todas as listagens
  - Cache quando aplicável

---

### RNF02 - Segurança

#### RNF02.1 - Autenticação
- **Descrição:** Mecanismos de autenticação seguros
- **Critério:**
  - Hash de senhas (bcrypt)
  - Guards separados por perfil
  - Sessões seguras
  - Proteção contra CSRF

#### RNF02.2 - Autorização
- **Descrição:** Controle de acesso por perfil
- **Critério:**
  - Middlewares em todas as rotas protegidas
  - Validação de propriedade de recursos
  - Isolamento de dados por usuário

#### RNF02.3 - Validação de Dados
- **Descrição:** Validação de entrada de dados
- **Critério:**
  - Form Requests para todas as submissões
  - Sanitização de inputs
  - Validação server-side obrigatória
  - Validação client-side complementar

#### RNF02.4 - Proteção de Dados Sensíveis
- **Descrição:** Proteção de informações pessoais
- **Critério:**
  - Senhas sempre hasheadas
  - CPF armazenado apenas números
  - HTTPS em produção
  - Logs não expõem dados sensíveis

---

### RNF03 - Usabilidade

#### RNF03.1 - Interface Responsiva
- **Descrição:** Adaptação a diferentes dispositivos
- **Critério:**
  - Design mobile-first
  - Breakpoints: mobile, tablet, desktop
  - Testado em Chrome, Firefox, Edge, Safari

#### RNF03.2 - Acessibilidade
- **Descrição:** Interface acessível
- **Critério:**
  - Contraste adequado de cores
  - Tema dark/light
  - Labels em formulários
  - Mensagens de erro claras

#### RNF03.3 - Feedback Visual
- **Descrição:** Comunicação clara com usuário
- **Critério:**
  - Mensagens de sucesso/erro
  - Loading states
  - Validação em tempo real
  - Confirmação para ações destrutivas

#### RNF03.4 - Navegação Intuitiva
- **Descrição:** Fácil localização de funcionalidades
- **Critério:**
  - Menu lateral organizado
  - Breadcrumbs em páginas internas
  - Ícones intuitivos (Font Awesome)
  - Estrutura lógica de páginas

---

### RNF04 - Manutenibilidade

#### RNF04.1 - Código Limpo
- **Descrição:** Código legível e organizado
- **Critério:**
  - Seguir PSR-12 (PHP)
  - Nomenclatura descritiva
  - Funções com responsabilidade única
  - Comentários em lógicas complexas

#### RNF04.2 - Arquitetura MVC
- **Descrição:** Separação de responsabilidades
- **Critério:**
  - Models: lógica de dados
  - Controllers: lógica de aplicação
  - Views: apresentação
  - Services: lógicas complexas

#### RNF04.3 - Versionamento
- **Descrição:** Controle de versão do código
- **Critério:**
  - Git para versionamento
  - Commits descritivos
  - Branches para features
  - Documentação de mudanças

---

### RNF05 - Confiabilidade

#### RNF05.1 - Tratamento de Erros
- **Descrição:** Gestão de erros e exceções
- **Critério:**
  - Try-catch em operações críticas
  - Logs de erros
  - Mensagens amigáveis ao usuário
  - Rollback em transações

#### RNF05.2 - Validação de Integridade
- **Descrição:** Garantia de dados consistentes
- **Critério:**
  - Foreign keys no banco
  - Transações para operações múltiplas
  - Validação de regras de negócio
  - Testes de integridade

#### RNF05.3 - Backup e Recuperação
- **Descrição:** Proteção contra perda de dados
- **Critério:**
  - Migrations versionadas
  - Seeders para dados iniciais
  - Documentação de estrutura

---

### RNF06 - Portabilidade

#### RNF06.1 - Multiplataforma
- **Descrição:** Execução em diferentes ambientes
- **Critério:**
  - Linux, Windows, macOS
  - PHP 8.2+
  - MySQL/PostgreSQL
  - Composer para dependências

#### RNF06.2 - Configuração Flexível
- **Descrição:** Fácil configuração de ambiente
- **Critério:**
  - Arquivo .env para configurações
  - Variáveis de ambiente documentadas
  - Instalação via composer
  - Migrations automatizadas

---

### RNF07 - Escalabilidade

#### RNF07.1 - Crescimento de Dados
- **Descrição:** Suporte a aumento de volume
- **Critério:**
  - Paginação em listagens
  - Índices otimizados
  - Queries eficientes
  - Arquivamento de dados antigos (futuro)

#### RNF07.2 - Crescimento de Usuários
- **Descrição:** Suporte a mais usuários simultâneos
- **Critério:**
  - Sessões em banco/cache
  - Prepared statements
  - Connection pooling
  - Load balancing (futuro)

---

### RNF08 - Compatibilidade

#### RNF08.1 - Navegadores
- **Descrição:** Suporte a navegadores modernos
- **Critério:**
  - Chrome 90+
  - Firefox 88+
  - Edge 90+
  - Safari 14+

#### RNF08.2 - Conformidade Legal
- **Descrição:** Adequação à legislação brasileira
- **Critério:**
  - CLT: 44 horas semanais
  - CLT: Máximo 2h extras por dia
  - CLT: Máximo 10h extras por semana
  - Portaria 671/2021 (Ponto Eletrônico)

---

## 📜 Regras de Negócio

### RN01 - Jornada de Trabalho
- **RN01.1:** Jornada semanal não pode exceder 44 horas (CLT)
- **RN01.2:** Jornada pode variar por dia da semana
- **RN01.3:** Cada dia pode ter até 2 turnos (manhã/tarde)
- **RN01.4:** Total semanal calculado automaticamente
- **RN01.5:** Suporte a turnos noturnos (saída no dia seguinte)

### RN02 - Registro de Ponto
- **RN02.1:** Apenas 4 marcações por dia por colaborador
- **RN02.2:** Ordem obrigatória: entrada → saída → entrada → saída
- **RN02.3:** Horários devem ser cronológicos
- **RN02.4:** Um único registro por colaborador por dia
- **RN02.5:** Status automático: incompleto → completo → ausente
- **RN02.6:** Total de horas calculado automaticamente

### RN03 - Banco de Horas
- **RN03.1:** Cálculo semanal (domingo a sábado)
- **RN03.2:** Limite de 44h por semana considerado
- **RN03.3:** Saldo = (horas trabalhadas) - (horas esperadas)
- **RN03.4:** Baseado na jornada individual do colaborador
- **RN03.5:** Colaborador visualiza apenas próprio banco
- **RN03.6:** Admin visualiza banco de todos

### RN04 - Solicitações
- **RN04.1:** Apenas últimos 30 dias podem ser solicitados
- **RN04.2:** Solicitação por período completo (manhã ou tarde)
- **RN04.3:** Colaborador não pode editar ponto diretamente
- **RN04.4:** Apenas admin pode aprovar/rejeitar
- **RN04.5:** Aprovação atualiza automaticamente o registro
- **RN04.6:** Rejeição exige comentário justificativo
- **RN04.7:** Apenas pendentes podem ser canceladas

### RN05 - Colaboradores
- **RN05.1:** CPF único no sistema
- **RN05.2:** Email único no sistema
- **RN05.3:** Senha hasheada (bcrypt)
- **RN05.4:** Colaborador vinculado a cargo e jornada
- **RN05.5:** Não excluir se houver registros de ponto
- **RN05.6:** Status ativo/inativo controla acesso

### RN06 - Cargos e Departamentos
- **RN06.1:** Cargo vinculado a departamento
- **RN06.2:** Nomes únicos
- **RN06.3:** Não excluir se houver colaboradores vinculados

### RN07 - Segurança
- **RN07.1:** Guards separados: web (admin) e collaborator
- **RN07.2:** Colaborador acessa apenas próprios dados
- **RN07.3:** Admin acessa todos os dados
- **RN07.4:** Validação server-side obrigatória

---

## 🎭 Casos de Uso

### UC01 - Registrar Ponto (Colaborador)

**Ator:** Colaborador  
**Pré-condição:** Colaborador autenticado  
**Fluxo Principal:**
1. Colaborador acessa menu "Bater Ponto"
2. Sistema exibe próximo tipo de marcação
3. Colaborador opcionalmente adiciona observação
4. Colaborador clica em "Registrar Ponto"
5. Sistema registra data/hora automática
6. Sistema valida ordem cronológica
7. Sistema atualiza status do registro
8. Sistema exibe mensagem de sucesso

**Fluxo Alternativo:**
- 6a. Horário fora de ordem cronológica
  - Sistema exibe erro
  - Retorna ao passo 3

**Pós-condição:** Ponto registrado no sistema

---

### UC02 - Solicitar Correção de Ponto (Colaborador)

**Ator:** Colaborador  
**Pré-condição:** Colaborador autenticado  
**Fluxo Principal:**
1. Colaborador acessa menu "Solicitações"
2. Colaborador clica em "Nova Solicitação"
3. Colaborador seleciona registro dos últimos 30 dias
4. Sistema exibe períodos disponíveis
5. Colaborador seleciona período (manhã/tarde)
6. Sistema preenche horários antigos automaticamente
7. Colaborador informa novos horários
8. Colaborador informa motivo
9. Sistema valida dados
10. Sistema cria solicitação com status "pendente"
11. Sistema exibe mensagem de sucesso

**Fluxo Alternativo:**
- 9a. Dados inválidos
  - Sistema exibe erros de validação
  - Retorna ao passo 7

**Pós-condição:** Solicitação criada aguardando análise

---

### UC03 - Aprovar Solicitação (Admin)

**Ator:** Administrador  
**Pré-condição:** Admin autenticado, solicitação pendente  
**Fluxo Principal:**
1. Admin acessa menu "Solicitações"
2. Admin filtra solicitações pendentes
3. Admin visualiza detalhes da solicitação
4. Admin clica em "Aprovar"
5. Admin opcionalmente adiciona comentário
6. Sistema valida status "pendente"
7. Sistema atualiza registro de ponto
8. Sistema determina período correto (manhã/tarde)
9. Sistema atualiza status para "aprovada"
10. Sistema registra log de auditoria
11. Sistema exibe mensagem de sucesso

**Fluxo Alternativo:**
- 6a. Solicitação já processada
  - Sistema exibe erro
  - Retorna à lista

**Pós-condição:** Solicitação aprovada e ponto corrigido

---

### UC04 - Visualizar Banco de Horas (Colaborador)

**Ator:** Colaborador  
**Pré-condição:** Colaborador autenticado  
**Fluxo Principal:**
1. Colaborador acessa menu "Banco de Horas"
2. Sistema calcula banco de horas do mês atual
3. Sistema exibe cards de resumo
4. Sistema lista dias registrados
5. Colaborador clica em um dia
6. Sistema expande dropdown com detalhes
7. Sistema exibe horários por período
8. Sistema exibe total trabalhado
9. Sistema exibe observações (se houver)

**Fluxo Alternativo:**
- 2a. Colaborador filtra por mês
  - Sistema recalcula banco de horas
  - Atualiza resumo e lista

**Pós-condição:** Colaborador visualiza banco de horas

---

### UC05 - Gerenciar Jornada de Trabalho (Admin)

**Ator:** Administrador  
**Pré-condição:** Admin autenticado  
**Fluxo Principal:**
1. Admin acessa menu "Jornadas de Trabalho"
2. Admin clica em "Nova Jornada"
3. Admin informa nome da jornada
4. Para cada dia da semana:
   - 4a. Admin marca se é dia útil
   - 4b. Admin informa horários (entrada/saída)
   - 4c. Admin opcionalmente informa 2º turno
5. Sistema calcula total semanal automaticamente
6. Sistema valida limite de 44h
7. Sistema salva jornada
8. Sistema exibe mensagem de sucesso

**Fluxo Alternativo:**
- 6a. Excede 44h semanais
  - Sistema exibe aviso
  - Ajusta para limite máximo
  - Continua no passo 7

**Pós-condição:** Jornada de trabalho criada

---

## 📊 Matriz de Rastreabilidade

| Requisito | Prioridade | Caso de Uso | Status |
|-----------|------------|-------------|--------|
| RF01.1 | Alta | UC01, UC02, UC03, UC04, UC05 | ✅ Implementado |
| RF02.3 | Alta | UC05 | ✅ Implementado |
| RF03.1 | Alta | UC01 | ✅ Implementado |
| RF04.1 | Alta | UC02 | ✅ Implementado |
| RF04.3 | Alta | UC03 | ✅ Implementado |
| RF05.2 | Alta | UC04 | ✅ Implementado |
| RNF02.1 | Alta | Todos | ✅ Implementado |
| RNF03.1 | Alta | Todos | ✅ Implementado |

---

## 📝 Notas de Implementação

### Tecnologias Utilizadas
- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Blade, TailwindCSS, JavaScript Vanilla
- **Banco de Dados:** MySQL/PostgreSQL
- **Autenticação:** Laravel Guards (web + collaborator)
- **Validação:** Form Requests
- **Tema:** Dark/Light com CSS Variables

### Conformidade Legal
- ✅ CLT Art. 58: Jornada de 44h semanais
- ✅ CLT Art. 59: Máximo 2h extras por dia
- ✅ Portaria 671/2021: Ponto Eletrônico

### Próximas Evoluções (Futuro)
- [ ] Relatórios em PDF
- [ ] Gráficos de produtividade
- [ ] Integração com folha de pagamento
- [ ] App mobile nativo
- [ ] Reconhecimento facial
- [ ] Geolocalização

---

**Documento gerado em:** 09/10/2025  
**Versão do Sistema:** 1.0  
**Autor:** Sistema TCC Desktop
