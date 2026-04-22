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
$currentUserId = (int)($_SESSION['user_id'] ?? 0);
$currentUserRole = $_SESSION['user_role'] ?? null;
$isLoggedIn = $currentUserId > 0;
$isAdmin = ($currentUserRole === 'admin');
$isPostAuthor = $currentUserId && ((int)$post['author_id'] === $currentUserId);
$canEditPost = $isPostAuthor || $isAdmin;
?>
<?php include __DIR__ . '/header.php'; ?>

<!-- post page styles -->
<style>
    .single-article-section { background:#f9f9f9; padding:30px; border-radius:5px; margin-bottom:40px; }
    .single-article-img { margin-bottom:20px; border-radius:5px; overflow:hidden; }
    .single-article-img img { width:100%; height:auto; display:block; }
    .blog-meta { margin:20px 0; font-size:14px; color:#666; }
    .blog-meta span { margin-right:20px; }
    .single-article-text p { line-height:1.8; color:#333; margin-bottom:15px; }
    .post-actions { display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-top:20px; border-top:1px solid #e5e5e5; padding-top:15px; }
    .like-form { display:inline; margin:0; }
    .like-btn {
        background:#fff; border:1px solid #e0e0e0; color:#555; padding:6px 14px;
        border-radius:999px; font-size:14px; font-weight:600; cursor:pointer;
        display:inline-flex; align-items:center; gap:8px;
        transition: background 0.15s, color 0.15s, border-color 0.15s;
    }
    .like-btn:hover { border-color:#ff6b6b; color:#ff6b6b; }
    .like-btn.liked { background:#ff6b6b; color:#fff; border-color:#ff6b6b; }
    .pill {
        background:#f1f1f1; color:#555; padding:6px 14px; border-radius:999px;
        font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:8px;
    }
    .edit-btn { color:#3498db; text-decoration:none; margin-left:auto; font-size:14px; }
    .edit-btn:hover { text-decoration:underline; }
    .del-btn { color:#c0392b; text-decoration:none; font-size:14px; }
    .del-btn:hover { text-decoration:underline; }

    .comments-section { margin-top:50px; border-top:2px solid #e0e0e0; padding-top:30px; }
    .comments-section h3 { font-size:24px; margin-bottom:30px; font-weight:bold; }
    .comment { margin-bottom:20px; padding:20px; background:#fff; border-left:3px solid #ff6b6b; border-radius:3px; }
    .comment-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:10px; }
    .comment-author strong { font-size:16px; color:#333; }
    .comment-date { font-size:13px; color:#999; margin-left:10px; }
    .comment p { margin:0; color:#555; line-height:1.6; }
    .comment-footer { margin-top:12px; display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    .comment .delete-link { font-size:13px; color:#c0392b; text-decoration:none; margin-left:auto; }
    .comment .delete-link:hover { text-decoration:underline; }

    .comment-form { background:#f9f9f9; padding:30px; border-radius:5px; margin-top:30px; }
    .comment-form h4 { font-size:20px; margin-bottom:20px; font-weight:bold; }
    .form-group { margin-bottom:20px; }
    .form-control { border:1px solid #ddd; padding:10px; border-radius:3px; width:100%; }
    .btn-primary { background:#ff6b6b; color:#fff; padding:10px 30px; border:none; border-radius:3px; cursor:pointer; font-weight:bold; text-decoration:none; display:inline-block; }
    .btn-primary:hover { background:#ff5252; color:#fff; }
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
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="single-article-section">
                    <?php if (!empty($post['image_path'])): ?>
                        <div class="single-article-img">
                            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="">
                        </div>
                    <?php endif; ?>

                    <div class="single-article-text">
                        <p class="blog-meta">
                            <span class="author"><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author_name'] ?? 'Inconnu'); ?></span>
                            <span class="date"><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                        </p>
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    </div>

                    <div class="post-actions">
                        <?php if ($isLoggedIn): ?>
                            <form class="like-form" action="toggle_like.php" method="POST">
                                <input type="hidden" name="target_type" value="post">
                                <input type="hidden" name="target_id" value="<?php echo (int)$post['id']; ?>">
                                <input type="hidden" name="redirect" value="post.php?id=<?php echo (int)$post['id']; ?>">
                                <button type="submit" class="like-btn <?php echo !empty($post['liked_by_me']) ? 'liked' : ''; ?>">
                                    <i class="<?php echo !empty($post['liked_by_me']) ? 'fas' : 'far'; ?> fa-heart"></i>
                                    J'aime &middot; <?php echo (int)($post['like_count'] ?? 0); ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="pill"><i class="far fa-heart"></i> <?php echo (int)($post['like_count'] ?? 0); ?></span>
                        <?php endif; ?>

                        <span class="pill"><i class="far fa-comment"></i> <?php echo count($comments); ?></span>

                        <?php if ($canEditPost): ?>
                            <a class="edit-btn" href="post_edit.php?id=<?php echo (int)$post['id']; ?>">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a class="del-btn"
                               href="post_delete.php?id=<?php echo (int)$post['id']; ?>"
                               onclick="return confirm('Supprimer ce post et tous ses commentaires ?');">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="comments-section">
                    <h3>Commentaires (<?php echo count($comments); ?>)</h3>

                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $c): ?>
                            <?php
                            $cId = (int)$c['id'];
                            $cOwner = $currentUserId && ((int)$c['user_id'] === $currentUserId);
                            $canDeleteComment = $cOwner || $isAdmin;
                            ?>
                            <div class="comment">
                                <div class="comment-header">
                                    <div class="comment-author">
                                        <strong><?php echo htmlspecialchars($c['user_name'] ?? 'Utilisateur'); ?></strong>
                                        <span class="comment-date"><?php echo date('F j, Y H:i', strtotime($c['created_at'])); ?></span>
                                    </div>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($c['content'])); ?></p>
                                <div class="comment-footer">
                                    <?php if ($isLoggedIn): ?>
                                        <form class="like-form" action="toggle_like.php" method="POST">
                                            <input type="hidden" name="target_type" value="comment">
                                            <input type="hidden" name="target_id" value="<?php echo $cId; ?>">
                                            <input type="hidden" name="redirect" value="post.php?id=<?php echo (int)$post['id']; ?>">
                                            <button type="submit" class="like-btn <?php echo !empty($c['liked_by_me']) ? 'liked' : ''; ?>">
                                                <i class="<?php echo !empty($c['liked_by_me']) ? 'fas' : 'far'; ?> fa-heart"></i>
                                                <?php echo (int)($c['like_count'] ?? 0); ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="pill"><i class="far fa-heart"></i> <?php echo (int)($c['like_count'] ?? 0); ?></span>
                                    <?php endif; ?>

                                    <?php if ($canDeleteComment): ?>
                                        <a class="delete-link"
                                           href="delete_comment.php?id=<?php echo $cId; ?>"
                                           onclick="return confirm('Supprimer ce commentaire ?');">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Pas encore de commentaires. Soyez le premier à commenter !</p>
                    <?php endif; ?>

                    <?php if ($isLoggedIn): ?>
                        <div class="comment-form">
                            <h4>Laisser un commentaire</h4>
                            <form action="add_comment.php" method="POST">
                                <input type="hidden" name="post_id" value="<?php echo (int)$post['id']; ?>">
                                <div class="form-group">
                                    <textarea name="content" class="form-control" rows="4" placeholder="Écrivez votre commentaire ici..." required></textarea>
                                </div>
                                <button type="submit" class="btn-primary">Soumettre</button>
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
