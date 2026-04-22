<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Like.php';

class LikeController {
    private $db;
    public $like;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->like = new Like($this->db);
    }

    /**
     * Handle POST from a "like" button.
     *
     * Expects: target_type ('post'|'comment'), target_id (int), redirect (string)
     * Always redirects back to $redirect (or posts_list.php as fallback).
     */
    public function handleToggle() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: auth.php');
            exit;
        }

        $target_type = $_POST['target_type'] ?? '';
        $target_id = (int)($_POST['target_id'] ?? 0);
        $redirect = $_POST['redirect'] ?? 'posts_list.php';

        // Safety: only allow same-app redirects (no absolute URLs)
        if (preg_match('#^(https?:)?//#i', $redirect)) {
            $redirect = 'posts_list.php';
        }

        if (!in_array($target_type, ['post', 'comment'], true) || $target_id <= 0) {
            $_SESSION['error'] = 'Cible invalide pour le like.';
            header('Location: ' . $redirect);
            exit;
        }

        $this->like->toggle((int)$_SESSION['user_id'], $target_type, $target_id);

        header('Location: ' . $redirect);
        exit;
    }
}
