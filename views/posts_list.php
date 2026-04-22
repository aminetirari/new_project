<?php
session_start();
require_once __DIR__ . '/../controllers/PostController.php';

$postController = new PostController();
$posts = $postController->getAllPosts();
$isAdmin = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true
    && ($_SESSION['user_role'] ?? '') === 'admin';
?>
<?php include __DIR__ . '/header.php'; ?>

<style>
    .posts-toolbar { margin-bottom: 30px; display:flex; justify-content: space-between; align-items:center; }
    .posts-toolbar .btn-create {
        background:#ff6b6b; color:#fff; padding:10px 24px; border-radius:3px;
        text-decoration:none; font-weight:bold; border:none;
    }
    .posts-toolbar .btn-create:hover { background:#ff5252; color:#fff; }
    .single-latest-news {
        background:#fff; border-radius:6px; overflow:hidden;
        box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:30px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height:100%;
    }
    .single-latest-news:hover { transform: translateY(-4px); box-shadow:0 6px 18px rgba(0,0,0,0.12); }
    .news-text-box { padding:20px; }
    .news-text-box h3 { font-size:20px; margin-bottom:10px; }
    .news-text-box h3 a { color:#222; text-decoration:none; }
    .news-text-box h3 a:hover { color:#ff6b6b; }
    .blog-meta { color:#888; font-size:13px; margin-bottom:10px; }
    .blog-meta span { margin-right:15px; }
    .excerpt { color:#555; line-height:1.6; }
    .read-more-btn {
        display:inline-block; margin-top:10px; color:#ff6b6b; font-weight:bold; text-decoration:none;
    }
    .read-more-btn:hover { color:#ff5252; text-decoration:underline; }
    .comment-count-badge {
        background:#ff6b6b; color:#fff; padding:3px 10px; border-radius:12px;
        font-size:12px; font-weight:bold;
    }
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

        <?php if ($isAdmin): ?>
            <div class="posts-toolbar">
                <h2 style="margin:0; font-weight:bold;">Gérer les posts</h2>
                <a href="backoffice/post_create.php" class="btn-create">
                    <i class="fas fa-plus"></i> Nouveau post
                </a>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-latest-news">
                            <div class="news-text-box">
                                <h3>
                                    <a href="post.php?id=<?php echo (int)$post['id']; ?>">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </h3>
                                <p class="blog-meta">
                                    <span class="author"><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author_name'] ?? 'Inconnu'); ?></span>
                                    <span class="date"><i class="fas fa-calendar"></i> <?php echo date('d M Y', strtotime($post['created_at'])); ?></span>
                                    <span class="comment-count-badge"><i class="fas fa-comment"></i> <?php echo (int)($post['comment_count'] ?? 0); ?></span>
                                </p>
                                <p class="excerpt">
                                    <?php
                                    $excerpt = strip_tags($post['content']);
                                    echo htmlspecialchars(mb_strlen($excerpt) > 140 ? mb_substr($excerpt, 0, 140) . '...' : $excerpt);
                                    ?>
                                </p>
                                <a href="post.php?id=<?php echo (int)$post['id']; ?>" class="read-more-btn">
                                    Lire plus <i class="fas fa-angle-right"></i>
                                </a>

                                <?php if ($isAdmin): ?>
                                    <div style="margin-top:15px;border-top:1px solid #eee;padding-top:10px;">
                                        <a href="backoffice/post_edit.php?id=<?php echo (int)$post['id']; ?>"
                                           style="color:#3498db;margin-right:15px;text-decoration:none;">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <a href="backoffice/post_delete.php?id=<?php echo (int)$post['id']; ?>"
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
