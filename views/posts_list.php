<?php
session_start();
require_once '../controllers/PostController.php';

$postController = new PostController();
$posts = $postController->post->getAll();
?>
<?php include 'header.php'; ?>

<!-- breadcrumb-section -->
<div class="breadcrumb-section breadcrumb-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p>Read our latest news</p>
                    <h1>Blog Posts</h1>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end breadcrumb section -->

<!-- blog section -->
<div class="mt-150 mb-150">
    <div class="container">
        <div class="row">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-latest-news">
                            <a href="post.php?id=<?php echo $post['id']; ?>">
                                <div class="latest-news-bg">
                                    <img src="assets/img/blog-img-1.jpg" alt="Post Image">
                                </div>
                            </a>
                            <div class="news-text-box">
                                <h3><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                                <p class="blog-meta">
                                    <span class="author"><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author_name']); ?></span>
                                    <span class="date"><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                                </p>
                                <p class="excerpt"><?php echo substr(htmlspecialchars($post['content']), 0, 100) . '...'; ?></p>
                                <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more-btn">Read More <i class="fas fa-angle-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-lg-12 text-center">
                    <p>No posts available yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- end blog section -->

<?php include 'footer.php'; ?>