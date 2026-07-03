<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../_repository/LivroRepository.php';
require_once __DIR__ . '/../_repository/AutorRepository.php';


$repoAutor = new AutorRepository();
$repo = new LivroRepository();


$id = 0;
if (isset($_GET['id_livro'])) {
    $id = (int) $_GET['id_livro'];
}

$livro = null;
if ($id > 0) {
    $livro = $repo->buscarPorId($id);
}

// Livro não encontrado ou não pertence ao usuário logado
if ($livro === null || $livro->getUsuarioId() !== $_SESSION['usuario_id']) {
    header('Location: index.php');
    exit;
}

// O bloco abaixo só deve processar se o formulário de confirmação for enviado (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Remove os vínculos do livro com os autores na tabela intermediária
    $repoAutor->removerAutoresLivro($livro->getId());

    // 2. Apaga o arquivo físico da capa na pasta uploads, caso ele exista
    if ($livro->getCapa()) {
        $caminhoCapa = __DIR__ . '/../uploads/' . $livro->getCapa();
        if (file_exists($caminhoCapa)) {
            unlink($caminhoCapa);
        }
    }

    // 3. Exclui o registro do livro no banco de dados
    $repo->excluir($livro->getId());
    
    header('Location: index.php?excluido=1');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <h2>Excluir Livro</h2>
  <a href="index.php" class="btn btn-ghost">← Voltar</a>
</div>

<div class="confirm-card">
  <h3>Você tem certeza?</h3>
  <p>
    Você está prestes a excluir o livro:
    <strong><?= htmlspecialchars($livro->getNome()) ?></strong>?
    <br>Esta ação não pode ser desfeita.
  </p>

  <form method="POST" action="livro_delete.php?id_livro=<?= $livro->getId() ?>">
    <div class="form-actions">
      <button type="submit" class="btn btn-excluir" style="background-color: red; color: white; padding: 10px; border: none; cursor: pointer;">Sim, excluir</button>
      <a href="index.php" class="btn btn-ghost">Cancelar</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>