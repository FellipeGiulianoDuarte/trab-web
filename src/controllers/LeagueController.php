<?php

class LeagueController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToLeagues();
            return;
        }

        // Get form data
        $league_name = trim($_POST['league_name'] ?? '');
        $keyword = trim($_POST['keyword'] ?? '');
        $creator_user_id = $_SESSION['user_id'];

        // Validate input
        if (empty($league_name) || empty($keyword)) {
            $_SESSION['league_error'] = 'Nome da liga e palavra-chave são obrigatórios.';
            $this->redirectToLeagues();
            return;
        }

        if (strlen($league_name) < 3 || strlen($league_name) > 255) {
            $_SESSION['league_error'] = 'Nome da liga deve ter entre 3 e 255 caracteres.';
            $this->redirectToLeagues();
            return;
        }

        if (strlen($keyword) < 3 || strlen($keyword) > 50) {
            $_SESSION['league_error'] = 'Palavra-chave deve ter entre 3 e 50 caracteres.';
            $this->redirectToLeagues();
            return;
        }

        if (strpos($keyword, ' ') !== false) {
            $_SESSION['league_error'] = 'Palavra-chave não deve conter espaços.';
            $this->redirectToLeagues();
            return;
        }

        try {
            // Check if league name already exists
            $check_name_query = "SELECT id FROM leagues WHERE name = ?";
            $stmt = $this->conn->prepare($check_name_query);
            $stmt->bind_param("s", $league_name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $_SESSION['league_error'] = 'Já existe uma liga com este nome.';
                $this->redirectToLeagues();
                return;
            }

            // Check if keyword already exists
            $check_keyword_query = "SELECT id FROM leagues WHERE keyword = ?";
            $stmt = $this->conn->prepare($check_keyword_query);
            $stmt->bind_param("s", $keyword);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $_SESSION['league_error'] = 'Já existe uma liga com esta palavra-chave.';
                $this->redirectToLeagues();
                return;
            }

            // Create the league
            $insert_query = "INSERT INTO leagues (name, keyword, creator_user_id, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($insert_query);
            $stmt->bind_param("ssi", $league_name, $keyword, $creator_user_id);

            if ($stmt->execute()) {
                $league_id = $this->conn->insert_id;

                // Automatically add creator as a member
                $member_query = "INSERT INTO league_members (league_id, user_id, joined_at) VALUES (?, ?, NOW())";
                $stmt = $this->conn->prepare($member_query);
                $stmt->bind_param("ii", $league_id, $creator_user_id);
                $stmt->execute();

                $_SESSION['league_message'] = "Liga '{$league_name}' criada com sucesso!";
            } else {
                $_SESSION['league_error'] = 'Erro ao criar a liga. Tente novamente.';
            }

        } catch (Exception $e) {
            error_log("Error creating league: " . $e->getMessage());
            $_SESSION['league_error'] = 'Erro interno do servidor. Tente novamente mais tarde.';
        }

        $this->redirectToLeagues();
    }
    
    public function join() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToLeagues();
            return;
        }

        // Get form data
        $league_id = intval($_POST['league_id'] ?? 0);
        $keyword = trim($_POST['keyword'] ?? '');
        $user_id = $_SESSION['user_id'];

        // Validate input
        if ($league_id <= 0 || empty($keyword)) {
            $_SESSION['league_error'] = 'ID da liga e palavra-chave são obrigatórios.';
            $this->redirectToLeagues();
            return;
        }

        try {
            // Check if league exists and verify keyword
            $check_league_query = "SELECT id, name, keyword FROM leagues WHERE id = ?";
            $stmt = $this->conn->prepare($check_league_query);
            $stmt->bind_param("i", $league_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $_SESSION['league_error'] = 'Liga não encontrada.';
                $this->redirectToLeagues();
                return;
            }

            $league = $result->fetch_assoc();
            
            if ($league['keyword'] !== $keyword) {
                $_SESSION['league_error'] = 'Palavra-chave incorreta.';
                $this->redirectToLeagues();
                return;
            }

            // Check if user is already a member
            $member_check_query = "SELECT id FROM league_members WHERE league_id = ? AND user_id = ?";
            $stmt = $this->conn->prepare($member_check_query);
            $stmt->bind_param("ii", $league_id, $user_id);
            $stmt->execute();
            $member_result = $stmt->get_result();

            if ($member_result->num_rows > 0) {
                $_SESSION['league_error'] = 'Você já é membro desta liga.';
                $this->redirectToLeagues();
                return;
            }

            // Add user to league
            $join_query = "INSERT INTO league_members (league_id, user_id, joined_at) VALUES (?, ?, NOW())";
            $stmt = $this->conn->prepare($join_query);
            $stmt->bind_param("ii", $league_id, $user_id);

            if ($stmt->execute()) {
                $_SESSION['league_message'] = "Você entrou na liga '{$league['name']}' com sucesso!";
            } else {
                $_SESSION['league_error'] = 'Erro ao entrar na liga. Tente novamente.';
            }

        } catch (Exception $e) {
            error_log("Error joining league: " . $e->getMessage());
            $_SESSION['league_error'] = 'Erro interno do servidor. Tente novamente mais tarde.';
        }

        $this->redirectToLeagues();
    }
    
    public function leave() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToLeagues();
            return;
        }

        // Get form data
        $league_id = intval($_POST['league_id'] ?? 0);
        $user_id = $_SESSION['user_id'];

        // Validate input
        if ($league_id <= 0) {
            $_SESSION['league_error'] = 'ID da liga inválido.';
            $this->redirectToLeagues();
            return;
        }

        try {
            // Check if league exists and get league info
            $check_league_query = "SELECT l.id, l.name, l.creator_user_id FROM leagues l WHERE l.id = ?";
            $stmt = $this->conn->prepare($check_league_query);
            $stmt->bind_param("i", $league_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $_SESSION['league_error'] = 'Liga não encontrada.';
                $this->redirectToLeagues();
                return;
            }

            $league = $result->fetch_assoc();

            // Check if user is the creator
            if ($league['creator_user_id'] == $user_id) {
                $_SESSION['league_error'] = 'O criador da liga não pode sair da própria liga.';
                $this->redirectToLeagues();
                return;
            }

            // Check if user is a member
            $member_check_query = "SELECT id FROM league_members WHERE league_id = ? AND user_id = ?";
            $stmt = $this->conn->prepare($member_check_query);
            $stmt->bind_param("ii", $league_id, $user_id);
            $stmt->execute();
            $member_result = $stmt->get_result();

            if ($member_result->num_rows === 0) {
                $_SESSION['league_error'] = 'Você não é membro desta liga.';
                $this->redirectToLeagues();
                return;
            }

            // Remove user from league
            $leave_query = "DELETE FROM league_members WHERE league_id = ? AND user_id = ?";
            $stmt = $this->conn->prepare($leave_query);
            $stmt->bind_param("ii", $league_id, $user_id);

            if ($stmt->execute()) {
                $_SESSION['league_message'] = "Você saiu da liga '{$league['name']}' com sucesso!";
            } else {
                $_SESSION['league_error'] = 'Erro ao sair da liga. Tente novamente.';
            }

        } catch (Exception $e) {
            error_log("Error leaving league: " . $e->getMessage());
            $_SESSION['league_error'] = 'Erro interno do servidor. Tente novamente mais tarde.';
        }

        $this->redirectToLeagues();
    }
    
    private function redirectToLeagues() {
        header('Location: /leagues');
        exit;
    }
}
