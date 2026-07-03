<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modelo/livro.php';

class LivroRepository {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getConexao();
    }

    /** @return Livro[] */
    public function listarPorUsuario(int $usuarioId): array {
        // Ajustado para INNER JOIN explicito e inclusão da coluna capa
        $stmt = $this->pdo->prepare(
            'SELECT l.id_livro, l.nome_livro, l.genero, l.nota, l.usuario_id, l.capa
             FROM livro l
             INNER JOIN genero g ON l.genero = g.id_genero
             WHERE l.usuario_id = :uid_livro
             ORDER BY l.nome_livro;'
        );

        $stmt->execute([':uid_livro' => $usuarioId]);
        $lista = [];
        
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $dados) {
            $lista[] = new Livro($dados);
        }
        return $lista;
    }

    public function buscarPorId(int $id): ?Livro {
        $stmt = $this->pdo->prepare('SELECT * FROM livro WHERE id_livro = :id_livro LIMIT 1');
        $stmt->execute([':id_livro' => $id]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dados) {
            // Antes de retornar, precisamos buscar os IDs dos autores vinculados a este livro
            $stmtAutores = $this->pdo->prepare('SELECT autor_id FROM livro_autor WHERE livro_id = :id');
            $stmtAutores->execute([':id' => $id]);
            $dados['autores_ids'] = $stmtAutores->fetchAll(PDO::FETCH_COLUMN);

            return new Livro($dados);
        }

        return null;
    }

    public function salvar(Livro $livro): void {
        if ($livro->getId() > 0) {
            // OPERAÇÃO DE UPDATE
            $stmt = $this->pdo->prepare(
                'UPDATE livro SET nome_livro = :nome_livro, genero = :genero, nota = :nota, capa = :capa 
                 WHERE id_livro = :id_livro'
            );
            $stmt->execute([
                ':nome_livro' => $livro->getNome(),
                ':genero'     => $livro->getGenero(),
                ':nota'       => $livro->getNota(),
                ':capa'       => $livro->getCapa(),
                ':id_livro'   => $livro->getId(),
            ]);

            // Atualiza relacionamento N:N (Limpa os antigos e insere os atuais)
            $stmtDel = $this->pdo->prepare('DELETE FROM livro_autor WHERE livro_id = :id');
            $stmtDel->execute([':id' => $livro->getId()]);

            $this->salvarAutoresVinculados($livro->getId(), $livro->getAutoresIds());
            return;
        }

        // OPERAÇÃO DE INSERT
        if ($livro->getUsuarioId() <= 0) {
            throw new InvalidArgumentException('Usuário inválido.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO livro (nome_livro, genero, nota, usuario_id, capa) 
             VALUES (:nome, :genero, :nota, :uid_livro, :capa)'
        );
        $stmt->execute([
            ':nome'      => $livro->getNome(),
            ':genero'    => $livro->getGenero(),
            ':nota'      => $livro->getNota(),
            ':uid_livro' => $livro->getUsuarioId(),
            ':capa'      => $livro->getCapa()
        ]);

        $livroIdGerado = (int) $this->pdo->lastInsertId();
        $livro->registrarIdGerado($livroIdGerado);

        // Salva os autores na tabela intermediária
        $this->salvarAutoresVinculados($livroIdGerado, $livro->getAutoresIds());
    }

    // Método auxiliar privado para automatizar a gravação na tabela intermediária
    private function salvarAutoresVinculados(int $livroId, array $autoresIds): void {
        if (empty($autoresIds)) return;

        $stmt = $this->pdo->prepare(
            'INSERT INTO livro_autor (livro_id, autor_id) VALUES (:livro_id, :autor_id)'
        );

        foreach ($autoresIds as $autorId) {
            $stmt->execute([
                ':livro_id' => $livroId,
                ':autor_id' => (int)$autorId
            ]);
        }
    }

    // Métodos simplificados para a camada de visualização (Controller/Views)
    public function inserir(string $nome, int $genero, int $nota, int $usuarioId, ?string $capa = null, array $autoresIds = []): void {
        $livro = Livro::novo($nome, $genero, $nota, $usuarioId, $capa, $autoresIds);
        $this->salvar($livro);
    }

    public function atualizar(int $id, string $nome, int $genero, int $nota, ?string $capa = null, array $autoresIds = []): void {
        $livro = $this->buscarPorId($id);

        if ($livro === null) {
            throw new RuntimeException('Livro não encontrado.');
        }

        $livro->alterarDados($nome, $genero, $nota, $capa, $autoresIds);
        $this->salvar($livro);  
    }

    public function excluir(int $id): void {
        // Como no SQL usamos ON DELETE CASCADE na tabela livro_autor, 
        // deletar o livro removerá automaticamente os vínculos na tabela intermediária.
        $stmt = $this->pdo->prepare('DELETE FROM livro WHERE id_livro = :id_livro');
        $stmt->execute([':id_livro' => $id]);
    }
}