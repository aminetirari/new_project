<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth.php');
    exit;
}

require_once __DIR__ . '/../../controllers/PostController.php';
$postController = new PostController();
$posts = $postController->getAllPosts();
$activeNav = 'posts';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gérer les posts - NutriMind Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/images/logooo.png">
    <script type="module" crossorigin src="assets/js/main.js"></script>
    <link rel="stylesheet" crossorigin href="assets/css/main.css">
</head>
<body>
<div id="overlay" class="overlay"></div>

<nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
    <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm">
        <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>
    <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
        <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>
    <div><h4 class="mb-0">Gérer les posts</h4></div>
</nav>

<?php include __DIR__ . '/_sidebar.php'; ?>

<main id="content" class="content py-10">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="fs-3 mb-1">Posts du blog</h1>
                        <p class="text-secondary mb-0">Créer, modifier et supprimer les posts visibles sur le site.</p>
                    </div>
                    <a href="post_create.php" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>Nouveau post
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($posts)): ?>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">#</th>
                                            <th>Titre</th>
                                            <th>Auteur</th>
                                            <th>Date</th>
                                            <th class="text-center">Commentaires</th>
                                            <th class="text-end" style="width:220px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($posts as $p): ?>
                                            <tr>
                                                <td><?php echo (int)$p['id']; ?></td>
                                                <td>
                                                    <a href="../post.php?id=<?php echo (int)$p['id']; ?>" target="_blank">
                                                        <?php echo htmlspecialchars($p['title']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($p['author_name'] ?? '—'); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($p['created_at'])); ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary"><?php echo (int)($p['comment_count'] ?? 0); ?></span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="post_edit.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="ti ti-edit"></i> Modifier
                                                    </a>
                                                    <a href="post_delete.php?id=<?php echo (int)$p['id']; ?>"
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('Supprimer ce post et tous ses commentaires ?');">
                                                        <i class="ti ti-trash"></i> Supprimer
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5 text-secondary">
                                <p class="mb-3">Aucun post pour le moment.</p>
                                <a href="post_create.php" class="btn btn-primary">Créer le premier post</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function logout() {
    if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
        window.location.href = '../logout.php';
    }
}
</script>
</body>
</html>
