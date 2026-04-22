<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';

class PostController {
    private $db;
    public $post;
    public $comment;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->post = new Post($this->db);
        $this->comment = new Comment($this->db);
    }

    /**
     * Load a single post with its comments.
     * Returns ['post' => ..., 'comments' => [...]] or null if not found.
     */
    public function loadPost($id) {
        $post = $this->post->getById($id);
        if (!$post) {
            return null;
        }
        $comments = $this->comment->getByPostId($id);
        return ['post' => $post, 'comments' => $comments];
    }

    /**
     * Return all posts with a comment_count column.
     */
    public function getAllPosts() {
        return $this->post->getAllWithCommentCount();
    }

    /**
     * Handle POST from the comment form. Always redirects back to the post.
     */
    public function addComment($post_id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: auth.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content'] ?? '');

            if ($content === '') {
                $_SESSION['error'] = 'Le commentaire ne peut pas être vide.';
                header('Location: post.php?id=' . (int)$post_id);
                exit;
            }

            $this->comment->post_id = (int)$post_id;
            $this->comment->user_id = (int)$_SESSION['user_id'];
            $this->comment->content = $content;

            if ($this->comment->create()) {
                $_SESSION['success'] = 'Commentaire ajouté avec succès.';
            } else {
                $_SESSION['error'] = 'Impossible d\'ajouter le commentaire.';
            }
        }

        header('Location: post.php?id=' . (int)$post_id);
        exit;
    }

    /**
     * Delete a comment. Only the comment author or an admin may delete.
     */
    public function deleteComment($comment_id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: auth.php');
            exit;
        }

        $existing = $this->comment->getById($comment_id);
        if (!$existing) {
            $_SESSION['error'] = 'Commentaire introuvable.';
            header('Location: posts_list.php');
            exit;
        }

        $isOwner = ((int)$existing['user_id'] === (int)$_SESSION['user_id']);
        $isAdmin = (($_SESSION['user_role'] ?? '') === 'admin');
        if (!$isOwner && !$isAdmin) {
            $_SESSION['error'] = 'Vous n\'avez pas le droit de supprimer ce commentaire.';
            header('Location: post.php?id=' . (int)$existing['post_id']);
            exit;
        }

        $this->comment->id = (int)$comment_id;
        if ($this->comment->delete()) {
            $_SESSION['success'] = 'Commentaire supprimé.';
        } else {
            $_SESSION['error'] = 'Impossible de supprimer le commentaire.';
        }

        header('Location: post.php?id=' . (int)$existing['post_id']);
        exit;
    }

    /**
     * Create a new post. Admin-only (checked at the caller view level too).
     */
    public function create() {
        if (!$this->isAdmin()) {
            header('Location: ../auth.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['title' => '', 'content' => '', 'errors' => []];
        }

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $errors = [];
        if ($title === '') { $errors[] = 'Le titre est requis.'; }
        if ($content === '') { $errors[] = 'Le contenu est requis.'; }

        if (!empty($errors)) {
            return ['title' => $title, 'content' => $content, 'errors' => $errors];
        }

        $this->post->title = $title;
        $this->post->content = $content;
        $this->post->author_id = (int)$_SESSION['user_id'];

        if ($this->post->create()) {
            $_SESSION['success'] = 'Post créé avec succès.';
            header('Location: posts_list.php');
            exit;
        }

        return ['title' => $title, 'content' => $content, 'errors' => ['Impossible de créer le post.']];
    }

    /**
     * Update an existing post. Admin-only.
     */
    public function update($id) {
        if (!$this->isAdmin()) {
            header('Location: ../auth.php');
            exit;
        }

        $existing = $this->post->getById($id);
        if (!$existing) {
            $_SESSION['error'] = 'Post introuvable.';
            header('Location: posts_list.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['post' => $existing, 'errors' => []];
        }

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $errors = [];
        if ($title === '') { $errors[] = 'Le titre est requis.'; }
        if ($content === '') { $errors[] = 'Le contenu est requis.'; }

        if (!empty($errors)) {
            $existing['title'] = $title;
            $existing['content'] = $content;
            return ['post' => $existing, 'errors' => $errors];
        }

        $this->post->id = (int)$id;
        $this->post->title = $title;
        $this->post->content = $content;

        if ($this->post->update()) {
            $_SESSION['success'] = 'Post mis à jour.';
            header('Location: posts_list.php');
            exit;
        }

        return ['post' => $existing, 'errors' => ['Impossible de mettre à jour le post.']];
    }

    /**
     * Delete a post. Admin-only. Comments are removed via FK cascade.
     */
    public function delete($id) {
        if (!$this->isAdmin()) {
            header('Location: ../auth.php');
            exit;
        }

        $this->post->id = (int)$id;
        if ($this->post->delete()) {
            $_SESSION['success'] = 'Post supprimé.';
        } else {
            $_SESSION['error'] = 'Impossible de supprimer le post.';
        }

        header('Location: posts_list.php');
        exit;
    }

    /**
     * Helper: current session user is admin.
     */
    private function isAdmin() {
        return isset($_SESSION['logged_in'])
            && $_SESSION['logged_in'] === true
            && ($_SESSION['user_role'] ?? '') === 'admin';
    }
}
