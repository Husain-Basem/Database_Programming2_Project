<?php
// Start a new PHP session
session_start();

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
$sql = "INSERT INTO comments (articleId, reviewBy, comment) VALUES (?, ?, ?)";
$success = $db->pquery($sql, "iss", $Yes);

// Check for errors
if (!$result) {
    die("Error adding comment: " . mysqli_error($conn));
}

// Redirect the user back to the article page
header("Location: article.php?id=$article_id");
?>

