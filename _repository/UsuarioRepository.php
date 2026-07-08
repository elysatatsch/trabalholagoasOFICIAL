<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../modelo/usuario.php';

class UsuarioRepository {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getConexao();
    }

     public function salvar(Usuario $usuario): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO usuario
            (
                nome,
                email,
                senha
            )
            VALUES
            (
                :nome,
                :email,
                :senha
            )
        ");

        $stmt->execute([
            ':nome' => $usuario->getNome(),
            ':email' => $usuario->getEmail(),
            ':senha' => $usuario->getSenha()
        ]);
    

      $usuario->registrarIdGerado(
            (int)$this->pdo->lastInsertId()
        );
    }



    public function buscarPorEmail(string $email): ?Usuario {
        $stmt = $this->pdo->prepare('SELECT * FROM usuario WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $dados = $stmt->fetch();

        if ($dados) {
            return new Usuario($dados);
        }
          return null;
    }

    public function inserir(string $nome, string $email, string $senha): void
{
    $usuario = Usuario::novo($nome, $email, $senha);
    $this->salvar($usuario);
}




      
}