<?php
session_start();
require_once '../controllers/PostController.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$postController = new PostController();
$postController->show($_GET['id']);
?>
<?php include 'header.php'; ?>

<!-- breadcrumb-section -->
<div class="breadcrumb-section breadcrumb-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p>Read our latest news</p>
                    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end breadcrumb section -->

<!-- single article section -->
<div class="mt-150 mb-150">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="single-article-section">
                    <div class="single-article-text">
                        <div class="single-artcile-bg">
                            <img src="assets/img/blog-img-1.jpg" alt="Post Image">
                        </div>
                        <p class="blog-meta">
                            <span class="author"><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author_name']); ?></span>
                            <span class="date"><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                        </p>
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="comments-section">
                    <h3>Comments (<?php echo count($comments); ?>)</h3>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <div class="comment-author">
                                    <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong>
                                    <span class="comment-date"><?php echo date('F j, Y H:i', strtotime($comment['created_at'])); ?></span>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No comments yet. Be the first to comment!</p>
                    <?php endif; ?>

                    <!-- Comment Form -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="comment-form">
                            <h4>Leave a Comment</h4>
                            <form action="add_comment.php" method="POST">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <div class="form-group">
                                    <textarea name="content" class="form-control" rows="4" placeholder="Write your comment here..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Comment</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p><a href="auth.php">Login</a> to leave a comment.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sidebar-section">
                    <div class="recent-posts">
                        <h4>Recent Posts</h4>
                        <ul>
                            <li><a href="#">Sample Post 1</a></li>
                            <li><a href="#">Sample Post 2</a></li>
                            <li><a href="#">Sample Post 3</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end single article section -->

<?php include 'footer.php'; ?>