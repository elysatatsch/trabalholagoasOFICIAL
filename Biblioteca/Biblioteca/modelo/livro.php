<?php

class Livro {

    private int    $id;
    private string $nome;
    private int $genero;
    private int    $nota;
    private int    $usuarioId;
    private int    $autor;

    public function __construct(array $dados) {
        $this->id        = (int) ($dados['id_livro']        ?? 0);
        $this->nome      =       ( $dados['nome_livro']       ?? '');
        $this->genero      =   (int)   (  $dados['genero']       ?? '');
        $this->nota        = (int) ($dados['nota']      ?? 1);
        $this->usuarioId = (int) ($dados['usuario_id'] ?? 0);
        $this->autor = (int) ($dados['autor_id'] ?? 0);
    }

    public function getId():        int    { return $this->id; }
    public function getNome():      string { return $this->nome; }
    public function getGenero():      int { return $this->genero; }
    public function getNota():     int    { return $this->nota; }
    public function getUsuarioId(): int    { return $this->usuarioId; }
     public function getAutor(): int    { return $this->autor; }

    public static function novo(string $nome, int $genero, int $nota, int $usuarioId, int $autor): Livro {
        if ($usuarioId <= 0) {
            throw new InvalidArgumentException('Usuário inválido.');
        }

        $livro = new Livro(['usuario_id' => $usuarioId]);
        $livro ->alterarDados($nome, $genero, $nota, $autor);

        return $livro;
    }

    public function alterarDados(string $nome, int $genero, int $nota, int $autor): void {
        $nome = trim($nome);
        $genero = (int)($genero);
        $nota = (int) $nota;
        $autor = (int) $autor;

        if ($nome === '' || $genero <= 0 || $autor <= 0) {
            throw new InvalidArgumentException('Nome do livro, genero e autor são obrigatórios.');
        }

        if ($nota < 1 || $nota > 5) {
            throw new InvalidArgumentException('O nota deve ser entre 1 e 5.');
        }

        $this->nome  = $nome;
        $this->genero  = $genero;
        $this->nota = $nota;
        $this->autor = $autor;

    }

    public function registrarIdGerado(int $id): void {
        if ($id <= 0) {
            throw new InvalidArgumentException('ID inválido.');
        }

        $this->id = $id;
    }
}