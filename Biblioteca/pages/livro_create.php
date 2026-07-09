<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../_repository/LivroRepository.php';
require_once __DIR__ . '/../_repository/GeneroRepository.php';
require_once __DIR__ . '/../_repository/AutorRepository.php';
require_once __DIR__ . '/../modelo/livro.php';
require_once __DIR__ . '/../modelo/autor.php';

$repo = new LivroRepository();

$repoGenero = new GeneroRepository();
$generos = $repoGenero->listar();
$repoAutor = new AutorRepository();



$erro = '';
$nome = '';
$genero = '';
$nota = 1;
$autorDigitado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome          = trim($_POST['nome_livro'] ?? '');
    $genero        = (int)($_POST['genero'] ?? 0);
    $nota          = (int)($_POST['nota'] ?? 1);
    $autorDigitado = trim($_POST['nome_autor'] ?? '');
    $status = $_POST['status'] ?? 'quero_ler';

    $nome_arquivo_salvo = null;

    try {
        if ($autorDigitado === '') {
            throw new InvalidArgumentException("O nome do autor é obrigatório.");
        }

        //  IMAGEM DA CAPA 
        if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['capa']['tmp_name'];
            $fileName    = $_FILES['capa']['name'];
            $fileSize    = $_FILES['capa']['size'];
            
            $extensao = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            $tamanho_maximo = 2 * 1024 * 1024; // 2MB

            if (!in_array($extensao, $extensoes_permitidas)) {
                throw new InvalidArgumentException("Apenas formatos JPG, PNG e WEBP são aceitos.");
            }
            
            if ($fileSize > $tamanho_maximo) {
                throw new InvalidArgumentException("A imagem não pode ser maior que 2MB.");
            }

            $nome_arquivo_salvo = uniqid() . '.' . $extensao;
            $diretorio_destino  = __DIR__ . '/../uploads/' . $nome_arquivo_salvo;
              
                if (!move_uploaded_file($fileTmpPath, $diretorio_destino)) {
                throw new RuntimeException("Erro ao salvar o arquivo de imagem.");
            }
        } else {
            throw new InvalidArgumentException("O envio da imagem da capa é obrigatório.");
        }

      
        $novoAutor = Autor::novo($autorDigitado);
        $repoAutor->salvar($novoAutor);
        $autorId = $novoAutor->getId(); 
        

       
        $livro = Livro::novo($nome, $genero, $nota, $_SESSION['usuario_id'], $nome_arquivo_salvo, [$autorId]);
        $repo->salvar($livro);

        header('Location: index.php?sucesso=1');
        exit;

    } catch (Exception $e) {
        $erro = $e->getMessage();
       
        if ($nome_arquivo_salvo && file_exists(__DIR__ . '/../uploads/' . $nome_arquivo_salvo)) {
            unlink(__DIR__ . 'C:\xampp2\htdocs\Biblioteca\uploads' . $nome_arquivo_salvo);
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  
  <h2>Novo Livro</h2>
  <a href="index.php" class="btn btn-ghost">← Voltar</a>
</div>

<div class="form-group">

<?php if ($erro !== ''): ?>
  <div class="alert alert-erro" style="color: red; font-weight: bold; margin-bottom: 15px;"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="form-card">
  <form method="POST" action="livro_create.php" enctype="multipart/form-data">

    <div class="form-group">
      <label for="nome">Nome do Livro</label>
      <input
        type="text"
        id="nome"
        name="nome_livro"
        placeholder="Ex: O Iluminado"
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
        placeholder="Ex: Stephen King"
        value="<?= htmlspecialchars($autorDigitado) ?>"
        required
      />
    </div>

    <div class="form-group">
        <label for="genero">Gênero</label>
        <select id="genero" name="genero" required>
            <option value="">Selecione um gênero...</option>
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
      <label for="capa">Capa do Livro (JPG, PNG ou WEBP - Máx: 2MB)</label>
      <input
        type="file"
        id="capa"
        name="capa"
        accept=".jpg,.jpeg,.png,.webp"
        required
      />
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Cadastrar Livro</button>
      <a href="index.php" class="btn btn-ghost">Cancelar</a>
    </div>

  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>