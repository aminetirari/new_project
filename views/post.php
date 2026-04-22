<?php
session_start();
require_once __DIR__ . '/../controllers/PostController.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: posts_list.php');
    exit;
}

$postController = new PostController();
$data = $postController->loadPost((int)$_GET['id']);

if ($data === null) {
    header('Location: posts_list.php');
    exit;
}

$post = $data['post'];
$comments = $data['comments'];
$currentUserId = $_SESSION['user_id'] ?? null;
$currentUserRole = $_SESSION['user_role'] ?? null;
?>
<?php include __DIR__ . '/header.php'; ?>

<!-- post page styles (reused from post.html design) -->
<style>
    .single-article-section { background:#f9f9f9; padding:30px; border-radius:5px; margin-bottom:40px; }
    .single-artcile-bg { margin-bottom:20px; }
    .single-artcile-bg img { width:100%; height:auto; border-radius:5px; }
    .blog-meta { margin:20px 0; font-size:14px; color:#666; }
    .blog-meta span { margin-right:20px; }
    .single-article-text p { line-height:1.8; color:#333; margin-bottom:15px; }
    .comments-section { margin-top:50px; border-top:2px solid #e0e0e0; padding-top:30px; }
    .comments-section h3 { font-size:24px; margin-bottom:30px; font-weight:bold; }
    .comment { margin-bottom:30px; padding:20px; background:#fff; border-left:3px solid #ff6b6b; border-radius:3px; position:relative; }
    .comment-author strong { font-size:16px; color:#333; }
    .comment-date { font-size:13px; color:#999; margin-left:10px; }
    .comment p { margin:0; color:#555; line-height:1.6; }
    .comment .delete-link { position:absolute; top:12px; right:14px; font-size:13px; color:#c0392b; text-decoration:none; }
    .comment .delete-link:hover { text-decoration:underline; }
    .comment-form { background:#f9f9f9; padding:30px; border-radius:5px; margin-top:30px; }
    .comment-form h4 { font-size:20px; margin-bottom:20px; font-weight:bold; }
    .form-group { margin-bottom:20px; }
    .form-control { border:1px solid #ddd; padding:10px; border-radius:3px; width:100%; }
    .btn-primary { background:#ff6b6b; color:#fff; padding:10px 30px; border:none; border-radius:3px; cursor:pointer; font-weight:bold; }
    .btn-primary:hover { background:#ff5252; }
    .alert { padding:15px; margin-bottom:20px; border-radius:3px; }
    .alert-danger { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
    .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
</style>

<!-- breadcrumb-section -->
<div class="breadcrumb-section breadcrumb-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p>Lire nos dernières actualités</p>
                    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end breadcrumb section -->

<div class="mt-150 mb-150">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="single-article-section">
                    <div class="single-article-text">
                        <p class="blog-meta">
                            <span class="author"><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author_name'] ?? 'Inconnu'); ?></span>
                            <span class="date"><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                        </p>
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    </div>
                </div>

                <div class="comments-section">
                    <h3>Commentaires (<?php echo count($comments); ?>)</h3>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $c): ?>
                            <div class="comment">
                                <div class="comment-author">
                                    <strong><?php echo htmlspecialchars($c['user_name'] ?? 'Utilisateur'); ?></strong>
                                    <span class="comment-date"><?php echo date('F j, Y H:i', strtotime($c['created_at'])); ?></span>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($c['content'])); ?></p>
                                <?php if ($currentUserId && ((int)$c['user_id'] === (int)$currentUserId || $currentUserRole === 'admin')): ?>
                                    <a class="delete-link"
                                       href="delete_comment.php?id=<?php echo (int)$c['id']; ?>"
                                       onclick="return confirm('Supprimer ce commentaire ?');">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Pas encore de commentaires. Soyez le premier à commenter !</p>
                    <?php endif; ?>

                    <?php if ($currentUserId): ?>
                        <div class="comment-form">
                            <h4>Laisser un commentaire</h4>
                            <form action="add_comment.php" method="POST">
                                <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
                                <div class="form-group">
                                    <textarea name="content" class="form-control" rows="4" placeholder="Écrivez votre commentaire ici..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Soumettre</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p><a href="auth.php">Connectez-vous</a> pour laisser un commentaire.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sidebar-section" style="background:#f9f9f9; padding:30px; border-radius:5px;">
                    <h4 style="font-weight:bold; margin-bottom:20px;">Retour au blog</h4>
                    <a href="posts_list.php" class="btn-primary" style="display:inline-block; text-decoration:none;">Tous les posts</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
