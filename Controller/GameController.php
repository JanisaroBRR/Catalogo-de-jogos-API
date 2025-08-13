<?php
namespace Controller;

use Model\Game;

require_once __DIR__ . '/../Config/configuration.php';

class GameController
{
    
    // Função para pegar todos os JOGOS
    public function getGames()
    {
        $gameModel = new Game();

        $games = $gameModel->getGames();


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

        // CORREÇÃO 5: Validar os campos do jogo (title, genero, etc.)
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
    public function updateGame() // O ID vem do corpo do JSON, então não precisa de parâmetro aqui
    {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->id) && isset($data->title)) { // Validação mínima
            $gameModel = new Game();
            $gameModel->id = $data->id;
            $gameModel->title = $data->title;
            $gameModel->genero = $data->genero;
            $gameModel->plataforma = $data->plataforma;
            $gameModel->ano_lancamento = $data->ano_lancamento;

            if ($gameModel->updateGame()) {
                header('Content-Type: application/json', true, 200);
                echo json_encode(["message" => "Jogo atualizado com sucesso"]);
            } else {
                header('Content-Type: application/json', true, 500);
                echo json_encode(["message" => "Falha ao atualizar jogo"]);
            }
        } else {
            header('Content-Type: application/json', true, 400);
            echo json_encode(["message" => "Dados inválidos para atualização. 'id' e 'title' são obrigatórios."]);
        }
    }

    // Função para excluir um JOGO
    public function deleteGame($id) 
    {
        if ($id) {
            $gameModel = new Game();
            $gameModel->id = $id;

            if ($gameModel->deleteGame()) {
                header('Content-Type: application/json', true, 200);
                echo json_encode(["message" => "Jogo excluído com sucesso"]);
            } else {
                header('Content-Type: application/json', true, 500);
                echo json_encode(["message" => "Falha ao excluir jogo"]);
            }
        }
        
    }

// Função para pegar um jogo específico pelo ID
public function getGameById($id)
{
    $gameModel = new \Model\Game();
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

    
}


?>