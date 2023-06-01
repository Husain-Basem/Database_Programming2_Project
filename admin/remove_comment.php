<?php

include '../prelude.php';

// authorize request
if (empty($_SESSION['username']) || !User::from_username($_SESSION['username'])->is_admin()) {
    http_response_code(401);
}

$comment = Comment::from_commentId($_POST['commentId']);
$comment->removed = true;

if ($comment->update_comment()) {
    $_SESSION['toasts'][] = array('type' => 'success', 'msg' => 'Successfully removed comment');
} else {
    $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Could not remove comment');
}

header('Location: ' . BASE_URL . '/displayNews/article.php?id=' . $comment->articleId . '#comments');