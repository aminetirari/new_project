<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth.php');
    exit;
}

require_once __DIR__ . '/../../controllers/PostController.php';

$postId = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$postId || !is_numeric($postId)) {
    header('Location: posts_list.php');
    exit;
}
$postId = (int)$postId;

$postController = new PostController();
$result = $postController->update($postId, 'posts_list.php');
$post = $result['post'];
$errors = $result['errors'] ?? [];
$activeNav = 'posts';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Modifier le post - NutriMind Admin</title>
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
    <div><h4 class="mb-0">Modifier le post</h4></div>
</nav>

<?php include __DIR__ . '/_sidebar.php'; ?>

<main id="content" class="content py-10">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="fs-3 mb-1">Modifier le post #<?php echo (int)$post['id']; ?></h1>
                        <p class="text-secondary mb-0">Mettre à jour le titre et le contenu du post.</p>
                    </div>
                    <a href="posts_list.php" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Retour à la liste
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="row mb-4">
                <div class="col-lg-8 mx-auto">
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $e): ?>
                                <li><?php echo htmlspecialchars($e); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="post_edit.php?id=<?php echo (int)$post['id']; ?>" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo (int)$post['id']; ?>">

                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Titre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title"
                                       value="<?php echo htmlspecialchars($post['title']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label fw-bold">Contenu <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content"
                                          rows="12" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Photo</label>
                                <?php if (!empty($post['image_path'])): ?>
                                    <div class="mb-2">
                                        <img src="../<?php echo htmlspecialchars($post['image_path']); ?>" alt="" style="max-width:200px;max-height:200px;border-radius:4px;">
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                        <label for="remove_image" class="form-check-label">Supprimer la photo actuelle</label>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                                <small class="text-secondary">JPG, PNG, GIF ou WEBP &middot; 5 Mo max</small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-secondary">
                                    Créé le <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                    <?php if (!empty($post['updated_at'])): ?>
                                        · Mis à jour le <?php echo date('d/m/Y H:i', strtotime($post['updated_at'])); ?>
                                    <?php endif; ?>
                                </small>
                                <div>
                                    <a href="posts_list.php" class="btn btn-outline-secondary">Annuler</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-2"></i>Enregistrer
                                    </button>
                                </div>
                            </div>
                        </form>
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
