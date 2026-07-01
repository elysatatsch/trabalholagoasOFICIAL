<?php

require_once __DIR__ . '/../uploads/auth.php';
require_once __DIR__ . '/../repository/livroRepository.php';

$repo     = new livroRepository();
$livros = $repo->listarPorUsuario($_SESSION['usuario_id']);

require_once __DIR__ . '/../uploads/header.php';
?>

<div class="page-header">
  <h2>Meus Livros</h2>
  <a href="livro_create.php" class="btn btn-primary">+ Novo Livro</a>
</div>

<?php if (empty($livros)): ?>
  <div class="empty-state">
    <p>Você ainda não cadastrou nenhum livro.</p>
    <a href="livro_create.php" class="btn btn-primary">Cadastrar agora</a>
  </div>
<?php else: ?>
  <div class="table-wrapper">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Nome</th>
          <th>genero</th>
          <th>nota</th>
          <th>Autor</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($livros as $livro): ?>
          <tr>
            <td><?= $livro->getId() ?></td>
            <td><strong><?= htmlspecialchars($livro->getNome()) ?></strong></td>
            <td><span class="badge badge-tipo"><?= htmlspecialchars($livro->getGenero()) ?></span></td>
            <td>Lv. <?= $livro->getNota() ?></td>
            <td class="acoes">
              <a href="livro_editar.php?id=<?= $livro->getId() ?>" class="btn btn-sm btn-editar">Editar</a>
              <a href="livro_delete.php?id=<?= $livro->getId() ?>" class="btn btn-sm btn-excluir">Excluir</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../uploads/footer.php'; ?>