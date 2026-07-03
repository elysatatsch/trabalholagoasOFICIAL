<?php

class Livro {

    private int    $id;
    private string $nome;
    private int    $genero;
    private int    $nota;
    private int    $usuarioId;
    private ?string $capa; // Alteração: Adicionado campo para a imagem (pode ser nulo no início)
    private array  $autoresIds = []; // Alteração: Array para guardar os IDs dos autores (N:N)

    public function __construct(array $dados) {
        $this->id        = (int) ($dados['id_livro']   ?? 0);
        $this->nome      =        ($dados['nome_livro'] ?? '');
        $this->genero    = (int)  ($dados['genero']     ?? 0);
        $this->nota      = (int)  ($dados['nota']       ?? 1);
        $this->usuarioId = (int)  ($dados['usuario_id'] ?? 0);
        $this->capa      =        ($dados['capa']       ?? null); // Inicializa a capa
        $this->autoresIds =       ($dados['autores_ids'] ?? []); // Inicializa os autores se vierem do banco
    }

    public function getId():        int    { return $this->id; }
    public function getNome():      string { return $this->nome; }
    public function getGenero():    int    { return $this->genero; }
    public function getNota():     int    { return $this->nota; }
    public function getUsuarioId(): int    { return $this->usuarioId; }
    public function getCapa():      ?string { return $this->capa; } // Getter da capa
    public function getAutoresIds(): array  { return $this->autoresIds; } // Getter dos autores

    // Método fábrica atualizado para receber capa e autores
    public static function novo(string $nome, int $genero, int $nota, int $usuarioId, ?string $capa = null, array $autoresIds = []): Livro {
        if ($usuarioId <= 0) {
            throw new InvalidArgumentException('Usuário inválido.');
        }

        $livro = new Livro(['usuario_id' => $usuarioId]);
        $livro->alterarDados($nome, $genero, $nota, $capa, $autoresIds);

        return $livro;
    }

    // Método de alteração atualizado para incluir capa e autores
    public function alterarDados(string $nome, int $genero, int $nota, ?string $capa = null, array $autoresIds = []): void {
        $nome = trim($nome);
        $genero = (int)$genero;
        $nota = (int)$nota;

        if ($nome === '' || $genero <= 0) {
            throw new InvalidArgumentException('Nome do livro e gênero são obrigatórios.');
        }

        if ($nota < 1 || $nota > 5) {
            throw new InvalidArgumentException('A nota deve ser entre 1 e 5.');
        }

        $this->nome       = $nome;
        $this->genero     = $genero;
        $this->nota       = $nota;
        $this->capa       = $capa; // Define o nome do arquivo de imagem
        $this->autoresIds = $autoresIds; // Define a lista de IDs de autores vinculados
    }

    public function registrarIdGerado(int $id): void {
        if ($id <= 0) {
            throw new InvalidArgumentException('ID inválido.');
        }

        $this->id = $id;
    }
}