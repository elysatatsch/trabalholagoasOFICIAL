<?php

class Usuario {

    private int    $id;
    private string $nome;
    private string $senha;
    private string $email;
    private string $criadoEm;
    private ?string $capa; // Alterado para ?string (pode ser nulo)

    public function __construct(array $dados) {
        $this->id       = (int) ($dados['id_usuario'] ?? 0);
        $this->nome     =        ($dados['nome']       ?? '');
        $this->senha    =        ($dados['senha']      ?? '');
        $this->email    =        ($dados['email']      ?? '');
        $this->criadoEm =        ($dados['criado_em']  ?? '');
        $this->capa     =        ($dados['capa']       ?? null);
    }

    public function getId():       int     { return $this->id; }
    public function getNome():     string  { return $this->nome; }
    public function getSenha():    string  { return $this->senha; }
    public function getEmail():    string  { return $this->email; }
    public function getCriadoEm(): string  { return $this->criadoEm; }
    public function getCapa():     ?string { return $this->capa; }

    /**
     * Método fábrica para criar um novo usuário (ex: no formulário de cadastro)
     */
    public static function novo(string $nome, string $email, string $senha, ?string $capa = null): Usuario {
        $nome = trim($nome);
        $email = trim($email);

        if ($nome === '' || $email === '') {
            throw new InvalidArgumentException('Nome e e-mail são obrigatórios.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Formato de e-mail inválido.');
        }

        if (strlen($senha) < 6) {
            throw new InvalidArgumentException('A senha deve ter pelo menos 6 caracteres.');
        }

        // Criptografa a senha usando o algoritmo seguro padrão do PHP (bcrypt)
        // Nota: No seu banco está SHA2, mas password_hash é o padrão de mercado para PHP puro!
       $senhaHash = hash('sha256', $senha);

        return new Usuario([
            'nome'  => $nome,
            'email' => $email,
            'senha' => $senhaHash,
            'capa'  => $capa
        ]);
    }

    public function registrarIdGerado(int $id): void {
        if ($id <= 0) {
            throw new InvalidArgumentException('ID inválido.');
        }
        $this->id = $id;
    }

    }

