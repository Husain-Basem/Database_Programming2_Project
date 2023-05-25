<?php

include '../prelude.php';

$article = Article::from_articleId($_POST['articleId']);
$article->published = true;

if ($article->update_article()) {
    $_SESSION['toasts'][] = array('type' => 'success', 'msg' => 'Successfully published article');
} else {
    $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Could not publish article');
}

session_write_close();
header('Location: ' . BASE_URL . '/articleEdit/author_panel.php');