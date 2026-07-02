<?php

class Usuario {

    private int    $id;
    private string $nome;
    private string $senha;
    private string $criadoEm;
    private string $email;
    private string $capa;
    

    public function __construct(array $dados) {
        $this->id       = (int) ($dados['id_usuario']       ?? 0);
        $this->nome     =        $dados['nome']      ?? '';
        $this->senha    =        $dados['senha']     ?? '';
        $this->criadoEm =        $dados['criado_em'] ?? '';
        $this->email =        $dados['email'] ?? '';
        $this->capa =        $dados['capa'] ?? '';
    }

    public function getId():       int    { return $this->id; }
    public function getNome():     string { return $this->nome; }
    public function getSenha():    string { return $this->senha; }
    public function getCriadoEm(): string { return $this->criadoEm; }
    public function getEmail(): string { return $this->email; }
    public function getCapa(): string { return $this->capa; }
}