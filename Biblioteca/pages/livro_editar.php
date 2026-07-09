<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../_repository/LivroRepository.php';
require_once __DIR__ . '/../_repository/GeneroRepository.php';
require_once __DIR__ . '/../_repository/AutorRepository.php';

$repo = new LivroRepository();
$repoAutor = new AutorRepository();
$repoGenero = new GeneroRepository();

$generos = $repoGenero->listar();

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


$autoresDoLivro = $repoAutor->buscarAutoresLivro($livro->getId());
$autorNomeAtual = !empty($autoresDoLivro) ? $autoresDoLivro[0]->getNome() : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome   = trim($_POST['nome_livro'] ?? '');
    $genero = (int)($_POST['genero'] ?? '');
    $nota   = (int) ($_POST['nota'] ?? 1);
    $autorNomeDigitado = trim($_POST['nome_autor'] ?? '');

    
    $nome_arquivo_salvo = $livro->getCapa(); 

    try {
        if ($autorNomeDigitado === '') {
            throw new InvalidArgumentException("O nome do autor é obrigatório.");
        }

       
        if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['capa']['tmp_name'];
            $fileName    = $_FILES['capa']['name'];
            $fileSize    = $_FILES['capa']['size'];
            
            $extensao = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            $tamanho_maximo = 2 * 1024 * 1024; // 2MB

            if (!in_array($extensao, $extensoes_permitidas)) {
                throw new InvalidArgumentException("Formatos permitidos: JPG, PNG e WEBP.");
            }
            
            if ($fileSize > $tamanho_maximo) {
                throw new InvalidArgumentException("A nova imagem não pode ser maior que 2MB.");
            }

          
            $novo_nome = uniqid() . '.' . $extensao;
            $diretorio_destino = __DIR__ . '/../uploads/' . $novo_nome;

            if (move_uploaded_file($fileTmpPath, $diretorio_destino)) {
                if ($livro->getCapa()) {
                    $antigaCapa = __DIR__ . '/../uploads/' . $livro->getCapa();
                    if (file_exists($antigaCapa)) {
                        unlink($antigaCapa);
                    }
                }
                $nome_arquivo_salvo = $novo_nome;
            }
        }

        $novoAutor = Autor::novo($autorNomeDigitado);
        $repoAutor->salvar($novoAutor);
        $autorId = $novoAutor->getId();

       
        $livro->alterarDados($nome, $genero, $nota, $nome_arquivo_salvo, [$autorId]);
        
        
        $repo->salvar($livro);

        header('Location: index.php?editado=1');
        exit;

    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  
  <h2>Editar Livro</h2>
  <a href="index.php" class="btn btn-ghost">← Voltar</a>
</div>

<?php if ($erro !== ''): ?>
  <div class="alert alert-erro" style="color: red; font-weight: bold; margin-bottom: 15px;"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="form-card">
  <form method="POST" action="livro_editar.php?id_livro=<?= $livro->getId() ?>" enctype="multipart/form-data">

    <div class="form-group">
      <label for="nome">Nome do Livro</label>
      <input
        type="text"
        id="nome"
        name="nome_livro"
        value="<?= htmlspecialchars($nome) ?>"
        required
      />
    </div>

     <div class="form-group">
      <label for="autor">Nome do Autor</label>
      <input
        type="text"
        id="autor"
        name="nome_autor"
        value="<?= htmlspecialchars($_SERVER['REQUEST_METHOD'] === 'POST' ? $autorNomeDigitado : $autorNomeAtual) ?>"
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

    <div class="form-group">
      <label>Capa Atual:</label><br>
      <?php if ($livro->getCapa()): ?>
         <img src="../public/uploads/<?= htmlspecialchars($livro->getCapa()) ?>" width="120" style="border-radius: 4px; margin-bottom: 10px;"><br>
      <?php else: ?>
         <p style="color: gray; font-style: italic;">Sem capa cadastrada</p>
      <?php endif; ?>
      
      <label for="capa">Substituir Capa (Deixe em branco para manter a atual):</label>
      <input
        type="file"
        id="capa"
        name="capa"
        accept=".jpg,.jpeg,.png,.webp"
      />
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Salvar alterações</button>
      <a href="index.php" class="btn btn-ghost">Cancelar</a>
    </div>

  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>