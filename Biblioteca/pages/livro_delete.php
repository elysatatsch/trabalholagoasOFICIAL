<?php

require_once __DIR__ . '/../uploads/auth.php';
require_once __DIR__ . '/../repository/livroRepository.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repo->excluir($livro->getId());
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../uploads/header.php';
?>

<div class="page-header">
  <h2>Excluir Livro</h2>
  <a href="index.php" class="btn btn-ghost">← Voltar</a>
</div>

<div class="confirm-card">
  <h3>Você tem certeza?</h3>
  <p>
    Você está prestes a excluir esse livro?
    <strong><?= htmlspecialchars($livro->getNome()) ?></strong>
    (<?= htmlspecialchars($livro->getGenero()) ?>, Lv. <?= $livro->getNota() ?>).
    Esta ação não pode ser desfeita.
  </p>

  <form method="POST" action="livro_delete.php?id=<?= $livro->getId() ?>">
    <div class="form-actions">
      <button type="submit" class="btn btn-excluir">Sim, excluir</button>
      <a href="index.php" class="btn btn-ghost">Cancelar</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../uploads/footer.php'; ?>