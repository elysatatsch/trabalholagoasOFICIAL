<?php

require_once __DIR__ . '/../config/database.php';

class AutorRepository {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getConexao();
    }

    public function listar(): array {

        $stmt = $this->pdo->query("
            SELECT id_autor, nome_autor
            FROM autor
            ORDER BY nome_autor
        ");

        return $stmt->fetchAll();
    }
}