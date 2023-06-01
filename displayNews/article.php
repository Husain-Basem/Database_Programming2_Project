<?php
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

$article = Article::from_articleId($article_id);
if (!$article) {
    die("Error retrieving article data: " . $db->mysqli->error);
}


// Query the database to retrieve the comments data
$sql = "SELECT Comments.*, Users.userName 
        FROM Comments JOIN Users on Comments.reviewBy = Users.userId
        WHERE articleId = $article_id
        ORDER BY date DESC";

$comments = new Pagination(5, $sql);

// use the website header
$pageTitle = $article->title;
$headerIncludes = '<link rel="stylesheet" href="' . BASE_URL . '/css/quill.snow.css" />';
include PROJECT_ROOT . '/header.html';
?>


<div class="container">
    <h1>
        <?= $article->title ?>
    </h1>
    <div>readtime:
        <?= $article->readTime ?> min
    </div>

    <div class="ql-container">
        <div class="ql-snow clearfix">
            <div class="ql-editor">
                <?= $article->content ?>
            </div>
        </div>
    </div>

    <?php if (!empty(File::get_files($article_id, true))) { ?>
        <hr>
        <h2>Attachments</h2>
        <div id="attachments" class="list-group mb-5 col-12 col-md-6 mx-auto">
            <?php
            $attachments = File::get_files($article->articleId, true);
            foreach ($attachments as $a) {
                echo '
                <div class="list-group-item d-flex align-items-center">
                  <p class="m-0">' . $a->fileName . '</p>
                  <small class="ms-3 text-muted">' . $a->fileSize . '</small>
                  <div class="ms-auto btn-group" role="group" aria-label="Attachment Actions">
                    <a class="upload-btn btn btn-outline-primary" href="' . $a->get_url() . '" downlod="' . $a->fileName . '">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download"
                        viewBox="0 0 16 16">
                        <path
                          d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                        <path
                          d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                      </svg>
                    </a>
                  </div>
                </div>
                ';
            }
            ?>
        </div>
    <?php } ?>
    <hr>

    <div class="row mb-5">
        <div class="col-md-6 mb-5">
            <h2 id="comments">Comments</h2>

            <?php if ($comments->get_total_entries() == 0) { ?>
                <p>No comments yet.</p>
            <?php } else { ?>
                <?php $page = isset($_GET['p']) ? $_GET['p'] : 1; ?>
                <?= $comments->pagination_controls($page, $_SERVER['QUERY_STRING'], 'comments') ?>
                <ul class="list-group">
                    <?php foreach ($comments->get_page($page) as $comment) { ?>
                        <li class="list-group-item">
                            <p>
                                <strong>
                                    <?= '@' . $comment['userName']; ?>
                                </strong> on
                                <?= date('F j, Y g:i a', strtotime($comment['date'])); ?>:
                            </p>
                            <p>
                                <?= $comment['comment']; ?>
                            </p>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
        <div class="col-md-6">
            <h2>Add a Comment</h2>

            <?php if (isset($_SESSION['username'])) { ?>
                <form action="add_comment.php" method="POST">
                    <input type="hidden" name="articleId" value="<?php echo $article_id; ?>">
                    <input type="hidden" name="reviewBy" id="reviewBy" value="<?php echo $_SESSION['userId']; ?>">
                    <div class="mb-3">
                        <label for="name">Username:</label>
                        <input class="form-control" type="text" name="name" id="name"
                            value="<?= $_SESSION['username']; ?>" disabled readonly>
                    </div>
                    <div class="mb-3">
                        <label for="comment">Comment:</label>
                        <textarea class="form-control" name="comment" id="comment" rows="5" required></textarea>
                    </div>
                    <input class="btn btn-primary" type="submit" value="Submit">
                </form>
            <?php } else { ?>
                <p>You must be logged in to leave a comment. <a
                        href="<?= BASE_URL . '/user/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']) ?>">Click
                        here
                        to log
                        in</a>.</p>
            <?php } ?>
        </div>
    </div>

</div>

<script>
    $(() => {
        function formatSize(size) {
            if (size < 1000) return size + ' Bytes';
            else if (size < 1_000_000) return (size / 1000).toFixed(2) + ' kB';
            else return (size / 1_000_000).toFixed(2) + ' MB';
        }

        $.each($('#attachments small'), function () {
            $(this).text(formatSize($(this).text()));
        });
    });
</script>

<?php include PROJECT_ROOT . '/footer.html' ?>