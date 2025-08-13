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

    // Método para editar um JOGO (Renomeado de updateUser para updateGame)
    public function updateGame() // CORREÇÃO 2: O nome do método foi atualizado para refletir a entidade "Game".
    {
        $sql = "UPDATE jogos SET title = :title, genero = :genero, plataforma = :plataforma, ano_lancamento = :ano_lancamento WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        // CORREÇÃO 3: Faltavam os bindParam para plataforma e ano_lancamento na atualização.
        // A lógica é a mesma da criação: todos os placeholders (os :campos) na query SQL precisam de um valor correspondente.
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":title", $this->title, PDO::PARAM_STR);
        $stmt->bindParam(":genero", $this->genero, PDO::PARAM_STR);
        $stmt->bindParam(":plataforma", $this->plataforma, PDO::PARAM_STR);
        $stmt->bindParam(":ano_lancamento", $this->ano_lancamento, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Método para excluir um JOGO (Renomeado de deleteUser para deleteGame)
    public function deleteGame() // CORREÇÃO 4: Nome do método atualizado para consistência.
    {
        $sql = "DELETE FROM jogos WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}

?>
