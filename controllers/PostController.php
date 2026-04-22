<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';

class PostController {
    private $db;
    private $post;
    private $comment;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->post = new Post($this->db);
        $this->comment = new Comment($this->db);
    }

    /**
     * Display a single post with comments
     */
    public function show($id) {
        $post = $this->post->getById($id);
        if (!$post) {
            header('Location: index.php');
            exit;
        }

        $comments = $this->comment->getByPostId($id);

        // Include the view
        include __DIR__ . '/../views/post.php';
    }

    /**
     * Add a comment to a post
     */
    public function addComment($post_id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: auth.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content'] ?? '');

            if (empty($content)) {
                $_SESSION['error'] = 'Comment cannot be empty';
                header('Location: post.php?id=' . $post_id);
                exit;
            }

            $this->comment->post_id = $post_id;
            $this->comment->user_id = $_SESSION['user_id'];
            $this->comment->content = $content;

            if ($this->comment->create()) {
                $_SESSION['success'] = 'Comment added successfully';
            } else {
                $_SESSION['error'] = 'Failed to add comment';
            }
        }

        header('Location: post.php?id=' . $post_id);
        exit;
    }

    /**
     * List all posts
     */
    public function index() {
        $posts = $this->post->getAll();
        include __DIR__ . '/../views/posts_list.php';
    }

    /**
     * Create a new post (for admin/backoffice)
     */
    public function create() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');

            if (empty($title) || empty($content)) {
                $_SESSION['error'] = 'Title and content are required';
                include __DIR__ . '/../views/post_create.php';
                return;
            }

            $this->post->title = $title;
            $this->post->content = $content;
            $this->post->author_id = $_SESSION['user_id'];

            if ($this->post->create()) {
                $_SESSION['success'] = 'Post created successfully';
                header('Location: posts_list.php');
                exit;
            } else {
                $_SESSION['error'] = 'Failed to create post';
            }
        }

        include __DIR__ . '/../views/post_create.php';
    }
}