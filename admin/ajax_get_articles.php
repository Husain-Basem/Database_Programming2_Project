<?php

include '../prelude.php';

// authorize request
if (empty($_SESSION['username']) || !User::from_username($_SESSION['username'])->is_admin()) {
    http_response_code(401);
}

$search = !empty($_POST['search']) ? $_POST['search'] : null;
$published = isset($_POST['published']) ? $_POST['published'] : null;
$approved = isset($_POST['approved']) ? $_POST['approved'] : null;
$removed = isset($_POST['removed']) ? $_POST['removed'] : null;

$pagination = Article::search_articles_exact($search, $published, $approved, $removed);
$articles = $pagination->get_page((int) $_POST['page']);
$articles = array_map(function ($row) {
    return Article::__set_state($row);
}, $articles);

if (!empty($articles)) {
    echo json_encode(
        array(
            'totalEntries' => $pagination->get_total_entries(),
            'totalPages' => $pagination->get_total_pages(),
            'articles' =>
            array_map(function ($article) {
                return array(
                    'articleId' => $article->articleId,
                    'title' => $article->title,
                    'date' => $article->date,
                    'category' => $article->category,
                    'thumbnail' => $article->thumbnail,
                    'readTime' => $article->readTime,
                    'author' => $article->get_author_name(),
                    'published' => $article->published,
                    'approved' => $article->approved,
                    'removed' => $article->removed,
                );
            }, $articles)
        )
    );
} else {
    http_response_code(404);
}