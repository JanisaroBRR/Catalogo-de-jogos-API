<?php

header('Access-Control-Allow-Origin: *'); // Permite que qualquer front-end (de qualquer origem/domínio) acesse nossa API. Para produção, isso seria restrito.
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Define quais métodos HTTP são permitidos.
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Define quais cabeçalhos o front-end pode enviar.
header('Content-Type: application/json'); // Informa a todos que a resposta desta API será sempre em formato JSON.

// Se a requisição for do tipo OPTIONS, é um "pre-flight check" do navegador.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}


// ROTEAMENTO (O Controlador de Tráfego Aéreo)

// 1. Importar o nosso controlador de jogos.
require_once __DIR__ . '/vendor/autoload.php';

// 2. Analisar a URL para descobrir o que o cliente quer.
// Graças ao .htaccess, a URL amigável (ex: /jogos/1) é transformada em um parâmetro GET.
$path = explode('/', $_GET['path'] ?? ''); // Pega a URL, remove a barra e cria um array. Ex: 'jogos/1' vira ['jogos', '1']

// O primeiro item do array é o nosso "recurso".
$recurso = $path[0] ?? null;

// O segundo item (se existir) é o ID.
$id = $path[1] ?? null;

// 3. Analisar o método da requisição (GET, POST, PUT, DELETE).
$metodo = $_SERVER['REQUEST_METHOD'];

// 4. Instanciar nosso controlador.
// A barra invertida no início diz ao PHP para procurar a partir do "namespace raiz".
$gameController = new \Controller\GameController(); 



// 5. Tomar a Decisão (Direcionar para o Portão Correto)
// Usamos um switch para direcionar a requisição para o método correto do controlador.

switch ($metodo) {
    case 'GET':
        // Se a requisição for GET, chamamos o método getGames().
        // (No futuro, poderíamos adicionar uma lógica para checar se um ID foi passado e chamar um getGameById($id)).
        $gameController->getGames();
        break;

    case 'POST':
        // Se for POST, chamamos o método para criar um novo jogo.
        $gameController->createGame();
        break;

    case 'PUT':
        // Se for PUT, chamamos o método para atualizar um jogo.
        // O ID e os dados estarão no corpo da requisição.
        $gameController->updateGame();
        break;

    case 'DELETE':
        // Se for DELETE, precisamos passar o ID para o método.
        // O ideal é que o ID venha na URL, mas vamos adaptar ao código da professora.
        // Vamos assumir que o ID é passado como um parâmetro na URL, ex: /jogos?id=1
        $_GET['id'] = $id; // Garantimos que o ID da URL amigável esteja disponível para o controlador.
        $gameController->deleteGame();
        break;

    default:
        // Se o método não for um dos esperados, retornamos um erro.
        header('HTTP/1.0 405 Method Not Allowed');
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
?>
