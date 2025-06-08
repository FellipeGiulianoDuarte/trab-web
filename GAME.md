**Objetivo do Projeto:**

Criar um jogo web chamado **"Type the Colour, Not the Word"**. O jogo consiste em exibir para o usuário uma palavra que é um nome de cor (ex: "VERMELHO"), mas pintada em uma cor CSS diferente (ex: a palavra "VERMELHO" em azul). O jogador deve digitar o nome da cor em que a palavra está pintada, e não a palavra em si. O jogo terá um temporizador de 60 segundos e um sistema de pontuação.

---

### **Estrutura de Arquivos Sugerida:**

* `public/index.html`: Estrutura principal do jogo.
* `public/css/style.css`: Estilização da página e dos elementos do jogo.
* `public/js/game.js`: Lógica principal do jogo no front-end.
* `src/game_logic/save_score.php`: Script PHP para receber e salvar a pontuação no banco de dados.

---

### **Detalhamento dos Componentes:**

#### **1. Front-end: `index.html`**

Crie a estrutura HTML básica contendo:
* Um título `<h1>` para o nome do jogo.
* Uma área para exibir a palavra colorida (ex: `<div id="word-display"></div>`).
* Um campo de texto para o jogador digitar a resposta (ex: `<input type="text" id="color-input">`).
* Um elemento para mostrar a pontuação atual (ex: `<span>Pontuação: <span id="score">0</span></span>`).
* Um elemento para mostrar o tempo restante (ex: `<span>Tempo: <span id="timer">60</span>s</span>`).
* Um botão para iniciar o jogo (ex: `<button id="start-button">Iniciar</button>`).
* Uma área para exibir a pontuação final e mensagens de fim de jogo (ex: `<div id="game-over-message"></div>`).

#### **2. Estilização: `style.css`**

* Centralize os elementos principais do jogo na tela.
* Use uma fonte grande e legível para a palavra exibida em `#word-display`.
* Garanta que a área do jogo seja visualmente clara e sem distrações.

#### **3. Lógica do Jogo: `game.js`**

Implemente a lógica do jogo seguindo estes passos:

* **Variáveis Iniciais:**
    * Crie um array de nomes de cores em português (ex: `['vermelho', 'azul', 'verde', 'amarelo', 'laranja', 'roxo']`).
    * Variáveis para pontuação (`score`), tempo (`timeLeft`), e estado do jogo (`isPlaying`).

* **Função `startGame()`:**
    * Chamada quando o botão "Iniciar" é clicado.
    * Reseta a pontuação para 0 e o tempo para 60 segundos.
    * Define `isPlaying` como `true`.
    * Desabilita o botão "Iniciar" e habilita o campo de input.
    * Inicia o temporizador (`setInterval` que decrementa `timeLeft` a cada segundo).
    * Chama a função para gerar a primeira palavra.

* **Função `generateWord()`:**
    * Escolhe aleatoriamente uma palavra do array (será o texto exibido).
    * Escolhe aleatoriamente uma cor do mesmo array (será a cor CSS da palavra).
    * **Regra Crítica:** Garanta que a palavra escolhida e a cor escolhida **sejam diferentes**. Se forem iguais, sorteie a cor novamente.
    * Atualize o `textContent` e o `style.color` do elemento `#word-display`.

* **Manipulação de Input:**
    * Adicione um `event listener` ao campo `#color-input` para o evento `keyup`.
    * Quando a tecla "Enter" for pressionada:
        1.  Verifique se o jogo ainda está ativo (`isPlaying === true`).
        2.  Obtenha o valor do input e **normalize-o** para minúsculas e sem espaços extras (`toLowerCase().trim()`).
        3.  Compare a resposta normalizada com a cor CSS atual da palavra.
        4.  Se a resposta estiver **correta**, adicione **10 pontos** à variável `score` e atualize a exibição da pontuação.
        5.  Se estiver **incorreta**, não faça nada com a pontuação.
        6.  Limpe o campo de input.
        7.  Chame `generateWord()` para exibir a próxima palavra.

* **Lógica do Temporizador e Fim de Jogo:**
    * O `setInterval` deve atualizar o elemento `#timer` a cada segundo.
    * Quando `timeLeft` chegar a 0:
        1.  Pare o temporizador (`clearInterval`).
        2.  Defina `isPlaying` como `false`.
        3.  Desabilite o campo de input para impedir novas respostas.
        4.  Exiba uma mensagem de "Fim de Jogo!" com a pontuação final.
        5.  Chame a função para salvar a pontuação no back-end.

* **Comunicação com o Back-end (`saveScore()`):**
    * Crie uma função que use a API `fetch` para enviar uma requisição `POST` para `src/game_logic/save_score.php`.
    * O corpo da requisição deve conter a pontuação final em formato JSON (ex: `{ "score": 120 }`).

#### **4. Back-end: `save_score.php`**

* **Receber Dados:**
    * Leia o corpo da requisição `POST` e decodifique o JSON para obter a pontuação (ex: `$data = json_decode(file_get_contents('php://input'), true); $score = $data['score'];`).
* **Lógica de Sessão (Simulado):**
    * Assuma que existe uma sessão de usuário ativa para obter o `user_id` (ex: `session_start(); $userId = $_SESSION['user_id'];`). Se não houver, ignore o salvamento.
* **Validação:**
    * Verifique se `$score` é um número válido e se `$userId` existe.
* **Salvar no Banco de Dados:**
    * Conecte-se ao banco de dados.
    * Prepare e execute uma query SQL `INSERT` para salvar os dados na tabela `games`.
    * **Tabela `games`:** deve ter colunas como `id` (AUTO_INCREMENT), `user_id` (FOREIGN KEY), `score` (INT), e `played_at` (TIMESTAMP DEFAULT CURRENT_TIMESTAMP).
    * Exemplo de Query: `INSERT INTO games (user_id, score) VALUES (?, ?)`
* **Retorno:**
    * Retorne uma resposta JSON indicando sucesso ou falha (ex: `echo json_encode(['status' => 'success']);`).