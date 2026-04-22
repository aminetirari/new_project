<?php
session_start();
require_once __DIR__ . '/../controllers/PostController.php';

$controller = new PostController();
$state = $controller->createFront();

$title = $state['title'] ?? '';
$content = $state['content'] ?? '';
$errors = $state['errors'] ?? [];
?>
<?php include __DIR__ . '/header.php'; ?>

<style>
    .create-wrap { max-width:780px; margin:0 auto; background:#fff; padding:40px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .create-wrap h2 { font-weight:bold; margin-bottom:25px; }
    .form-control { border:1px solid #ddd; padding:10px; border-radius:3px; width:100%; }
    .form-group { margin-bottom:20px; }
    .form-group label { font-weight:600; margin-bottom:8px; display:block; color:#333; }
    .btn-primary { background:#ff6b6b; color:#fff; padding:10px 30px; border:none; border-radius:3px; cursor:pointer; font-weight:bold; }
    .btn-primary:hover { background:#ff5252; }
    .btn-cancel { color:#666; margin-left:15px; text-decoration:none; }
    .alert-danger { padding:12px 15px; background:#f8d7da; color:#721c24; border-radius:3px; margin-bottom:20px; }
    .hint { color:#888; font-size:13px; margin-top:6px; }
    #image-preview { display:none; max-width:100%; max-height:260px; margin-top:10px; border-radius:4px; }
</style>

<div class="breadcrumb-section breadcrumb-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p>Partagez ce que vous pensez</p>
                    <h1>Nouveau post</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-150 mb-150">
    <div class="container">
        <div class="create-wrap">
            <h2>Publier un post</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert-danger">
                    <?php foreach ($errors as $e): ?>
                        <div><?php echo htmlspecialchars($e); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="post_create.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" id="title" name="title" class="form-control"
                           value="<?php echo htmlspecialchars($title); ?>" required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="content">Contenu</label>
                    <textarea id="content" name="content" class="form-control" rows="8" required><?php echo htmlspecialchars($content); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Photo (optionnel)</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                    <div class="hint">JPG, PNG, GIF ou WEBP &middot; 5 Mo max</div>
                    <img id="image-preview" alt="Aperçu">
                </div>

                <button type="submit" class="btn-primary">Publier</button>
                <a href="posts_list.php" class="btn-cancel">Annuler</a>
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
