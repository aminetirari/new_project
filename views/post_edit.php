<?php
session_start();
require_once __DIR__ . '/../controllers/PostController.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: posts_list.php');
    exit;
}

$postId = (int)$_GET['id'];
$controller = new PostController();
$state = $controller->update($postId, 'post.php?id=' . $postId);

$post = $state['post'] ?? null;
$errors = $state['errors'] ?? [];
if (!$post) {
    header('Location: posts_list.php');
    exit;
}
?>
<?php include __DIR__ . '/header.php'; ?>

<style>
    .edit-wrap { max-width:780px; margin:0 auto; background:#fff; padding:40px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .edit-wrap h2 { font-weight:bold; margin-bottom:25px; }
    .form-control { border:1px solid #ddd; padding:10px; border-radius:3px; width:100%; }
    .form-group { margin-bottom:20px; }
    .form-group label { font-weight:600; margin-bottom:8px; display:block; color:#333; }
    .btn-primary { background:#ff6b6b; color:#fff; padding:10px 30px; border:none; border-radius:3px; cursor:pointer; font-weight:bold; }
    .btn-primary:hover { background:#ff5252; }
    .btn-cancel { color:#666; margin-left:15px; text-decoration:none; }
    .alert-danger { padding:12px 15px; background:#f8d7da; color:#721c24; border-radius:3px; margin-bottom:20px; }
    .hint { color:#888; font-size:13px; margin-top:6px; }
    .current-image { max-width:200px; max-height:200px; border-radius:4px; display:block; margin-top:8px; }
    #image-preview { display:none; max-width:100%; max-height:260px; margin-top:10px; border-radius:4px; }
</style>

<div class="breadcrumb-section breadcrumb-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p>Mettre à jour votre post</p>
                    <h1>Modifier le post</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-150 mb-150">
    <div class="container">
        <div class="edit-wrap">
            <h2>Modifier le post</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert-danger">
                    <?php foreach ($errors as $e): ?>
                        <div><?php echo htmlspecialchars($e); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="post_edit.php?id=<?php echo $postId; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" id="title" name="title" class="form-control"
                           value="<?php echo htmlspecialchars($post['title']); ?>" required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="content">Contenu</label>
                    <textarea id="content" name="content" class="form-control" rows="8" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Photo actuelle</label>
                    <?php if (!empty($post['image_path'])): ?>
                        <img class="current-image" src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="">
                        <label style="font-weight:normal; margin-top:10px;">
                            <input type="checkbox" name="remove_image" value="1"> Supprimer la photo actuelle
                        </label>
                    <?php else: ?>
                        <div class="hint">Aucune photo.</div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="image">Remplacer la photo (optionnel)</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                    <div class="hint">JPG, PNG, GIF ou WEBP &middot; 5 Mo max</div>
                    <img id="image-preview" alt="Aperçu">
                </div>

                <button type="submit" class="btn-primary">Enregistrer</button>
                <a href="post.php?id=<?php echo $postId; ?>" class="btn-cancel">Annuler</a>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        var input = document.getElementById('image');
        var preview = document.getElementById('image-preview');
        if (!input || !preview) return;
        input.addEventListener('change', function (e) {
            var file = e.target.files && e.target.files[0];
            if (!file) { preview.style.display = 'none'; preview.src = ''; return; }
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        });
    })();
</script>

<?php include __DIR__ . '/footer.php'; ?>
