<?php

class Livro {

    private int    $id;
    private string $nome;
    private int    $genero;
    private int    $nota;
    private int    $usuarioId;
    private ?string $capa; 
    private array  $autoresIds = []; 
    private array $tropeIds=[];


    public function __construct(array $dados) {
        $this->id        = (int) ($dados['id_livro']   ?? 0);
        $this->nome      =        ($dados['nome_livro'] ?? '');
        $this->genero    = (int)  ($dados['genero']     ?? 0);
        $this->nota      = (int)  ($dados['nota']       ?? 1);
        $this->usuarioId = (int)  ($dados['usuario_id'] ?? 0);
        $this->capa      =        ($dados['capa']       ?? null); 
        $this->autoresIds =       ($dados['autores_ids'] ?? []); 
        $this->tropeIds=           $dados['trope_ids'] ?? [];
       
    }

    public function getId():        int    { return $this->id; }
    public function getNome():      string { return $this->nome; }
    public function getGenero():    int    { return $this->genero; }
    public function getNota():     int    { return $this->nota; }
    public function getUsuarioId(): int    { return $this->usuarioId; }
    public function getCapa():      ?string { return $this->capa; } 
    public function getAutoresIds(): array  { return $this->autoresIds; } 
    public function getTropeIds(): array {return $this->tropeIds;}
 

    public static function novo(string $nome, int $genero, int $nota, int $usuarioId, ?string $capa = null, array $autoresIds = [], array $tropeIds=[]): Livro {
        if ($usuarioId <= 0) {
            throw new InvalidArgumentException('Usuário inválido.');
        }

        $livro = new Livro(['usuario_id' => $usuarioId]);
        $livro->alterarDados($nome, $genero, $nota, $capa, $autoresIds, $tropeIds);
        return $livro;
        
    }

    public function alterarDados(string $nome, int $genero, int $nota, ?string $capa = null, array $autoresIds = [], array $tropeIds=[]): void {
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
        $this->capa       = $capa; 
        $this->autoresIds = $autoresIds; 
        $this->tropeIds = $tropeIds; 

    }

    public function registrarIdGerado(int $id): void {
        if ($id <= 0) {
            throw new InvalidArgumentException('ID inválido.');
        }

        $this->id = $id;
    }

}