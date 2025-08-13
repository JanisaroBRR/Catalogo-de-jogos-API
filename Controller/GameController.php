<?php
namespace Controller;

// CORREÇÃO 1: Precisamos importar a classe 'Game', não 'User'.
use Model\Game;

// O configuration.php não é estritamente necessário aqui, mas podemos manter por padrão.
require_once __DIR__ . '/../Config/configuration.php';

class GameController
{
    // Função para pegar todos os JOGOS
    // No seu GameController.php
    public function getGames()
    {
        // 1. Crie uma instância do MODELO (o especialista em dados)
        //    Note o '\Model\Game' - isso garante que estamos pegando a classe do namespace correto.
        $gameModel = new Game();

        // 2. Peça ao MODELO para buscar os dados do banco.
        $games = $gameModel->getGames();

        // 3. O resto do código (o if/else) continua o mesmo.
        if ($games) {
            header('Content-Type: application/json', true, 200);
            echo json_encode($games);
        } else {
            header('Content-Type: application/json', true, 404);
            echo json_encode(["message" => "Nenhum jogo encontrado"]);
        }
    }


    // Função para criar um JOGO
    public function createGame()
    {
        $data = json_decode(file_get_contents("php://input"));

        // CORREÇÃO 5: Validar os campos do nosso jogo (title, genero, etc.)
        if (isset($data->title) && isset($data->genero) && isset($data->plataforma) && isset($data->ano_lancamento)) {
            $game = new Game();
            // CORREÇÃO 6: Atribuir os dados do JSON para as propriedades do objeto Game.
            $game->title = $data->title;
            $game->genero = $data->genero;
            $game->plataforma = $data->plataforma;
            $game->ano_lancamento = $data->ano_lancamento;

            if ($game->createGame()) { // Chamar o método createGame()
                header('Content-Type: application/json', true, 201);
                echo json_encode(["message" => "Jogo criado com sucesso"]);
            } else {
                header('Content-Type: application/json', true, 500);
                echo json_encode(["message" => "Falha ao criar jogo"]);
            }
        } else {
            header('Content-Type: application/json', true, 400);
            echo json_encode(["message" => "Dados incompletos. É necessário enviar title, genero, plataforma e ano_lancamento."]);
        }
    }

    // Função para editar um JOGO
    public function updateGame()
    {
        $data = json_decode(file_get_contents("php://input"));

        // A validação para o update precisa também do ID.
        if (isset($data->id) && isset($data->title) && isset($data->genero) && isset($data->plataforma) && isset($data->ano_lancamento)) {
            $game = new Game();
            $game->id = $data->id;
            $game->title = $data->title;
            $game->genero = $data->genero;
            $game->plataforma = $data->plataforma;
            $game->ano_lancamento = $data->ano_lancamento;

            if ($game->updateGame()) { // Chamar o método updateGame()
                header('Content-Type: application/json', true, 200);
                echo json_encode(["message" => "Jogo atualizado com sucesso"]);
            } else {
                header('Content-Type: application/json', true, 500);
                echo json_encode(["message" => "Falha ao atualizar jogo"]);
            }
        } else {
            header('Content-Type: application/json', true, 400);
            echo json_encode(["message" => "Dados incompletos para atualização."]);
        }
    }

    // Função para excluir um JOGO
    public function deleteGame()
    {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $game = new Game();
            $game->id = $id;

            if ($game->deleteGame()) { // Chamar o método deleteGame()
                header('Content-Type: application/json', true, 200);
                echo json_encode(["message" => "Jogo excluído com sucesso"]);
            } else {
                header('Content-Type: application/json', true, 500);
                echo json_encode(["message" => "Falha ao excluir jogo"]);
            }
        } else {
            header('Content-Type: application/json', true, 400);
            echo json_encode(["message" => "ID do jogo não fornecido na URL."]);
        }
    }
}
?>