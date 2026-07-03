<?php

class Autor {

    private int $id;
    private string $nome;

    public function __construct(array $dados) {
        $this->id = (int)($dados['id_autor'] ?? 0);
        $this->nome = $dados['nome_autor'] ?? '';
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNomeAutor(): string {
        return $this->nome;
    }

    public static function novo(string $nome): Autor {
        if (trim($nome) === '') {
            throw new InvalidArgumentException('O nome do autor é obrigatório.');
        }

        return new Autor([
            'nome_autor' => trim($nome)
        ]);
    }

     public function registrarIdGerado(int $id): void {
        $this->id = $id;
    }

    }