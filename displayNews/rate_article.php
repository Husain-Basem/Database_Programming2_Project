<?php

include_once '../prelude.php';

if (isset($_SESSION['userId']))
    $userId = $_SESSION['userId'];
else
    $userId = null;


$rating = new Rating($_POST['articleId'], (bool) $_POST['like'], $userId);

if ($rating->upsert_rating()) {
    http_response_code(200);
} else {
    http_response_code(400);
}

echo json_encode(Rating::get_article_ratings($_POST['articleId']));