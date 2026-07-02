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

if ($livro === null || $livro->getUsuarioId() !== $_SESSION['usuario_id']) {
    header('Location: index.php');
    exit;
}

$erro = '';
$nome = $livro->getNome();
$genero = $livro->getGenero();
$nota = $livro->getNota();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome_livro'] ?? '');
    $genero  = (int)($_POST['genero'] ?? '');
    $nota = (int) ($_POST['nota'] ?? 1);
    $autor = trim ($_POST['nome_autor'] ?? '');

    try {
        $livro->alterarDados($nome, $genero, $nota, $autor);
        $repo->salvar($livro);

        $repoAutor->removerAutoresLivro(
        $livro->getId()
       );

       $novoAutor=Autor::novo($autor);

       $repoAutor->salvar($novoAutor);

      $repoAutor->salvarLivroAutor(
      $livro->getId(),
      $novoAutor->getId()
);

        header('Location: index.php');
        exit;
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    }
}

require_once __DIR__ . '/../uploads/header.php';
?>

<div class="page-header">
  <h2>Editar Livro</h2>
  <a href="index.php" class="btn btn-ghost">← Voltar</a>
</div>

<?php if ($erro !== ''): ?>
  <div class="alert alert-erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="form-card">
  <form method="POST" action="livro_editar.php?id_livro=<?= $livro->getId() ?>">

    <div class="form-group">
      <label for="nome">Nome do Livro</label>
      <input
        type="text"
        id="nome"
        name="nome_livro"
        placeholder="Ex: Suicidas"
        value="<?= htmlspecialchars($nome) ?>"
        required
      />
    </div>

     <div class="form-group">
      <label for="nome">Nome do Autor</label>
      <input
        type="text"
        id="autor"
        name="nome_autor"
        placeholder="Ex: Machado de Assis"
        value="<?= htmlspecialchars($autor) ?>"
        required
      />
    </div>

    

  <div class="form-group">
    <label for="Genero">Genero</label>
    <input
        type="text"
        id="genero"
        name="genero"
        placeholder="Ex: Romance"
        required
    >
</div><div class="form-group">
    <label for="genero">Gênero</label>

    <select id="genero" name="genero" required>

        <?php foreach ($generos as $g): ?>

            <option
                value="<?= $g['id_genero'] ?>"
                <?= ($g['id_genero'] == $genero) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($g['nome_genero']) ?>
            </option>

        <?php endforeach; ?>

    </select>
</div>

    <div class="form-group">
      <label for="nota">Nota (1 – 5)</label>
      <input
        type="number"
        id="nota"
        name="nota"
        min="1"
        max="5"
        value="<?= $nota ?>"
        required
      />
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Salvar alterações</button>
      <a href="index.php" class="btn btn-ghost">Cancelar</a>
    </div>

  </form>
</div>

<?php require_once __DIR__ . '/../uploads/footer.php'; ?>