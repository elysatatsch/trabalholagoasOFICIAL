<?php

require_once __DIR__ . '/../config/database.php';

class generoRepository {

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
}