<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Like.php';

class PostController {
    private $db;
    public $post;
    public $comment;
    public $like;

    /** Absolute filesystem path to the uploads folder. */
    private $uploadDirAbs;
    /** Relative (web) path from views/ to the uploads folder. */
    private $uploadDirWeb = 'uploads/posts/';

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->post = new Post($this->db);
        $this->comment = new Comment($this->db);
        $this->like = new Like($this->db);

        $this->uploadDirAbs = __DIR__ . '/../views/uploads/posts/';
        if (!is_dir($this->uploadDirAbs)) {
            @mkdir($this->uploadDirAbs, 0755, true);
        }
    }

    /**
     * Load a single post + its comments. Also enriches each row with like_count
     * and liked_by_me so the view can render the heart icons without extra queries.
     * Returns ['post' => ..., 'comments' => [...]] or null if not found.
     */
    public function loadPost($id) {
        $post = $this->post->getById($id);
        if (!$post) {
            return null;
        }
        $comments = $this->comment->getByPostId($id);
        $current_uid = (int)($_SESSION['user_id'] ?? 0);

        $post['like_count'] = $this->like->countFor('post', $post['id']);
        $post['liked_by_me'] = $this->like->hasLiked($current_uid, 'post', $post['id']);

        $comment_ids = array_map(static fn($c) => (int)$c['id'], $comments);
        $liked_map = $this->like->likedMapFor($current_uid, 'comment', $comment_ids);
        foreach ($comments as &$c) {
            $c['like_count'] = $this->like->countFor('comment', $c['id']);
            $c['liked_by_me'] = $liked_map[(int)$c['id']] ?? false;
        }
        unset($c);

        return ['post' => $post, 'comments' => $comments];
    }

    /**
     * Return all posts with comment_count and like_count columns + liked_by_me.
     */
    public function getAllPosts() {
        $rows = $this->post->getAllWithCounts();
        $current_uid = (int)($_SESSION['user_id'] ?? 0);
        $ids = array_map(static fn($r) => (int)$r['id'], $rows);
        $liked_map = $this->like->likedMapFor($current_uid, 'post', $ids);
        foreach ($rows as &$r) {
            $r['liked_by_me'] = $liked_map[(int)$r['id']] ?? false;
        }
        unset($r);
        return $rows;
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
     * Also removes any likes on that comment (polymorphic table).
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
            $this->like->deleteAllFor('comment', (int)$comment_id);
            $_SESSION['success'] = 'Commentaire supprimé.';
        } else {
            $_SESSION['error'] = 'Impossible de supprimer le commentaire.';
        }

        header('Location: post.php?id=' . (int)$existing['post_id']);
        exit;
    }

    /**
     * Create a post from the front-office. Any logged-in user is allowed.
     * Accepts an optional image file (input name="image", <= 5 MB, jpeg/png/gif/webp).
     */
    public function createFront() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: auth.php');
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

        $image_rel = null;
        if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $up = $this->handleImageUpload($_FILES['image']);
            if (isset($up['error'])) {
                $errors[] = $up['error'];
            } else {
                $image_rel = $up['path'];
            }
        }

        if (!empty($errors)) {
            return ['title' => $title, 'content' => $content, 'errors' => $errors];
        }

        $this->post->title = $title;
        $this->post->content = $content;
        $this->post->image_path = $image_rel;
        $this->post->author_id = (int)$_SESSION['user_id'];

        if ($this->post->create()) {
            $_SESSION['success'] = 'Post publié avec succès.';
            header('Location: post.php?id=' . (int)$this->post->id);
            exit;
        }

        // Cleanup uploaded file if DB insert failed
        if ($image_rel) {
            @unlink($this->uploadDirAbs . basename($image_rel));
        }
        return ['title' => $title, 'content' => $content, 'errors' => ['Impossible de créer le post.']];
    }

    /**
     * Admin back-office post creation (same fields + image).
     */
    public function create() {
        if (!$this->isAdmin()) {
            header('Location: ../auth.php');
            exit;
        }
        return $this->createFront();
    }

    /**
     * Update an existing post. Author OR admin may update. Optional new image.
     * $redirect_on_success is the URL to redirect to when the update succeeds.
     */
    public function update($id, $redirect_on_success = null) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: auth.php');
            exit;
        }

        $existing = $this->post->getById($id);
        if (!$existing) {
            $_SESSION['error'] = 'Post introuvable.';
            header('Location: posts_list.php');
            exit;
        }

        $isAuthor = ((int)$existing['author_id'] === (int)$_SESSION['user_id']);
        if (!$isAuthor && !$this->isAdmin()) {
            $_SESSION['error'] = 'Vous n\'avez pas le droit de modifier ce post.';
            header('Location: post.php?id=' . (int)$id);
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

        $new_image_rel = null;
        $touch_image = false;
        if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $up = $this->handleImageUpload($_FILES['image']);
            if (isset($up['error'])) {
                $errors[] = $up['error'];
            } else {
                $new_image_rel = $up['path'];
                $touch_image = true;
            }
        } elseif (!empty($_POST['remove_image'])) {
            $new_image_rel = null;
            $touch_image = true;
        }

        if (!empty($errors)) {
            $existing['title'] = $title;
            $existing['content'] = $content;
            return ['post' => $existing, 'errors' => $errors];
        }

        $this->post->id = (int)$id;
        $this->post->title = $title;
        $this->post->content = $content;
        if ($touch_image) {
            $this->post->image_path = $new_image_rel;
        }

        if ($this->post->update($touch_image)) {
            // If we replaced/cleared the image, delete the old file
            if ($touch_image && !empty($existing['image_path'])) {
                @unlink($this->uploadDirAbs . basename($existing['image_path']));
            }
            $_SESSION['success'] = 'Post mis à jour.';
            $target = $redirect_on_success ?: 'post.php?id=' . (int)$id;
            header('Location: ' . $target);
            exit;
        }

        return ['post' => $existing, 'errors' => ['Impossible de mettre à jour le post.']];
    }

    /**
     * Delete a post. Author OR admin. Comments are removed via FK cascade;
     * likes (post & comment) are cleaned up explicitly since the likes table
     * only FKs on user.
     */
    public function delete($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: auth.php');
            exit;
        }

        $existing = $this->post->getById($id);
        if (!$existing) {
            $_SESSION['error'] = 'Post introuvable.';
            header('Location: posts_list.php');
            exit;
        }

        $isAuthor = ((int)$existing['author_id'] === (int)$_SESSION['user_id']);
        if (!$isAuthor && !$this->isAdmin()) {
            $_SESSION['error'] = 'Vous n\'avez pas le droit de supprimer ce post.';
            header('Location: post.php?id=' . (int)$id);
            exit;
        }

        // Fetch comment ids first so we can clean their likes too
        $comments = $this->comment->getByPostId((int)$id);

        $this->post->id = (int)$id;
        if ($this->post->delete()) {
            $this->like->deleteAllFor('post', (int)$id);
            foreach ($comments as $c) {
                $this->like->deleteAllFor('comment', (int)$c['id']);
            }
            if (!empty($existing['image_path'])) {
                @unlink($this->uploadDirAbs . basename($existing['image_path']));
            }
            $_SESSION['success'] = 'Post supprimé.';
        } else {
            $_SESSION['error'] = 'Impossible de supprimer le post.';
        }

        header('Location: posts_list.php');
        exit;
    }

    /**
     * Validate & move an uploaded image into views/uploads/posts/.
     * Returns ['path' => 'uploads/posts/abc.jpg'] on success or ['error' => 'msg'].
     */
    private function handleImageUpload(array $file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Échec du téléversement de l\'image.'];
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['error' => 'L\'image dépasse 5 Mo.'];
        }

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];
        $mime = null;
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
        }
        if (!$mime || !isset($allowed[$mime])) {
            return ['error' => 'Type d\'image non autorisé (JPG, PNG, GIF, WEBP).'];
        }

        $ext = $allowed[$mime];
        $basename = bin2hex(random_bytes(8)) . '.' . $ext;
        $destAbs = $this->uploadDirAbs . $basename;
        if (!move_uploaded_file($file['tmp_name'], $destAbs)) {
            return ['error' => 'Impossible de sauvegarder l\'image sur le serveur.'];
        }

        return ['path' => $this->uploadDirWeb . $basename];
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
