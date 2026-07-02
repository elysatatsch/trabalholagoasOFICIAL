<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modelo/autor.php';

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



    public function salvar(Autor $autor): void {
    $stmt = $this->pdo->prepare(
        "INSERT INTO autor (nome_autor)
         VALUES (:nome)"
    );

    $stmt->execute([
        ':nome' => $autor->getNome()
    ]);

    $autor->registrarIdGerado(
    (int)$this->pdo->lastInsertId()
);
}

public function salvarLivroAutor(int $livroId, int $autorId): void {
   
    $stmt = $this->pdo->prepare(
        "INSERT INTO livro_autor (livro_id, autor_id)
         VALUES (:livro, :autor)"
    );

    $stmt->execute([
        ':livro' => $livroId,
        ':autor' => $autorId
    ]);
   
         }
 public function buscarPorId(int $id): ?array {
    $stmt = $this->pdo->prepare("
        SELECT *
        FROM autor
        WHERE id_autor = :id
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

public function buscarAutoresLivro(int $livroId): array
{
    $stmt = $this->pdo->prepare("
        SELECT
            a.id_autor,
            a.nome_autor
        FROM autor a,  livro_autor la
        WHERE la.livro_id = :livro and a.id_autor = la.autor_id
        ORDER BY a.nome_autor
    ");

    $stmt->execute([
        ':livro' => $livroId
    ]);

    return $stmt->fetchAll();
}

public function removerAutoresLivro(int $livroId): void
{
    $stmt = $this->pdo->prepare("
        DELETE FROM livro_autor
        WHERE livro_id = :livro
    ");

    $stmt->execute([
        ':livro' => $livroId
    ]);
}

}