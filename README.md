# ⏱️ Metre Ponto

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind-4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**Sistema de Controle de Ponto Eletrônico e Banco de Horas**

[Funcionalidades](#-funcionalidades) • [Tecnologias](#-tecnologias) • [Instalação](#-instalação) • [Uso](#-uso) • [Licença](#-licença)

</div>

---

## 📋 Sobre o Projeto

**Metre Ponto** é um sistema web completo para gestão de ponto eletrônico e controle de banco de horas, desenvolvido em conformidade com a **CLT (Consolidação das Leis do Trabalho)** brasileira. O sistema permite o registro de jornadas de trabalho de até 44 horas semanais, cálculo automático de banco de horas e gestão de solicitações de correção de ponto.

### 👥 Perfis de Usuário

- **Administradores/Gestores** (`User`): Acesso completo ao sistema, incluindo cadastros, aprovação de solicitações e relatórios gerenciais
- **Colaboradores** (`CollaboratorModel`): Registro de ponto próprio, visualização de banco de horas e envio de solicitações de correção

---

## ✨ Funcionalidades

### 🔐 Autenticação e Segurança

- **Sistema de login duplo** com guards separados para administradores e colaboradores
- **Recuperação de senha** via e-mail com token de segurança
- **Proteção CSRF** em todos os formulários
- **Senhas criptografadas** com bcrypt
- **Sessões isoladas** por tipo de usuário

### 👨‍💼 Gestão Administrativa (Admin)

#### Cadastros Completos
- **Departamentos**: Organização estrutural da empresa
- **Cargos**: Vinculados a departamentos específicos
- **Jornadas de Trabalho**: 
  - Configuração individual por dia da semana
  - Até 2 turnos por dia (manhã/tarde ou contínuo)
  - Suporte a turnos noturnos
  - Cálculo automático do total semanal
  - Validação de limite de 44h semanais (CLT)
- **Colaboradores**: 
  - Dados pessoais completos (CPF, telefone, endereço)
  - Vinculação a cargo e jornada de trabalho
  - Controle de status (ativo/inativo)
  - Validação de CPF e e-mail únicos

#### Gestão de Ponto
- **Registro de ponto** para qualquer colaborador
- **Edição de registros** com histórico de alterações
- **Visualização completa** de todos os registros
- **Filtros avançados** por colaborador e período
- **Cancelamento e restauração** de registros

#### Sistema de Solicitações
- **Aprovação/Rejeição** de correções de ponto
- **Comentários administrativos** em cada solicitação
- **Atualização automática** dos registros ao aprovar
- **Dashboard** com solicitações pendentes

#### Banco de Horas
- **Visão consolidada** de todos os colaboradores
- **Cálculo semanal** baseado na jornada individual
- **Análise de horas extras** e déficit
- **Detalhamento por dia e semana**
- **Filtros** por colaborador e mês

### 👤 Área do Colaborador

#### Registro de Ponto
- **4 marcações diárias**:
  - Entrada manhã
  - Saída para almoço
  - Retorno do almoço
  - Saída tarde
- **Data e hora automáticas** do servidor
- **Observações individuais** por marcação (até 30 caracteres)
- **Validação cronológica** automática
- **Cálculo automático** de horas trabalhadas
- **Status** do registro (ausente, incompleto, completo)

#### Solicitações de Correção
- **Criação de solicitações** para últimos 30 dias
- **Preenchimento automático** de horários antigos via AJAX
- **Seleção de período** (manhã ou tarde)
- **Justificativa obrigatória** (até 500 caracteres)
- **Acompanhamento de status**: pendente, aprovada, rejeitada, cancelada
- **Cancelamento** de solicitações pendentes

#### Banco de Horas Pessoal
- **Cards de resumo**:
  - Total de horas trabalhadas
  - Horas esperadas
  - Dias trabalhados
  - Saldo (positivo/negativo)
- **Detalhamento diário** com dropdown expansível:
  - Horários de entrada e saída por período
  - Total de horas por período
  - Observações registradas
  - Indicação de dias ausentes
- **Filtro por mês** (limitado ao mês atual)
- **Cálculo baseado** na jornada individual

### 📊 Dashboard

#### Dashboard Administrativo
- Total de colaboradores ativos
- Colaboradores por departamento
- Registros de ponto recentes
- Solicitações pendentes de aprovação
- Estatísticas e métricas

#### Dashboard do Colaborador
- Resumo do banco de horas atual
- Indicação do próximo tipo de marcação
- Últimos registros de ponto
- Solicitações recentes

---

## 🛠️ Tecnologias

### Backend
- **[Laravel 12](https://laravel.com/)** - Framework PHP
- **PHP 8.2** - Linguagem de programação
- **MySQL** - Banco de dados relacional

### Frontend
- **Blade Templates** - Template de desenvolvimento para interfaces web (semelhante ao HTML5)
- **[TailwindCSS 4.0](https://tailwindcss.com/)** - Framework CSS
- **JavaScript (ES6+)** - Interatividade
- **Font Awesome** - Ícones
- **[Vite](https://vitejs.dev/)** - Build tool e bundler

### Ferramentas de Desenvolvimento
- **Composer** - Gerenciador de dependências PHP
- **NPM** - Gerenciador de dependências JavaScript
- **Concurrently** - Execução paralela de scripts

---

## 📦 Instalação

### Pré-requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18.x e NPM
- MySQL >= 8.0
- Git

### Passo a Passo

1. **Clone o repositório**
```bash
git clone https://github.com/WebLucasDev/tcc.git
cd tcc
```

2. **Instale as dependências do PHP**
```bash
composer install
```

3. **Instale as dependências do Node.js**
```bash
npm install
```

4. **Configure o arquivo de ambiente**
```bash
cp .env.example .env
```

5. **Edite o arquivo `.env` e configure:**
```env
APP_NAME="Metre Ponto"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306 (ou a porta que desejar usar)
DB_DATABASE=metre_ponto
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

MAIL_MAILER=smtp
MAIL_HOST=seu_servidor_smtp
MAIL_PORT=587
MAIL_USERNAME=seu_email
MAIL_PASSWORD=sua_senha_email
MAIL_FROM_ADDRESS="noreply@metreponto.com"
MAIL_FROM_NAME="${APP_NAME}"
```

6. **Gere a chave da aplicação**
```bash
php artisan key:generate
```

7. **Execute as migrations**
```bash
php artisan migrate
```

8. **Execute os seeders (opcional)**
```bash
php artisan db:seed
```

9. **Compile os assets**
```bash
npm run build
```

---

## 🚀 Uso (ambiente de desenvolvimento)

Execute o comando abaixo para iniciar simultaneamente o servidor Laravel, a fila e o Vite:

```bash
composer run dev
```

A aplicação estará disponível em: `http://localhost:8000/login`

## 📁 Estrutura do Projeto

```
tcc/
├── app/
│   ├── Enums/                      # Enumeradores
│   │   ├── CollaboratorStatusEnum.php
│   │   ├── SolicitationStatusEnum.php
│   │   ├── TimeTrackingStatusEnum.php
│   │   └── WorkHoursStatusEnum.php
│   ├── Http/
│   │   ├── Controllers/            # Controladores
│   │   ├── Middleware/             # Middlewares personalizados
│   │   └── Requests/               # Form Requests
│   ├── Mail/                       # Classes de e-mail
│   │   └── ForgotPasswordMail.php
│   └── Models/                     # Models Eloquent
│       ├── CollaboratorModel.php   # Modelo de Colaborador
│       ├── DepartmentModel.php     # Modelo de Departamento
│       ├── PositionModel.php       # Modelo de Cargo
│       ├── SolicitationModel.php   # Modelo de Solicitação
│       ├── TimeTrackingModel.php   # Modelo de Ponto
│       ├── User.php                # Modelo de Administrador
│       └── WorkHoursModel.php      # Modelo de Jornada
├── database/
│   ├── migrations/                 # Migrations do banco de dados
│   └── seeders/                    # Seeders
├── public/                         # Arquivos públicos
│   ├── build/                      # Assets compilados
│   └── imgs/                       # Imagens
├── resources/
│   ├── css/                        # Arquivos CSS
│   ├── js/                         # Arquivos JavaScript
│   └── views/                      # Views Blade
├── routes/
│   ├── web.php                     # Rotas web
│   └── console.php                 # Comandos Artisan
└── tests/                          # Testes automatizados
```

---

## 🗄️ Modelos de Dados

### User (Administrador)
- Sistema de autenticação para gestores
- Acesso completo ao sistema

### CollaboratorModel (Colaborador)
- Dados pessoais (nome, CPF, e-mail, telefone, endereço, etc...)
- Vinculação a cargo, departamento e jornada de trabalho
- Status (ativo/inativo)
- Relacionamentos:
  - `belongsTo`: PositionModel, WorkHoursModel
  - `hasMany`: TimeTrackingModel

### DepartmentModel (Departamento)
- Estrutura organizacional
- Relacionamentos:
  - `hasMany`: PositionModel
  - `hasManyThrough`: CollaboratorModel

### PositionModel (Cargo)
- Vinculado a departamento
- Relacionamentos:
  - `belongsTo`: DepartmentModel
  - `hasMany`: CollaboratorModel

### WorkHoursModel (Jornada de Trabalho)
- Configuração de 7 dias da semana
- Até 2 turnos por dia
- Cálculo automático de horas semanais
- Validação CLT (44h semanais)
- Relacionamentos:
  - `hasMany`: CollaboratorModel

### TimeTrackingModel (Registro de Ponto)
- 4 marcações diárias
- Observações individuais por marcação
- Status (ausente, incompleto, completo)
- Cálculo automático de horas trabalhadas
- Relacionamentos:
  - `belongsTo`: CollaboratorModel

### SolicitationModel (Solicitação de Correção)
- Horários antigos e novos
- Motivo e comentário do admin
- Status (pendente, aprovada, rejeitada, cancelada)
- Relacionamentos:
  - `belongsTo`: CollaboratorModel, TimeTrackingModel

---

## 🔒 Segurança

- ✅ Autenticação com guards separados
- ✅ Senhas hasheadas com bcrypt
- ✅ Proteção CSRF em formulários
- ✅ Validação server-side obrigatória
- ✅ Middleware de autorização por perfil
- ✅ Isolamento de sessões por tipo de usuário
- ✅ Logs de erros (sem exposição de dados sensíveis)

---

## 📝 Licença

Este projeto está sob a licença **MIT**. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 👨‍💻 Autor

**Lucas Venancio Silva Tiago** (WebLucasDev)

- GitHub: [@WebLucasDev](https://github.com/WebLucasDev)
- Linkedin: (https://www.linkedin.com/in/lucasvenancio-dev/)

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir uma **issue** ou enviar um **pull request**.

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

---

## 📞 Suporte

Se você tiver alguma dúvida ou precisar de ajuda, abra uma [issue](https://github.com/WebLucasDev/tcc/issues) no GitHub.

---
