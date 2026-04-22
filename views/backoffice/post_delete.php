<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: posts_list.php');
    exit;
}

require_once __DIR__ . '/../../controllers/PostController.php';

$postController = new PostController();
$postController->delete((int)$_GET['id']);
