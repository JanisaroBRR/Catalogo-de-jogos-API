<?php
namespace Controller;

use Model\Game;

class GameController
{
    /**
     * Lista todos os jogos.
     */
    public function getGames()
    {
        $gameModel = new Game();
        $games = $gameModel->getGames();

        if ($games) {
            header('Content-Type: application/json', true, 200);
            echo json_encode($games);
        } else {
            // Se não houver jogos, retorna um array vazio, o que é um sucesso (200 OK).
            header('Content-Type: application/json', true, 200);
            echo json_encode([]);
        }
    }

    /**
     * Busca um único jogo pelo seu ID.
     */
    public function getGameById($id)
    {
        $gameModel = new Game();
        $game = $gameModel->getGameById($id);

        if ($game) {
            // Sucesso: Jogo encontrado.
            header('Content-Type: application/json', true, 200);
            echo json_encode($game);
        } else {
            // Falha: Nenhum jogo com esse ID foi encontrado.
            header('Content-Type: application/json', true, 404);
            echo json_encode(["message" => "Jogo não encontrado"]);
        }
    }

    /**
     * Cria um novo jogo com validação e sanitização.
     */
    public function createGame()
    {
        $data = json_decode(file_get_contents("php://input"));

        // --- INÍCIO DA BLINDAGEM DE SEGURANÇA ---

        // 1. VALIDAÇÃO: Verificar se os campos essenciais existem e não estão vazios.
        if (
            !isset($data->title) || empty(trim($data->title)) ||
            !isset($data->genero) || empty(trim($data->genero)) ||
            !isset($data->plataforma) || empty(trim($data->plataforma)) ||
            !isset($data->ano_lancamento) || !is_numeric($data->ano_lancamento)
        ) {
            header('Content-Type: application/json', true, 400); // 400 Bad Request
            echo json_encode(["message" => "Dados inválidos. Todos os campos são obrigatórios e o ano deve ser um número."]);
            return; // Encerra a execução
        }

        // 2. SANITIZAÇÃO: Limpar os dados antes de usá-los.
        $gameModel = new Game();
        // htmlspecialchars() previne ataques XSS.
        $gameModel->title = htmlspecialchars(strip_tags(trim($data->title)));
        $gameModel->genero = htmlspecialchars(strip_tags(trim($data->genero)));
        $gameModel->plataforma = htmlspecialchars(strip_tags(trim($data->plataforma)));
        $gameModel->ano_lancamento = (int)$data->ano_lancamento; // Garante que seja um inteiro.

        // --- FIM DA BLINDAGEM DE SEGURANÇA ---

        if ($gameModel->createGame()) {
            header('Content-Type: application/json', true, 201); // 201 Created
            echo json_encode(["message" => "Jogo criado com sucesso"]);
        } else {
            header('Content-Type: application/json', true, 500); // 500 Internal Server Error
            echo json_encode(["message" => "Falha ao criar jogo"]);
        }
    }

    /**
     * Atualiza um jogo existente com validação e sanitização.
     */
    public function updateGame()
    {
        $data = json_decode(file_get_contents("php://input"));

        // --- INÍCIO DA BLINDAGEM DE SEGURANÇA ---

        // 1. VALIDAÇÃO: Para atualizar, o ID é crucial, assim como o título.
        if (!isset($data->id) || !is_numeric($data->id) || !isset($data->title) || empty(trim($data->title))) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(["message" => "Dados inválidos. 'id' (numérico) e 'title' são obrigatórios."]);
            return;
        }

        // 2. SANITIZAÇÃO: Limpar todos os dados recebidos.
        $gameModel = new Game();
        $gameModel->id = (int)$data->id;
        $gameModel->title = htmlspecialchars(strip_tags(trim($data->title)));
        $gameModel->genero = htmlspecialchars(strip_tags(trim($data->genero ?? '')));
        $gameModel->plataforma = htmlspecialchars(strip_tags(trim($data->plataforma ?? '')));
        $gameModel->ano_lancamento = isset($data->ano_lancamento) ? (int)$data->ano_lancamento : null;

        // --- FIM DA BLINDAGEM DE SEGURANÇA ---

        if ($gameModel->updateGame()) {
            header('Content-Type: application/json', true, 200);
            echo json_encode(["message" => "Jogo atualizado com sucesso"]);
        } else {
            // Se updateGame() retorna false, é porque o rowCount() foi 0, ou seja, o ID não foi encontrado.
            header('Content-Type: application/json', true, 404);
            echo json_encode(["message" => "Falha ao atualizar: Jogo não encontrado com o ID fornecido."]);
        }
    }

    /**
     * Exclui um jogo pelo seu ID.
     */
    public function deleteGame($id)
    {
        // A validação do ID já é feita no index.php, mas podemos manter uma aqui por segurança.
        if ($id && is_numeric($id)) {
            $gameModel = new Game();
            $gameModel->id = $id;

            if ($gameModel->deleteGame()) {
                header('Content-Type: application/json', true, 200);
                echo json_encode(["message" => "Jogo excluído com sucesso"]);
            } else {
                // Se deleteGame() retorna false, é porque o rowCount() foi 0, ou seja, o ID não foi encontrado.
                header('Content-Type: application/json', true, 404);
                echo json_encode(["message" => "Falha ao excluir: Jogo não encontrado com o ID fornecido."]);
            }
        }
    }
}
