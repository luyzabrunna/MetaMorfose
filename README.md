# MetaMorfose

> Sistema web de gestão de metas de estudo — Projeto de Extensão 2 | ADS | Instituto Federal

---

## 📋 Sobre o projeto

O **MetaMorfose** é uma aplicação web desenvolvida para ajudar estudantes a organizarem seus objetivos de estudo de forma clara e motivadora. Com ele é possível criar metas, registrar sessões de estudo e acompanhar a evolução através de um dashboard intuitivo.

---

## 👩‍💻 Equipe

| Nome | Responsabilidade |
|---|---|
| Brunna Luyza | Usuário, Autenticação e Frontend |
| Ana Carla | Metas, Sessões e Frontend |

---

## 🚀 Funcionalidades

- ✅ Cadastro e login de usuários com sessão PHP
- ✅ Criar, editar e excluir metas de estudo
- ✅ Registrar sessões de estudo vinculadas às metas
- ✅ Acompanhamento de progresso com barras visuais
- ✅ Dashboard com metas recentes, sessões recentes, prazo mais próximo e total de horas estudadas
- ✅ Status automático da meta via triggers no banco (não iniciada → em andamento → concluída)
- ✅ Interface responsiva para celular, tablet, notebook e monitor

---

## 🛠️ Tecnologias utilizadas

| Camada | Tecnologia |
|---|---|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP 8.2 (padrão MVC) |
| Banco de dados | MySQL 8.0 |
| Servidor | Apache (via Docker) |
| Ambiente | Docker + Docker Compose |
| Fontes | Inter (@fontsource) |
| Ícones | Font Awesome 6.5 |

---

## 📁 Estrutura do projeto

```
METAMORFOSE/
├── src/
│   ├── app/
│   │   ├── config/
│   │   │   └── database.php
│   │   ├── controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── logout.php
│   │   │   └── MetaController.php
│   │   ├── models/
│   │   │   ├── MetaModel.php
│   │   │   └── UserModel.php
│   │   └── views/
│   │       ├── cadastro.php
│   │       ├── criarmeta.php
│   │       ├── dashboard.php
│   │       ├── detalhemeta.php
│   │       ├── landingpage.php
│   │       ├── login.php
│   │       ├── sessaoestudo.php
│   │       ├── visualizacaometas.php
│   │       └── visualizacaosessao.php
│   ├── assets/
│   │   ├── css/
│   │   ├── images/
│   │   └── js/
│   └── index.php
├── docker-compose.yml
├── Dockerfile
├── MetaMorfose.sql
└── README.md
---

## ⚙️ Como executar o projeto

### Pré-requisitos
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado

### Passo a passo

**1. Clone o repositório**
```bash
git clone https://github.com/seu-usuario/metamorfose.git
cd metamorfose
```

**2. Suba o ambiente com Docker**
```bash
docker-compose up -d
```

**3. Acesse o phpMyAdmin e importe o banco**
- Abra: [http://localhost:8081](http://localhost:8081)
- Usuário: `root` | Senha: `root`
- Crie o banco `metamorfose` e importe o arquivo `banco.sql`

**4. Acesse o sistema**
- Sistema: [http://localhost:8080](http://localhost:8080)
- phpMyAdmin: [http://localhost:8081](http://localhost:8081)

---

## 🗄️ Banco de dados

O banco possui 3 tabelas principais e triggers automáticos:

```
usuario  →  meta  →  sessao
```

- **Triggers:** atualizam `horas_estudadas` e `status` da meta automaticamente ao inserir, editar ou excluir uma sessão.

---

## 📌 Observações

- Projeto desenvolvido para fins acadêmicos
- Disciplina: Extensão 2
- Curso: Análise e Desenvolvimento de Sistemas (ADS)
- Instituição: Instituto Federal

---

<p align="center">Feito com 💜 por Brunna Luyza e Ana Carla</p>
