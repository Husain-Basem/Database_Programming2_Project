<?php
// Start a new PHP session
// Already done in prelude.php
// session_start();

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
$sql = "SELECT * FROM Articles WHERE articleId = $article_id ";
$result = $db->query($sql);
if (!$result) {
    die("Error retrieving article data: " . $db->mysqli->error);
}

// Retrieve the article data
$article = $result->fetch_assoc();

// Query the database to retrieve the comments data
$sql = "SELECT Comments.*, Users.userName 
        FROM Comments JOIN Users on Comments.reviewBy = Users.userId
        WHERE articleId = $article_id
        ORDER BY date DESC;";
$result = $db->query($sql);

// Check for errors
if (!$result) {
    die("Error retrieving comments data:" . $db->mysqli->error);
}

// Retrieve the comments data
$comments = $result->fetch_all(MYSQLI_ASSOC);

// use the website header
$pageTitle = $article['title'];
include PROJECT_ROOT . '/header.html';
?>

<div class="container">
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
                    <p><strong><?php echo $comment['userName']; ?></strong> on <?php echo date('F j, Y g:i a', strtotime($comment['date'])); ?>:</p>
                    <p><?php echo $comment['comment']; ?></p>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>

    <hr>

    <h2>Add a Comment</h2>

    <?php if (isset($_SESSION['username'])) { ?>
        <form action="add_comment.php" method="POST">
            <input type="hidden" name="articleId" value="<?php echo $article_id; ?>">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo $_SESSION['username']; ?>" readonly>
            <input type="hidden" name="reviewBy" id="reviewBy" value="<?php echo $_SESSION['userId']; ?>">
            <br>
            <label for="comment">Comment:</label>
            <textarea name="comment" id="comment" rows="5" required></textarea>
            <br>
            <input type="submit" value="Submit">
        </form>
    <?php } else { ?>
    <p>You must be logged in to leave a comment. <a href="<?= BASE_URL . '/user/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']) ?>">Click here to log in</a>.</p>
    <?php } ?>
</div>

<?php include PROJECT_ROOT . '/footer.html' ?>
