<?php

include '../prelude.php';

// authorize request
if (empty($_SESSION['username']) || !User::from_username($_SESSION['username'])->is_admin()) {
    http_response_code(401);
}

switch ($_POST['userSearchBy']) {
    case 'userName':
        $user = User::from_username($_POST['userSearch']);
        break;
    case 'userId':
        $user = User::from_userId($_POST['userSearch']);
        break;
    case 'email':
        $user = User::from_email($_POST['userSearch']);
        break;
}

if ($user != null) {
    if ($user->is_author() || $user->is_admin()) {
        echo json_encode(
            array(
                'published' => Article::get_author_articles($user->userId, false),
                'unpublished' => Article::get_author_articles($user->userId, true)
            )
        );
    } else {
        http_response_code(400);
        echo 'notauthor';
    }
} else {
    http_response_code(404);
    echo 'notfound';
}