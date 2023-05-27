<?php

include_once '../prelude.php';

$article = new Article(null, 'Untitled Article', '', 0, $_SESSION['userId'], '', 'local', false, null);

$articleId = $article->insert_article();

if ($articleId != null)
    header('Location: ' . BASE_URL . '/articleEdit/edit_article.php?articleId=' . $articleId);
else
    echo 'Could not create new article';