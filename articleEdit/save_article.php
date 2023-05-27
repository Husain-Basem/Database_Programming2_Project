<?php

include_once '../prelude.php';

$article = Article::from_articleId($_POST['articleId']);

if (isset($_POST['content']))
    $article->content = $_POST['content'];
if (isset($_POST['title']))
    $article->title = $_POST['title'];
if (isset($_POST['category']))
    $article->category = $_POST['category'];
if (isset($_POST['thumbnail']) && !empty($_POST['thumbnail']))
    $article->thumbnail = $_POST['thumbnail'];
else 
    $article->thumbnail = null;

if ($article->update_article())
    http_response_code(200);
else
    http_response_code(400);
