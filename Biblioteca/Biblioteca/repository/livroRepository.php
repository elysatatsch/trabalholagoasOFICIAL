<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modelo/livro.php';

class livroRepository {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getConexao();
    }

    /** @return Livro[] */
    public function listarPorUsuario(int $usuarioId): array {
        $stmt = $this->pdo->prepare(
     'SELECT
    l.id_livro,
    l.nome_livro,l.genero,
    g.nome_genero AS genero,
    l.nota,
    l.usuario_id
FROM livro l, genero g
WHERE l.genero = g.id_genero and l.usuario_id = :uid_livro
ORDER BY l.nome_livro;'
        );
        $stmt->execute([':uid_livro' => $usuarioId]);
        $lista = [];
        foreach ($stmt->fetchAll() as $dados) {
            $lista[] = new Livro($dados);
        }
        return $lista;
    }

    public function buscarPorId(int $id): ?Livro {
        $stmt = $this->pdo->prepare('SELECT * FROM Livro WHERE id_livro = :id_livro LIMIT 1');
        $stmt->execute([':id_livro' => $id]);
        $dados = $stmt->fetch();

        if ($dados) {
            return new Livro($dados);
        }

        return null;
    }

    public function salvar(Livro $livro): void {
        if ($livro->getId() > 0) {
            $stmt = $this->pdo->prepare(
                'UPDATE livro SET nome_livro = :nome_livro, genero = :genero, nota = :nota, autor = autor WHERE id_livro = :id_livro'
            );
            $stmt->execute([
                ':nome_livro'  => $livro->getNome(),
                ':genero'  => $livro->getGenero(),
                ':nota' => $livro->getNota(),
                ':id_livro'    => $livro->getId(),
                ':autor'    => $livro->getAutor(),
            ]);
            return;
        }

        if ($livro->getUsuarioId() <= 0) {
            throw new InvalidArgumentException('Usuário inválido.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO livro (nome_livro, genero, nota, usuario_id, autor) VALUES (:nome, :genero, :nota, :uid, :autor)'
        );
        $stmt->execute([
            ':nome'  => $livro->getNome(),
            ':genero'  => $livro->getGenero(),
            ':nota' => $livro->getNota(),
            ':uid_livro'   => $livro->getUsuarioId(),
            ':autor'   => $livro->getAutor(),
        ]);

        $livro->registrarIdGerado((int) $this->pdo->lastInsertId());
    }

    public function inserir(string $nome, string $genero, int $nota, int $usuarioId): void {
        $livro = Livro::novo($nome, $genero, $nota, $usuarioId, $autor);
        $this->salvar($livro);
    }

    public function atualizar(int $id, string $nome, string $genero, int $nota, int $autor): void {
        $livro = $this->buscarPorId($id);

        if ($livro === null) {
            throw new RuntimeException('Livro não encontrado.');
        }

        $livro->alterarDados($nome, $genero, $nota, $autor);
        $this->salvar($livro);
    }

    public function excluir(int $id): void {
        $stmt = $this->pdo->prepare('DELETE FROM livro WHERE id_livro = :id_livro');
        $stmt->execute([':id_livro' => $id]);
    }
}