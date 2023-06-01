<?php

// Start a new PHP session
// Already done in prelude.php
// session_start();

// Include the configuration file
include_once '../prelude.php';

$db = Database::getInstance();

// Check for errors
if ($db->mysqli->connect_errno) {
    die("Connection failed: " . $db->mysqli->connect_error);
}
// Retrieve the form data
$article_id = $_POST['articleId'];
$reviewBy = $_POST['reviewBy'];
$comment = $_POST['comment'];

// Insert the comment data into the database
$sql = "INSERT INTO Comments (articleId, reviewBy, comment, date) VALUES (?, ?, ?, ?)";
$success = $db->pquery($sql, "isss", $article_id, $reviewBy, $comment, date('Y-m-d\TH:i:s'));

// Check for errors
if (!$success) {
    die("Error adding comment: " . mysqli_error($db->mysqli));
}

// Redirect the user back to the article page
header('Location: ' . BASE_URL . "/displayNews/article.php?id=$article_id#comments");
