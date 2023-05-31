<?php

include '../prelude.php';

$article = Article::from_articleId($_POST['articleId']);
$article->published = false;
$article->approved = false;
$article->removed = true;

if ($article->update_article()) {
    http_response_code(200);
} else {
    http_response_code(400);
}