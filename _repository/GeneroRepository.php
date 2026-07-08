<?php

require_once __DIR__ . '/../config/database.php';

class GeneroRepository {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getConexao();
    }

    public function listar(): array {

        $stmt = $this->pdo->query("
            SELECT id_genero, nome_genero
            FROM genero
            ORDER BY nome_genero
        ");
            return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array {
    $stmt = $this->pdo->prepare("
        SELECT *
        FROM genero
        WHERE id_genero = :id
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $id
    ]);

    $dados = $stmt->fetch();

    if (!$dados) {
        return null;
    }

    return $dados;
}     


}