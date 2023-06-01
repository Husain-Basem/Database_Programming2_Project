<?php
include_once '../prelude.php';

// authorize request
if (empty($_SESSION['username']) || !User::from_username($_SESSION['username'])->is_admin()) {
    http_response_code(401);
}

$article = Article::from_articleId($_GET['articleId']);
$article->published = true;
$article->approved = true;
$article->removed = false;
$success = $article->update_article();

if ($success) {
    $_SESSION['toasts'][] = array('type' => 'success', 'msg' => 'Approved Article "' . $article->title . '"');
    session_write_close();
    header('Location: ' . BASE_URL . '/admin/admin_panel.php#pending-articles-tab');
} else {
    $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Could not approve article');
    session_write_close();
    header('Location: ' . BASE_URL . '/articleEdit/preview.php?articleId=' . $article->articleId);
}