<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../_repository/LivroRepository.php';
require_once __DIR__ . '/../_repository/UsuarioRepository.php';

$usuarioRepo = new UsuarioRepository();
$livroRepo = new LivroRepository();


// Busca apenas os livros do usuário que está logado na sessão
$livros = $livroRepo->listarPorUsuario($_SESSION['usuario_id']);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-container">
    <div class="page-header">
        <div>
            <h2>Meus Livros</h2>
            <p>Bem-vindo de volta, <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong>!</p>
        </div>
        <a href="livro_create.php" class="btn btn-primary">+ Novo Livro</a>

        <a href="logoout.php" class="btn btn-secundary"> Deslogar</a>
    
    </div>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-sucesso">Livro cadastrado com sucesso!</div>
    <?php endif; ?>
    <?php if (isset($_GET['editado'])): ?>
        <div class="alert alert-sucesso">Livro atualizado com sucesso!</div>
    <?php endif; ?>
    <?php if (isset($_GET['excluido'])): ?>
        <div class="alert alert-alerta">Livro removido com sucesso.</div>
    <?php endif; ?>

    <?php if (empty($livros)): ?>
        <div class="empty-state">
            <p>Você ainda não tem nenhum livro cadastrado na sua estante.</p>
            <a href="livro_create.php" class="btn btn-secondary">Começar a cadastrar</a>
        </div>
    <?php else: ?>
        <div class="books-grid">
            <?php foreach ($livros as $livro): 
                // Busca o autor vinculado a este livro específico
                $nomeAutor = !empty($autores)
                ?$autores[0]['nome_autor']
                : 'Autor desconhecido';
            ?>
                <div class="book-card">
                    <div class="book-cover">
                        <?php if ($livro->getCapa()): ?>
                            <img src="../public/uploads/<?= htmlspecialchars($livro->getCapa()) ?>" alt="Capa de <?= htmlspecialchars($livro->getNome()) ?>">
                        <?php else: ?>
                            <div class="no-cover">Sem Capa</div>
                        <?php endif; ?>
                    </div>
                    <div class="book-info">
                        <h3 class="book-title"><?= htmlspecialchars($livro->getNome()) ?></h3>
                        <p class="book-author">Por: <?= htmlspecialchars($nomeAutor) ?></p>
                        <span class="book-rating">★ <?= $livro->getNota() ?> / 5</span>
                    </div>
                    <div class="book-actions">
                        <a href="livro_editar.php?id_livro=<?= $livro->getId() ?>" class="btn-action btn-edit">Editar</a>
                        <a href="livro_delete.php?id_livro=<?= $livro->getId() ?>" class="btn-action btn-delete">Excluir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>