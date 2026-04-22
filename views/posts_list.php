<?php
session_start();
require_once __DIR__ . '/../controllers/PostController.php';

$postController = new PostController();
$posts = $postController->getAllPosts();
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$isAdmin = $isLoggedIn && ($_SESSION['user_role'] ?? '') === 'admin';
$currentUserId = (int)($_SESSION['user_id'] ?? 0);
?>
<?php include __DIR__ . '/header.php'; ?>

<style>
    .posts-toolbar { margin-bottom: 30px; display:flex; justify-content: space-between; align-items:center; flex-wrap:wrap; gap:15px; }
    .posts-toolbar .btn-create {
        background:#ff6b6b; color:#fff; padding:10px 24px; border-radius:3px;
        text-decoration:none; font-weight:bold; border:none; display:inline-block;
    }
    .posts-toolbar .btn-create:hover { background:#ff5252; color:#fff; }
    .single-latest-news {
        background:#fff; border-radius:6px; overflow:hidden;
        box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:30px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height:100%; display:flex; flex-direction:column;
    }
    .single-latest-news:hover { transform: translateY(-4px); box-shadow:0 6px 18px rgba(0,0,0,0.12); }
    .post-image-wrap { display:block; width:100%; height:220px; overflow:hidden; background:#f3f3f3; }
    .post-image-wrap img { width:100%; height:100%; object-fit:cover; }
    .news-text-box { padding:20px; flex:1; display:flex; flex-direction:column; }
    .news-text-box h3 { font-size:20px; margin-bottom:10px; }
    .news-text-box h3 a { color:#222; text-decoration:none; }
    .news-text-box h3 a:hover { color:#ff6b6b; }
    .blog-meta { color:#888; font-size:13px; margin-bottom:10px; display:flex; flex-wrap:wrap; gap:10px; }
    .excerpt { color:#555; line-height:1.6; flex:1; }
    .read-more-btn {
        display:inline-block; margin-top:10px; color:#ff6b6b; font-weight:bold; text-decoration:none;
    }
    .read-more-btn:hover { color:#ff5252; text-decoration:underline; }
    .counts-row {
        display:flex; gap:10px; margin:10px 0; align-items:center; flex-wrap:wrap;
    }
    .pill {
        background:#f1f1f1; color:#555; padding:4px 12px; border-radius:999px;
        font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px;
    }
    .pill.liked { background:#ff6b6b; color:#fff; }
    .like-form { display:inline; margin:0; }
    .like-btn {
        background:#fff; border:1px solid #e0e0e0; color:#555; padding:4px 12px;
        border-radius:999px; font-size:13px; font-weight:600; cursor:pointer;
        display:inline-flex; align-items:center; gap:6px;
        transition: background 0.15s, color 0.15s, border-color 0.15s;
    }
    .like-btn:hover { border-color:#ff6b6b; color:#ff6b6b; }
    .like-btn.liked { background:#ff6b6b; color:#fff; border-color:#ff6b6b; }
</style>

<!-- breadcrumb -->
<div class="breadcrumb-section breadcrumb-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p>Lire nos dernières actualités</p>
                    <h1>Tous les Posts</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-150 mb-150">
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" style="padding:15px;background:#d4edda;color:#155724;border-radius:3px;margin-bottom:20px;">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="padding:15px;background:#f8d7da;color:#721c24;border-radius:3px;margin-bottom:20px;">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="posts-toolbar">
            <h2 style="margin:0; font-weight:bold;">Tous les posts</h2>
            <?php if ($isLoggedIn): ?>
                <a href="post_create.php" class="btn-create">
                    <i class="fas fa-plus"></i> Nouveau post
                </a>
            <?php else: ?>
                <a href="auth.php" class="btn-create">
                    <i class="fas fa-sign-in-alt"></i> Connectez-vous pour publier
                </a>
            <?php endif; ?>
        </div>

        <div class="row">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <?php
                    $postId = (int)$post['id'];
                    $likeCount = (int)($post['like_count'] ?? 0);
                    $commentCount = (int)($post['comment_count'] ?? 0);
                    $likedByMe = !empty($post['liked_by_me']);
                    $isAuthor = $currentUserId && ((int)$post['author_id'] === $currentUserId);
                    $canEdit = $isAuthor || $isAdmin;
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-latest-news">
                            <?php if (!empty($post['image_path'])): ?>
                                <a class="post-image-wrap" href="post.php?id=<?php echo $postId; ?>">
                                    <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="">
                                </a>
                            <?php endif; ?>
                            <div class="news-text-box">
                                <h3>
                                    <a href="post.php?id=<?php echo $postId; ?>">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </h3>
                                <p class="blog-meta">
                                    <span class="author"><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author_name'] ?? 'Inconnu'); ?></span>
                                    <span class="date"><i class="fas fa-calendar"></i> <?php echo date('d M Y', strtotime($post['created_at'])); ?></span>
                                </p>
                                <p class="excerpt">
                                    <?php
                                    $excerpt = strip_tags($post['content']);
                                    echo htmlspecialchars(mb_strlen($excerpt) > 140 ? mb_substr($excerpt, 0, 140) . '...' : $excerpt);
                                    ?>
                                </p>

                                <div class="counts-row">
                                    <?php if ($isLoggedIn): ?>
                                        <form class="like-form" action="toggle_like.php" method="POST">
                                            <input type="hidden" name="target_type" value="post">
                                            <input type="hidden" name="target_id" value="<?php echo $postId; ?>">
                                            <input type="hidden" name="redirect" value="posts_list.php">
                                            <button type="submit" class="like-btn <?php echo $likedByMe ? 'liked' : ''; ?>">
                                                <i class="<?php echo $likedByMe ? 'fas' : 'far'; ?> fa-heart"></i>
                                                <?php echo $likeCount; ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="pill"><i class="far fa-heart"></i> <?php echo $likeCount; ?></span>
                                    <?php endif; ?>
                                    <span class="pill"><i class="far fa-comment"></i> <?php echo $commentCount; ?></span>
                                </div>

                                <a href="post.php?id=<?php echo $postId; ?>" class="read-more-btn">
                                    Lire plus <i class="fas fa-angle-right"></i>
                                </a>

                                <?php if ($canEdit): ?>
                                    <div style="margin-top:15px;border-top:1px solid #eee;padding-top:10px;">
                                        <a href="post_edit.php?id=<?php echo $postId; ?>"
                                           style="color:#3498db;margin-right:15px;text-decoration:none;">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <a href="post_delete.php?id=<?php echo $postId; ?>"
                                           onclick="return confirm('Supprimer ce post et tous ses commentaires ?');"
                                           style="color:#c0392b;text-decoration:none;">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-lg-12 text-center">
                    <p>Aucun post pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
