<?php

include '../prelude.php';

if (Article::delete_article($_POST['articleId'])) {
    $_SESSION['toasts'][] = array('type' => 'success', 'msg' => 'Successfully deleted article');
} else {
    $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Could not delete article');
}

session_write_close();
header('Location: ' . BASE_URL . '/articleEdit/author_panel.php');