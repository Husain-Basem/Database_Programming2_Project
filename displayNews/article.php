<?php
include_once '../prelude.php';

// Connect to the database using the Database class
$db = Database::getInstance();

if (isset($_SESSION['userId']))
    $user = User::from_userId($_SESSION['userId']);

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
$article->get_author_name();

// get all ratings for this article
$ratings = Rating::get_article_ratings($article_id);

// get the current user rating for this article if any
$userRating = Rating::get_user_rating($article_id);
if (isset($userRating)) {
    $userLike = $userRating ? 'checked' : '';
    $userDislike = $userRating ? '' : 'checked';
}

// Query the database to retrieve the comments data
$user_id = isset($user) ? $user->userId : -1;
$sql = "SELECT Comments.*, Users.userName 
        FROM Comments JOIN Users on Comments.reviewBy = Users.userId
        WHERE articleId = $article_id";
// allow admins to see removed comments
if (!(isset($user) && $user->is_admin()))
    $sql .=
        " AND (removed = 0 OR reviewBy = $user_id)";

$sql .=
    ' ORDER BY date DESC';

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
    <div class="d-flex gap-3 align-items-baseline text-muted flex-wrap flex-sm-nowrap">
        <span class="badge rounded-pill text-bg-primary vertical-align-middle">
            <?= $article->display_category() ?>
        </span>
        <span class="ms-auto ms-sm-0">
            Read Time:
            <?= $article->readTime ?> min
        </span>
        <span class="ms-0 ms-sm-auto me-auto me-sm-0">
            By
            <?= $article->author ?>
        </span>
        <span>
            Published on
            <span id="articleDate">
                <?= date('F j, Y', strtotime($article->date)) ?>
            </span>
        </span>
    </div>

    <hr>

    <div class="ql-container">
        <div class="ql-snow clearfix">
            <div class="ql-editor"><?= $article->content ?></div>
        </div>
    </div>

    <?php
    $attachments = File::get_files($article->articleId, true);
    if (!empty(File::get_files($article_id, true))) { ?>
        <hr>
        <h2>Attachments</h2>
        <div id="attachments" class="list-group mb-5 col-12 col-md-6 mx-auto">
            <?php
            foreach ($attachments as $a) {
                echo '
                <div class="list-group-item d-flex align-items-center">
                  <p class="m-0">' . $a->fileName . '</p>
                  <small class="ms-3 text-muted">' . $a->fileSize . '</small>
                  <div class="ms-auto btn-group" role="group" aria-label="Attachment Actions">
                    <a class="upload-btn btn btn-outline-primary" href="' . $a->get_url() . '" downlod="' . $a->fileName . '">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                      </svg>
                    </a>
                  </div>
                </div>
                ';
            } ?>
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
                <div class="d-flex" style="align-items: first baseline">
                    <span class="me-3">Page:</span>
                    <?= $comments->pagination_controls($page, $_SERVER['QUERY_STRING'], 'comments') ?>
                </div>
                <ul class="list-group">
                    <?php foreach ($comments->get_page($page) as $comment) { ?>
                        <li class="list-group-item" data-comment-id="<?= $comment['commentId'] ?>">
                            <?php
                            if ($comment['removed']) {
                                $t = $user->is_admin() ? 'This' : 'Your';
                                echo '<span class="text-danger" title="Other users cannot see this comment anymore">' .
                                    $t . ' comment was removed by an administrator.</span>';
                            }
                            ?>
                            <div class="hstack gap-2 mb-3">
                                <span>
                                    <strong>
                                        <?= '@' . $comment['userName']; ?>
                                    </strong>
                                </span>
                                <span>
                                    on
                                    <?= date('F j, Y g:i a', strtotime($comment['date'])); ?>:
                                </span>
                                <?php if (isset($user) && $user->is_admin()) { ?>
                                    <button class="removeCommentBtn btn btn-sm btn-outline-danger ms-auto"
                                        data-comment-id="<?= $comment['commentId'] ?>" data-bs-toggle="modal"
                                        data-bs-target="#removeCommentModal">Remove</button>
                                <?php } ?>
                            </div>
                            <p class="border-start border-2 ps-2">
                                <?= $comment['comment']; ?>
                            </p>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
        <div class="col-md-6">
            <div class="hstack align-items-center gap-3 mb-3">
                <h2 class="mb-0">Rate this Article</h2>
                <div class="btn-group ms-auto">
                    <input type="radio" class="btn-check" name="like" id="like1" aria-label="Like" value="1"
                        <?= $userLike ?>>
                    <label for="like1" class="btn btn-outline-success" title="Like">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                            <path
                                d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.077.27.113.567.113.856 0 .289-.036.586-.113.856-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.163 3.163 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.82 4.82 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z" />
                        </svg>
                    </label>
                    <input type="radio" class="btn-check" name="like" id="like2" aria-label="Dislike" value="0"
                        <?= $userDislike ?>>
                    <label for="like2" class="btn btn-outline-danger" title="Dislike">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-hand-thumbs-down-fill" viewBox="0 0 16 16">
                            <path
                                d="M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2.094 2.094 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.162 3.162 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.823 4.823 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591z" />
                        </svg>
                    </label>
                </div>
                <span>
                    <span id="likes">
                        <?= $ratings['likes'] ?>
                    </span>
                    Likes
                </span>
                <span class="me-4 me-sm-0">
                    <span id="dislikes">
                        <?= $ratings['dislikes'] ?>
                    </span>
                    Dislikes
                </span>
            </div>
            <h2>Add a Comment</h2>

            <?php if (isset($_SESSION['username'])) { ?>
                <form action="add_comment.php" method="POST">
                    <input type="hidden" name="articleId" value="<?php echo $article_id; ?>">
                    <input type="hidden" name="reviewBy" id="reviewBy" value="<?php echo $_SESSION['userId']; ?>">
                    <div class="mb-3">
                        <label for="name">Username:</label>
                        <input class="form-control" type="text" name="name" id="name" value="<?= $_SESSION['username']; ?>"
                            disabled readonly>
                    </div>
                    <div class="mb-3">
                        <label for="comment">Comment:</label>
                        <textarea class="form-control" name="comment" id="comment" rows="5" required></textarea>
                    </div>
                    <input class="btn btn-primary" type="submit" value="Submit">
                </form>
            <?php } else { ?>
                <p>
                    You must be logged in to leave a comment.
                    <a href="<?= BASE_URL . '/user/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']) ?>">
                        Click here to log in
                    </a>.
                </p>
            <?php } ?>
        </div>
    </div>

</div>


<div class="modal fade" id="removeCommentModal" tabindex="-1" role="dialog" aria-labelledby="removeCommentModalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeCommentModalTitle">Remove Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove this comment?
                <ul class="list-group">
                    <li class="list-group-item" id="removeCommentModalComment"></li>
                </ul>
                It will be hidden from viewers other than the comment
                author.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form action="<?= BASE_URL ?>/admin/remove_comment.php" method="post">
                    <input type="hidden" name="commentId" id="removeCommentConfirmId">
                    <button id="removeCommentConfirmBtn" type="submit" class="btn btn-danger">Remove</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(() => {
        // format attachment file size
        function formatSize(size) {
            if (size < 1000) return size + ' Bytes';
            else if (size < 1_000_000) return (size / 1000).toFixed(2) + ' kB';
            else return (size / 1_000_000).toFixed(2) + ' MB';
        }

        $.each($('#attachments small'), function () {
            $(this).text(formatSize($(this).text()));
        });

        // like and dislike
        $('input[name="like"]').on('change', function () {
            $.post('<?= BASE_URL ?>/displayNews/ajax_rate_article.php', {
                articleId: <?= $article_id ?>,
                like: $(this).val()
            }, null, 'json').done(({ likes, dislikes }) => {
                $('#likes').text(likes);
                $('#dislikes').text(dislikes);
            });
        });

        // comment remove
        $('.removeCommentBtn').on('click', function () {
            const commentId = $(this).data('commentId');
            $('#removeCommentConfirmId').val(commentId);
            $('#removeCommentModalComment').html(
                $(`.list-group-item[data-comment-id="${commentId}"]`).html()
            );
            $('#removeCommentModalComment button').remove();
        });
    });
</script>

<?php include PROJECT_ROOT . '/footer.html' ?>