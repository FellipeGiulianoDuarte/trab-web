# Guia de Implementação Passo a Passo: Jogo de Digitação

Este documento descreve os passos para implementar a aplicação WEB de jogo de digitação, conforme especificado no arquivo `README.md`.

**Stack Tecnológica:**
*   **Front-end:** HTML5, CSS3, JavaScript (jQuery opcional, Bootstrap/W3.CSS opcional)
*   **Back-end:** PHP
*   **Banco de Dados:** MySQL
*   **Servidor Web:** Apache

1.  **Estrutura de Pastas do Projeto**
    ```
    /trab-web/
    |-- public/
    |   |-- css/
    |   |   |-- style.css
    |   |-- js/
    |   |   |-- game.js
    |   |   |-- script.js
    |   |-- img/
    |   |-- index.php
    |   |-- login.php
    |   |-- register.php
    |   |-- game_page.php
    |   |-- scores.php
    |   |-- leagues.php
    |-- src/
    |   |-- db/
    |   |   |-- connection.php
    |   |-- includes/
    |   |   |-- header.php
    |   |   |-- footer.php
    |   |-- auth/
    |   |   |-- handle_register.php
    |   |   |-- handle_login.php
    |   |   |-- logout.php
    |   |-- game_logic/
    |   |   |-- save_score.php
    |   |-- league_logic/
    |   |   |-- create_league.php
    |   |   |-- join_league.php
    |-- README.md
    |-- IMPLEMENTATION_GUIDE.md
    |-- db_schema.sql
    ```

## Fase 1: Banco de Dados (MySQL)

1.  **Modelagem do Banco de Dados:**
    *   **`users`**: `id`, `username`, `password_hash`, `email`, `created_at`
    *   **`games`**: `id`, `user_id` (FK to users), `score`, `played_at`
    *   **`leagues`**: `id`, `name`, `creator_user_id` (FK to users), `keyword`, `created_at`
    *   **`league_members`**: `id`, `league_id` (FK to leagues), `user_id` (FK to users), `joined_at`
    *   (Considere tabelas adicionais se necessário para pontuações semanais ou outras funcionalidades complexas).
2.  **Criação do Banco de Dados e Tabelas:**
    *   Crie um arquivo `db_schema.sql` com os comandos `CREATE DATABASE` e `CREATE TABLE`.
    *   Execute o script no seu servidor MySQL.
3.  **Conexão PHP com MySQL:**
    *   Crie `src/db/connection.php` para gerenciar a conexão com o banco de dados. Use a extensão `mysqli` ou `PDO`.

## Fase 2: Back-end (PHP)

1.  **Autenticação de Usuário:**
    *   **Registro (`public/register.php`, `src/auth/handle_register.php`):**
        *   Formulário HTML para coletar nome de usuário, email e senha.
        *   PHP para validar dados, verificar se usuário já existe, hashear a senha (ex: `password_hash()`) e salvar no banco.
    *   **Login (`public/login.php`, `src/auth/handle_login.php`):**
        *   Formulário HTML para coletar nome de usuário/email e senha.
        *   PHP para verificar credenciais no banco (usar `password_verify()`), iniciar sessão (`session_start()`) e armazenar `user_id` na sessão.
    *   **Logout (`src/auth/logout.php`):**
        *   Destruir a sessão (`session_destroy()`) e redirecionar para a página de login.
    *   **Proteção de Páginas:** Crie um script para verificar se o usuário está logado no início de páginas restritas.
2.  **Gerenciamento de Ligas:**
    *   **Criação (`public/leagues.php`, `src/league_logic/create_league.php`):**
        *   Formulário para nome da liga e palavra-chave.
        *   PHP para salvar a nova liga no banco, associando ao usuário criador.
    *   **Inscrição (`public/leagues.php`, `src/league_logic/join_league.php`):**
        *   Interface para listar ligas disponíveis e campo para inserir palavra-chave.
        *   PHP para verificar a palavra-chave e adicionar o usuário à tabela `league_members`.
3.  **Lógica do Jogo (Servidor):**
    *   **Salvar Pontuação (`src/game_logic/save_score.php`):**
        *   Receber pontuação do front-end (via AJAX/Fetch).
        *   Validar e salvar na tabela `games`, associando ao `user_id` da sessão.
4.  **Exibição de Pontuações e Histórico:**
    *   **Histórico de Partidas do Usuário (`public/scores.php`):**
        *   PHP para buscar no banco todas as partidas do usuário logado e exibi-las.
    *   **Quadro de Pontuação Geral (`public/scores.php`):**
        *   PHP para calcular e exibir pontuações totais e semanais de todos os jogadores.
        *   Para pontuação semanal, filtre os jogos pela data (`played_at`).
    *   **Quadro de Pontuação de Ligas (`public/leagues.php` ou página dedicada):**
        *   PHP para calcular e exibir pontuações totais e semanais dos membros de uma liga específica.
5.  **Relatórios:**
    *   **Relatório de Partidas do Usuário (`public/profile.php` ou `public/reports.php`):**
        *   PHP para buscar e exibir todas as partidas jogadas pelo usuário logado com suas respectivas pontuações.
6.  **Validação de Dados no Back-end:**
    *   Implemente validações robustas para todos os dados recebidos de formulários ou requisições AJAX (tipo, tamanho, formato, etc.).

## Fase 3: Front-end (HTML, CSS, JS)

1.  **Estrutura HTML Básica:**
    *   Crie arquivos HTML/PHP para as páginas principais:
        *   `index.php` (pode ser a página de login ou um dashboard se logado)
        *   `login.php`
        *   `register.php`
        *   `game_page.php` (onde o jogo será carregado)
        *   `scores.php` (placares gerais e histórico pessoal)
        *   `leagues.php` (visualizar, criar, entrar em ligas, placares de ligas)
    *   Use `src/includes/header.php` e `src/includes/footer.php` para partes comuns das páginas.
2.  **Estilização com CSS (`public/css/style.css`):**
    *   Crie uma interface amigável e responsiva.
    *   Utilize Bootstrap ou W3.CSS se desejar para acelerar o desenvolvimento da UI.
3.  **Validação de Formulários com JS (`public/js/script.js`):**
    *   Adicione validação nos campos de formulário (registro, login, criação de liga) para feedback imediato ao usuário antes do envio.
4.  **Implementação do Jogo de Digitação (`public/js/game.js`):**
    *   **Lógica do Jogo "Type the colour and not the word":**
        *   Gerar aleatoriamente uma palavra (nome de uma cor, ex: "VERMELHO").
        *   Exibir essa palavra em uma cor aleatória (ex: a palavra "VERMELHO" pintada de azul).
        *   O usuário deve digitar o nome da *cor* em que a palavra está pintada (neste exemplo, "AZUL"), e não a palavra em si.
    *   **Elementos da UI do Jogo:**
        *   Área para exibir a palavra colorida.
        *   Campo de input para o usuário digitar.
        *   Exibição de pontuação atual, tempo restante (se aplicável).
    *   **Mecânica:**
        *   Iniciar/pausar jogo.
        *   Contador de tempo ou vidas.
        *   Verificar a resposta do usuário.
        *   Atualizar pontuação.
        *   Gerar próxima palavra/cor.
        *   Fim de jogo e exibição da pontuação final.
    *   **Comunicação com o Back-end:**
        *   Ao final da partida, use `fetch` ou `XMLHttpRequest` (AJAX) para enviar a pontuação para `src/game_logic/save_score.php`.
5.  **Interação com o Back-end (JS):**
    *   Use AJAX para carregar dinamicamente placares, listas de ligas, etc., sem recarregar a página inteira, onde apropriado.

## Fase 4: Funcionalidades Adicionais e Requisitos

*   **Interface Amigável:** Revise todo o fluxo do usuário e o design para garantir que seja intuitivo e agradável.
*   **Validação de Campos:** Certifique-se de que a validação está implementada tanto no front-end (JS) quanto no back-end (PHP).

## Fase 5: Testes e Refinamento

1.  **Testes Manuais:**
    *   Teste todas as funcionalidades: registro, login, logout.
    *   Jogar várias partidas, testar a lógica de pontuação.
    *   Criar e entrar em ligas, verificar a palavra-chave.
    *   Verificar todos os quadros de pontuação (geral, ligas, total, semanal).
    *   Verificar o relatório de partidas.
    *   Testar em diferentes navegadores.
2.  **Correção de Bugs:** Identifique e corrija quaisquer erros ou comportamentos inesperados.
3.  **Otimização:**
    *   Otimize consultas SQL.
    *   Melhore a performance do JavaScript do jogo, se necessário.
    *   Verifique a segurança (ex: prevenção contra SQL Injection, XSS).

## Fase 7: Documentação Final

1.  **Atualizar `README.md`:**
    *   Descreva o sistema finalizado.
    *   Explique como executar o projeto.
    *   Detalhe o funcionamento do jogo e das funcionalidades de ligas e pontuações.
    *   Siga a sintaxe Markdown corretamente.

## Considerações Adicionais

*   **Segurança:**
    *   Use senhas hasheadas (`password_hash` e `password_verify`).
    *   Previna SQL Injection (use prepared statements com `mysqli` ou `PDO`).
    *   Previna Cross-Site Scripting (XSS) (sanitize as saídas com `htmlspecialchars`).
    *   Gerencie sessões de forma segura.
*   **Boas Práticas:**
    *   Comente seu código.
    *   Mantenha o código organizado e modular.
    *   Use controle de versão (Git) desde o início.
