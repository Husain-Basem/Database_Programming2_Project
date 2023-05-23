<?php
// Start a new PHP session
session_start();

include_once '../prelude.php';

// Connect to the database using the Database class
$db = Database::getInstance();

// Check for errors
if ($db->mysqli->connect_errno) {
    die("Connection failed: " . $db->mysqli->connect_error);
}


// Retrieve the article ID from the URL
$article_id = $_GET['id'];

// Query the database to retrieve the article data
$sql = "SELECT * FROM articles WHERE id = $article_id";
$result = $db->query($sql);
if (!$result) {
    die("Error retrieving article data: " . $db->mysqli->error);
}

// Check for errors
if (!$result) {
    die("Error retrieving article data: " . $db->mysqli->error);
}

// Retrieve the article data
$article = $result->fetch_assoc();

// Query the database to retrieve the comments data
$sql = "SELECT * FROM comments WHERE articleId = $article_id ORDER BY created_at DESC";
$result = $db->query($sql);

// Check for errors
if (!$result) {
    die("Error retrieving comments data:" . $db->mysqli->error);
}

// Retrieve the comments data
$comments = $result->fetch_all(MYSQLI_ASSOC);
?>

<html>
<head>
    <title><?php echo $article['title']; ?></title>
</head>
<body>
    <h1><?php echo $article['title']; ?></h1>
    <p><?php echo $article['content']; ?></p>
    <?php if ($article['image']) { ?>
        <img src="<?php echo $article['image']; ?>" alt="<?php echo $article['title']; ?>">
    <?php } ?>
    <?php if ($article['audio']) { ?>
        <audio controls>
            <source src="<?php echo $article['audio']; ?>" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    <?php } ?>
    <?php if ($article['video']) { ?>
        <video controls>
            <source src="<?php echo $article['video']; ?>" type="video/mp4">
            Your browser does not support the video element.
        </video>
    <?php } ?>

    <hr>

    <h2>Comments</h2>

    <?php if (count($comments) == 0) { ?>
        <p>No comments yet.</p>
    <?php } else { ?>
        <ul>
            <?php foreach ($comments as $comment) { ?>
                <li>
                    <p><strong><?php echo $comment['name']; ?></strong> on <?php echo date('F j, Y g:i a', strtotime($comment['created_at'])); ?>:</p>
                    <p><?php echo $comment['comment']; ?></p>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>

    <hr>

    <h2>Add a Comment</h2>

    <?php if (isset($_SESSION['username'])) { ?>
        <form action="add_comment.php" method="POST">
            <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo $_SESSION['username']; ?>" readonly>
            <br>
            <label for="comment">Comment:</label>
            <textarea name="comment" id="comment" rows="5" required></textarea>
            <br>
            <input type="submit" value="Submit">
        </form>
    <?php } else { ?>
        <p>You must be logged in to leave a comment. <a href="login.php">Click here to log in</a>.</p>
    <?php } ?>
</body>
</html>


