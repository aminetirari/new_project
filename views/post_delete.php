<?php
session_start();
require_once __DIR__ . '/../controllers/PostController.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: posts_list.php');
    exit;
}

$controller = new PostController();
$controller->delete((int)$_GET['id']);
