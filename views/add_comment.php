<?php
session_start();
require_once '../controllers/PostController.php';

if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
    header('Location: index.php');
    exit;
}

$postController = new PostController();
$postController->addComment($_POST['post_id']);
?>