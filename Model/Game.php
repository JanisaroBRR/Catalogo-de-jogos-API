<?php
namespace Model;

use PDO;
use Model\Connection;

class Game
{
    private $conn;

    public $id;
    public $title;
    public $genero;
    public $plataforma;
    public $ano_lancamento;

    public function __construct()
    {
        $this->conn = Connection::getConnection();
    }

    // Método para obter todos os JOGOS (Não mais usuários)
    public function getGames()
    {
        $sql = "SELECT * FROM jogos";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para criar um novo JOGO
    public function createGame()
    {
        $sql = "INSERT INTO jogos (title, genero, plataforma, ano_lancamento) VALUES (:title, :genero, :plataforma, :ano_lancamento)";
        $stmt = $this->conn->prepare($sql);

        // CORREÇÃO 1: Faltavam os bindParam para plataforma e ano_lancamento.
        // Se não fizermos o "bind", o banco de dados não sabe que valor colocar nesses campos.
        $stmt->bindParam(":title", $this->title, PDO::PARAM_STR);
        $stmt->bindParam(":genero", $this->genero, PDO::PARAM_STR);
        $stmt->bindParam(":plataforma", $this->plataforma, PDO::PARAM_STR);
        $stmt->bindParam(":ano_lancamento", $this->ano_lancamento, PDO::PARAM_INT); // Usamos PARAM_INT para números inteiros

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

public function updateGame()
{
    $sql = "UPDATE jogos SET title = :title, genero = :genero, plataforma = :plataforma, ano_lancamento = :ano_lancamento WHERE id = :id";
    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
    $stmt->bindParam(":title", $this->title, PDO::PARAM_STR);
    $stmt->bindParam(":genero", $this->genero, PDO::PARAM_STR);
    $stmt->bindParam(":plataforma", $this->plataforma, PDO::PARAM_STR);
    $stmt->bindParam(":ano_lancamento", $this->ano_lancamento, PDO::PARAM_INT);

    $stmt->execute(); // Executa a query


    if ($stmt->rowCount() > 0) {
        return true; // Sucesso, uma ou mais linhas foram atualizadas.
    }

    return false; // Falha, nenhuma linha com esse ID foi encontrada ou os dados eram os mesmos.
}



public function deleteGame()
{
    $sql = "DELETE FROM jogos WHERE id = :id";
    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

    $stmt->execute(); // Executa a query

    // AQUI ESTÁ A MÁGICA: Verificamos se alguma linha foi realmente deletada.
    if ($stmt->rowCount() > 0) {
        return true; // Sucesso, a linha foi encontrada e deletada.
    }

    return false; // Falha, nenhuma linha com esse ID foi encontrada.
}
// No arquivo Model/Game.php, dentro da classe Game

// Método para obter um único jogo pelo seu ID
public function getGameById($id)
{
    // A query SQL agora tem uma cláusula WHERE para filtrar pelo ID
    $sql = "SELECT * FROM jogos WHERE id = :id";
    $stmt = $this->conn->prepare($sql);

    // Vinculamos o parâmetro :id ao valor do $id que recebemos
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    $stmt->execute();

    // Usamos fetch() em vez de fetchAll(), pois esperamos apenas um resultado
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


}

?>
