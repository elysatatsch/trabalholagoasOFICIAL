<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modelo/autor.php';

class AutorRepository {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getConexao();
    }

    /**
     * Retorna uma lista de Objetos do tipo Autor
     */
    public function listar(): array {
        $stmt = $this->pdo->query("
            SELECT id_autor, nome_autor
            FROM autor
            ORDER BY nome_autor
        ");

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $autores = [];

        // Transforma cada linha do banco em um Objeto Autor
        foreach ($resultados as $dados) {
            $autores[] = new Autor($dados);
        }

        return $autores;
    }

    public function salvar(Autor $autor): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO autor (nome_autor)
             VALUES (:nome)"
        );

        $stmt->execute([
            ':nome' => $autor->getNomeAutor()
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

    /**
     * Retorna um objeto Autor ou null se não encontrar
     */
    public function buscarPorId(int $id): ?Autor {
        $stmt = $this->pdo->prepare("
            SELECT id_autor, nome_autor
            FROM autor
            WHERE id_autor = :id
            LIMIT 1
        ");

        $stmt->execute([':id' => $id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        return new Autor($dados);
    }           

    public function buscarAutoresLivro(int $livroId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                a.id_autor,
                a.nome_autor
            FROM autor a, livro_autor la
            WHERE a.id_autor = la.autor_id and la.livro_id = :livro
            ORDER BY a.nome_autor
        ");

        $stmt->execute([':livro' => $livroId]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $autores = [];
        foreach ($resultados as $dados) {
            $autores[] = new Autor($dados);
        }

        return $autores;
    }

    public function removerAutoresLivro(int $livroId): void
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM livro_autor
            WHERE livro_id = :livro
        ");

        $stmt->execute([':livro' => $livroId]);
    }
}