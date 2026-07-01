class Livro {

    private int    $id;
    private string $nome;
    private string $genero;
    private int    $usuarioId;
    private iny    $nota;

    public function __construct(array $dados) {
        $this->id        = (int) ($dados['id']        ?? 0);
        $this->nome      =        $dados['nome']       ?? '';
        $this->genero    =        $dados['genero']       ?? '';
        $this->usuarioId = (int) ($dados['usuario_id'] ?? 0);
        $this->nota      = int ($dados['nota']         ?? 0);
    }

    public function getId():        int    { return $this->id; }
    public function getNome():      string { return $this->nome; }
    public function getgenero():      string { return $this->genero; }
    public function getUsuarioId(): int    { return $this->usuarioId; }
    public function getNota(): int    { return $this->nota; }

    public static function novo(string $nome, string $genero, int $usuarioId, int nota): Livro{
        if ($usuarioId <= 0) {
            throw new InvalidArgumentException('Usuário inválido.');
        }

        $Livro = new Livro(['usuario_id' => $usuarioId]);
        $Livro->alterarDados($nome, $genero);

        return $Livro;
    }

    public function alterarDados(string $nome, string $genero): void {
        $nome = trim($nome);
        $genero = trim($genero);

        if ($nome === '' || $genero === '') {
            throw new InvalidArgumentException('Nome e genero são obrigatórios.');
        }

        if ($nota < 1 || $nota > 5) {
            throw new InvalidArgumentException('a nota deve ser entre 1 e 5.');
        }

        $this->nome  = $nome;
        $this->genero  = $genero;
        $this->nota = $nota;
    }

    public function registrarIdGerado(int $id): void {
        if ($id <= 0) {
            throw new InvalidArgumentException('ID inválido.');
        }

        $this->id = $id;
    }
}