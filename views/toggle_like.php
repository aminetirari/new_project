<?php
session_start();
require_once __DIR__ . '/../controllers/LikeController.php';

$controller = new LikeController();
$controller->handleToggle();
