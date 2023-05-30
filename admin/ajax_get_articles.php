<?php

include '../prelude.php';

$published = isset($_POST['published']) ? $_POST['published'] : null;
$approved = isset($_POST['approved']) ? $_POST['approved'] : null;
$removed = isset($_POST['removed']) ? $_POST['removed'] : null;

$articles = Article::search_articles_exact($_POST['articleSearch'], $published, $approved, $removed);

if (!empty($articles)) {
    echo json_encode(
        array_map(function ($article) {
            return array(
                'articleId' => $article->articleId,
                'title' => $article->title,
                'date' => $article->date,
                'category' => $article->category,
                'thumbnail' => $article->thumbnail,
                'readTime' => $article->readTime,
                'author' => $article->get_author_name(),
                'debug' => var_export($_POST, true)
            );
        }, $articles)
    );
} else {
    http_response_code(404);
}