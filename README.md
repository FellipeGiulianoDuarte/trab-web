# Type the Colour, Not the Word - Game Implementation Status

## ✅ COMPLETED FEATURES

### Game Implementation
- **Core Game Logic**: "Type the Colour, Not the Word" game fully implemented
- **Game Mechanics**: 
  - Words displayed in different colors than their meaning
  - 60-second timer
  - 10 points per correct answer
  - Real-time score tracking
- **Visual Feedback**: Color-coded feedback for correct/incorrect answers
- **Responsive Design**: Works on desktop and mobile devices

### Backend Integration
- **Score Saving**: Automatic score saving to database after each game
- **Database Schema**: Complete implementation with users, games, leagues, and league_members tables
- **Authentication**: User registration, login, and session management
- **Security**: Password hashing, SQL injection protection, input validation

### Frontend Features
- **Game Interface**: Clean, intuitive game interface with instructions
- **Score Display**: Personal statistics, game history, and leaderboards
- **Navigation**: Easy navigation between game, scores, and leagues
- **Styling**: Modern CSS with responsive design

### Files Created/Modified:
- `public/game_page.php` - Main game interface
- `public/js/game.js` - Complete game logic
- `public/scores.php` - Score display and leaderboards  
- `public/backend/game_logic/save_score.php` - Score saving backend
- `public/css/style.css` - Enhanced with game-specific styles
- `public/js/script.js` - General utility functions

## 🎮 How to Play
1. Register an account and log in
2. Click "Jogar" from the main menu
3. Click "Iniciar" to start the game
4. A word will appear in a color different from its meaning
5. Type the COLOR you see (not the word itself)
6. Press Enter to submit your answer
7. Try to get as many correct answers as possible in 60 seconds!

## 🏆 Scoring System
- Each correct answer: **+10 points**
- Incorrect answers: **0 points** (no penalty)
- Personal statistics track total and weekly scores
- Global and weekly leaderboards available

---

<!-- filepath: c:\Users\Fellipe\Desktop\facul\trab-web\README.md -->
game to be based on: Click the Colour and Not the Word
But we are going to make: Type the colour and not the word

Especificação de Trabalho Prático 01/2025
O trabalho prático envolve a criação de uma aplicação WEB completa. Ou seja, que inclua a implementação de front-end, back-end e que possua integração com um banco de dados.

Tema
A aplicação deve implementar um jogo de digitação utilizando Javascript e utilizar PHP para armazenar e exibir quadros de pontuação.

O funcionamento é o seguinte:

O usuário deve se registrar e se autenticar para acessar o sistema;
Uma vez autenticado, o usuário pode jogar partidas de um jogo de digitação;
A cada partida, o usuário acumula pontos, exibidos pelo sistema.
O usuário pode acessar seu histórico de partidas (e pontuação), bem como diferentes quadros de pontuação (pelo menos geral e ligas)
O jogo de digitação a ser implementado é livre, desde que envolva o princípio básico de digitação correta de palavras. Os jogos typing.com e ztype são bons exemplos desse princípio.

O sistema deve disponibilizar a inscrição do usuário em ligas. Ligas são um conjunto de usuários que competem entre si. O usuário pode criar e se cadastrar em ligas. Para o cadastro do usuário em uma liga é necessário uma palavra-chave, definida pelo criador da liga.

A pontuação da liga deve ser exibida de duas formas:

pontuação desde a criação da liga; e
pontuação semanal.
Além da pontuação em suas respectivas ligas, o usuário também pode verificar sua pontuação geral, envolvendo todos os jogadores. Esse quadro também deve apresentar a pontuação desde a criação do sistema e pontuação semanal.

A qualquer momento, o usuário pode acessar um relatório com os dados de todas as partidas jogadas, com suas respectivas pontuações.

Requisitos
A aplicação desenvolvida deve atender os seguintes requisitos:

Front-end:

Uso de HTML5, CSS3 e JS;
Interface amigável; ;
Validação de campos de formulário;
Implementação do Jogo de digitação completamente em JS;
Back-end;

Integração com um banco de dados:
Sistema de autenticação/autorização de usuário(s) salvo(s) em banco de dados;
Validação de campos de formulário e outras informações recebidas.
Ambiente de Desenvolvimento
O sistema deve ser desenvolvido utilizando apenas os recursos demonstrados na disciplina DS122 (PHP, Javascript (JQuery), HTML5, CSS3 e algum banco de dados);
É permitido o uso de frameworks front-end, como Bootstrap e W3.CSS;
Não é permitido o uso de frameworks back-end.
Entrega
Datas de entrega e defesa no moodle.

O trabalho pode ser feito em grupos de 2 até 4 alunos.

O código deve ser entregue através do moodle da disciplina, por meio de link para repositório git.

O trabalho deverá ser defendido através de uma rápida demonstração de seu funcionamento e explicação do código. A defesa é realizada apenas para o professor, não para a turma.

Documentação
O repositório deverá conter um arquivo chamado README.md com a descrição do sistema e de seu funcionamento. Deve-se utilizar a sintaxe correta da linguagem Markdown nesse documento (para saber mais, consulte: https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet).

Critério para avaliação
Os critérios para avaliação serão os seguintes:

Defesa e conceitos [3 pontos]:

Estrutura e clareza do código [1 ponto]
Qualidade da defesa [1 ponto];
Domínio do código [1 ponto];
Funcionalidades e implementação [7 pontos]:

Qualidade da interface do usuário [1 ponto];
Funcionamento do Jogo de digitação [3 ponto];
Funcionamento da aplicação back-end [3 pontos];
Atenção: em nenhuma hipótese serão aceitos trabalhos com qualquer traço de plágio. A identificação de plágio implica em nota zero a todos os integrantes do grupo.


