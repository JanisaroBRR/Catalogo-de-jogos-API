<?php
// CABEÇALHOS E AUTOLOADER (Mantenha como estão)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/vendor/autoload.php';

// ROTEAMENTO SIMPLES E EFICAZ (Sua Lógica Original)
$metodo = $_SERVER['REQUEST_METHOD'];
$path = explode('/', $_GET['path'] ?? '');
$id = $path[0] ?? null; // O ID é a primeira e única parte da URL (ex: /1)

$gameController = new \Controller\GameController();

switch ($metodo) {

    case 'GET':
        // AQUI ESTÁ A MUDANÇA
        if ($id && is_numeric($id)) {
            // Se um ID numérico foi passado na URL, chama a função de busca por ID.
            $gameController->getGameById($id);
        } else {
            // Se não houver ID, continua listando todos os jogos.
            $gameController->getGames();
        }
        break;



    case 'POST':
        $gameController->createGame();
        break;

    case 'PUT':
        $gameController->updateGame();
        break;

    case 'DELETE':
        if ($id && is_numeric($id)) {
            $gameController->deleteGame($id);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "ID do jogo inválido ou não fornecido na URL. Ex: /1"]);
        }
        break;

    default:
        header('HTTP/1.0 405 Method Not Allowed');
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
?>
