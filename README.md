# Documentação do Projeto Web Game

## Descrição do Sistema

Este projeto consiste em um jogo web interativo onde os jogadores podem competir individualmente e em ligas, acompanhando suas pontuações e rankings. O sistema permite que usuários se cadastrem, façam login, joguem, criem e participem de ligas, e visualizem placares globais e de ligas específicas.

## Como Executar o Projeto Localmente com XAMPP

Para executar o projeto em seu ambiente local utilizando o XAMPP, siga os passos abaixo:

1.  **Instale o XAMPP:**
    *   Baixe e instale a versão mais recente do XAMPP compatível com o seu sistema operacional a partir do site oficial: [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html)

2.  **Clone o Repositório:**
    *   Clone este repositório para dentro da pasta `htdocs` do seu diretório de instalação do XAMPP.
    *   Exemplo: `C:\xampp\htdocs\nome_do_seu_projeto`

3.  **Inicie os Módulos do XAMPP:**
    *   Abra o Painel de Controle do XAMPP.
    *   Inicie os módulos "Apache" e "MySQL".

4.  **Importe o Banco de Dados:**
    *   Acesse o phpMyAdmin através do seu navegador (geralmente em `http://localhost/phpmyadmin`).
    *   Crie um novo banco de dados (ex: `meu_jogo_db`).
    *   Selecione o banco de dados criado e vá para a aba "Importar".
    *   Clique em "Escolher arquivo" e selecione o arquivo `db_schema.sql` localizado na raiz do projeto.
    *   Clique em "Executar" para importar o esquema do banco de dados.

5.  **Configure a Conexão com o Banco de Dados:**
    *   Verifique o arquivo `public/backend/db/connection.php`.
    *   Certifique-se de que as credenciais do banco de dados (`$servername`, `$username`, `$password`, `$dbname`) correspondem à sua configuração do MySQL no XAMPP e ao nome do banco de dados que você criou. Por padrão, o usuário do XAMPP costuma ser `root` sem senha.

6.  **Acesse o Projeto:**
    *   Abra seu navegador e acesse o projeto através do seguinte URL (substitua `nome_do_seu_projeto` pelo nome da pasta onde você clonou o projeto):
        `http://localhost/nome_do_seu_projeto/public/`
    *   Você deverá ser redirecionado para a página de login ou para a página inicial do jogo.

## Funcionamento do Jogo

O jogo web chama-se **"Type the Colour, Not the Word"**. A mecânica principal consiste em exibir para o usuário uma palavra que é um nome de cor (ex: "VERMELHO"), mas esta palavra é pintada em uma cor CSS diferente (ex: a palavra "VERMELHO" escrita na cor azul). O jogador deve digitar rapidamente o nome da cor em que a palavra está pintada, e não a palavra em si. O jogo possui um temporizador de 60 segundos e um sistema de pontuação que recompensa respostas corretas.

*   **Objetivo:** O objetivo principal é alcançar a maior pontuação possível dentro do limite de tempo de 60 segundos, digitando corretamente o nome da cor em que as palavras são exibidas.
*   **Controles:** O jogador utiliza o teclado para digitar o nome da cor no campo de texto e pressiona "Enter" para submeter a resposta.
*   **Pontuação:** A pontuação é calculada da seguinte forma:
    *   Resposta correta: +10 pontos.
    *   Resposta incorreta: 0 pontos (nenhuma penalidade, mas também nenhum ganho).
*   **Game Over:** O jogo termina quando o temporizador de 60 segundos chega a zero. Após o game over, a pontuação final do jogador é registrada e exibida, e há uma tentativa de salvá-la no banco de dados se o usuário estiver logado.

## Funcionalidades de Ligas e Pontuações

O sistema oferece funcionalidades robustas para gerenciamento de ligas e visualização de pontuações, incentivando a competição entre os jogadores.

### Ligas

*   **Criação de Ligas:** Usuários logados podem criar suas próprias ligas, definindo um nome para a liga. O criador da liga se torna automaticamente um membro.
*   **Participar de Ligas:** Os jogadores podem visualizar uma lista de ligas existentes e optar por entrar nelas.
*   **Sair de Ligas:** Jogadores podem sair de ligas que não desejam mais participar.
*   **Visualização de Ligas:** É possível ver os membros de cada liga e suas respectivas pontuações dentro daquela liga.

### Pontuações

*   **Placar Individual:** Cada jogador tem suas pontuações registradas e pode visualizar seu histórico de melhores pontuações.
*   **Placar Global:** Existe um placar global que exibe os melhores jogadores do sistema, independentemente das ligas.
*   **Placar de Ligas:** Cada liga possui seu próprio placar, mostrando o ranking dos jogadores participantes daquela liga específica. As pontuações consideradas para o ranking da liga são aquelas obtidas enquanto o jogador é membro da liga.