<?php

require_once __DIR__ . '/../uploads/auth.php';
require_once __DIR__ . '/../repository/livroRepository.php';
require_once __DIR__ . '/../repository/generoRepository.php';

$repo = new LivroRepository();

$repoAutor = new autorRepository();
$autor = $repoAutor->Listar();

$repoGenero = new generoRepository();
$generos = $repoGenero->listar();



$erro = '';
$nome = '';
$genero = '';
$nota = 1;
$autor ='';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome_livro'] ?? '');
    $genero  = (int)($_POST['genero'] ?? '');
    $nota = (int) ($_POST['nota'] ?? 1);
    $autor = trim($_POST['nome_autor'] ?? '');

    try {
        $livro = Livro ::novo($nome, $genero, $nota, $_SESSION['usuario_id'], $autor);
        $repo->salvar($livro);

        $novoAutor = Autor::novo($autor);
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
  <h2>Novo Livro</h2>
  <a href="index.php" class="btn btn-ghost">← Voltar</a>
</div>

<?php if ($erro !== ''): ?>
  <div class="alert alert-erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="form-card">
  <form method="POST" action="livro_create.php">

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
      <label for="autor">Autor</label>
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
      <button type="submit" class="btn btn-primary">Cadastrar Livro</button>
      <a href="index.php" class="btn btn-ghost">Cancelar</a>
    </div>

  </form>
</div>

<?php require_once __DIR__ . '/../uploads/footer.php'; ?>