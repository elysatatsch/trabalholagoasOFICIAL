<?php

session_start();

require_once __DIR__ . '/../_repository/UsuarioRepository.php';
require_once __DIR__ . '/../modelo/Usuario.php';

$erro = '';
$nome = '';
$email = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    try{

        $repo = new UsuarioRepository();

        if($repo->buscarPorEmail($email)){
            throw new InvalidArgumentException("Este e-mail já está cadastrado.");
        }

        $repo->Inserir($nome,$email,$senha);

        header("Location: index.php");
        exit;

    }
    catch(InvalidArgumentException $e){

        $erro = $e->getMessage();

    }

}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<title>Cadastro</title>

<link rel="stylesheet" href="../assets/style.css">

</head>

<body class="login-body">

<div class="login-card">

<h1>Cadastrar</h1>

<?php if($erro != ''): ?>

<div class="alert alert-erro">

<?= htmlspecialchars($erro) ?>

</div>

<?php endif; ?>

<form method="POST">

<div class="form-group">

<label>Nome</label>

<input
type="text"
name="nome"
value="<?= htmlspecialchars($nome) ?>"
required>

</div>

<div class="form-group">

<label>Email</label>

<input
type="email"
name="email"
value="<?= htmlspecialchars($email) ?>"
required>

</div>

<div class="form-group">

<label>Senha</label>

<input
type="password"
name="senha"
required>

</div>

<button class="btn btn-primary btn-full">

Cadastrar

<a href="index.php"></a>

</button>

</form>

<p style="margin-top:20px;text-align:center;">

Já possui conta?

<a href="login.php">

Entrar

</a>

</p>

</div>

</body>

</html>




