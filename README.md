# PHP SSR + CSR Notes (MySQL)

Pequeno projeto demonstrando **renderização no servidor (SSR)** com PHP + **elementos renderizados no cliente (CSR)** com JavaScript.

**Tecnologias:** PHP, HTML, CSS, JavaScript e MySQL (PDO).

## Funcionalidades
- Lista de anotações é **renderizada no servidor** (PHP).
- Criação e exclusão de anotações via **fetch/AJAX** (renderização no cliente).
- Atualização em tempo real da lista (sem recarregar a página).
- Contador de caracteres e ordenação **no cliente**.

## Estrutura
```text
/
├─ index.php        # SSR: renderiza HTML inicial + lista
├─ api.php          # API simples (JSON) para CRUD básico (create/delete)
├─ db.php           # conexão PDO (ajuste as credenciais)
├─ database.sql     # script para criar BD e tabela
└─ assets/
   ├─ style.css
   └─ app.js
```

## Requisitos
- PHP 8+ com extensão PDO MySQL
- MySQL 5.7+ ou MariaDB compatível

## Configuração
1. Crie o banco e a tabela:
   ```sql
   -- no MySQL
   CREATE DATABASE notes_app CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
   USE notes_app;
   SOURCE database.sql;  -- ou cole o conteúdo do arquivo
   ```

2. Ajuste as credenciais em `db.php`:
   ```php
   $DB_HOST = '127.0.0.1';
   $DB_NAME = 'notes_app';
   $DB_USER = 'root';
   $DB_PASS = 'senha';
   ```

3. Rode localmente (opção A com servidor embutido):
   ```bash
   php -S 127.0.0.1:8000
   ```
   Abra http://127.0.0.1:8000

   (ou opção B) configure o virtual host/Nginx apontando para a pasta do projeto.

## Endpoints
- `GET  /api.php?notes=1` → lista notas (JSON)
- `POST /api.php` → cria nota: body `title`, `content`
- `DELETE /api.php?id=ID` → exclui nota por ID

## Publicação no GitHub
1. Inicie o repositório e faça o push:
   ```bash
   git init
   git add .
   git commit -m "PHP SSR + CSR Notes (MySQL)"
   git branch -M main
   # substitua <seu-usuario> e <repo>
   git remote add origin https://github.com/<seu-usuario>/<repo>.git
   git push -u origin main
   ```
2. Torne o repositório **público** e adicione o colaborador:
   - Settings → Collaborators → Add people → `rennanmaia` (ou e-mail `rennanmaia@gmail.com`).

## Observações de segurança
- Exemplo educacional. Para produção, adicione proteção CSRF, validação mais robusta, autenticação e logging.
