<?php

include '../prelude.php';

// authorize request
if (empty($_SESSION['username']) || !User::from_username($_SESSION['username'])->is_admin()) {
    http_response_code(401);
}

$article = Article::from_articleId($_POST['articleId']);
$article->published = false;
$article->approved = false;
$article->removed = true;

if ($article->update_article()) {
    http_response_code(200);
} else {
    http_response_code(400);
}