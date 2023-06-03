<?php
include_once '../prelude.php';

$article = Article::from_articleId($_GET['articleId']);

if (empty($_SESSION['username'])) {
    // redirect to login page that returns to this page
    header('Location: ' . BASE_URL . '/user/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$user = User::from_username($_SESSION['username']);

// only allow admins and the author of the article
if (!($user->is_admin() || ($user->is_author() && $article->writtenBy == $user->userId))) {
    $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Unauthorized request');
    header('Location: ' . BASE_URL . '/index.php');
}

// use the website header
if ($user->is_admin()) {
    $pageTitle = 'Review - ' . $article->title;
    $returnUrl = BASE_URL . '/admin/admin_panel.php#pending-articles-tab';
} else {
    $pageTitle = 'Preview - ' . $article->title;
    $returnUrl = BASE_URL . '/articleEdit/edit_article.php?articleId=' . $article->articleId;
}
if (isset($_GET['returnUrl']))
    $returnUrl = $_GET['returnUrl'];
if (isset($_GET['returnName']))
    $returnName = $_GET['returnName'];
else
    $returnName = 'editor';
$headerIncludes = '<link rel="stylesheet" href="' . BASE_URL . '/css/quill.snow.css" />';
include PROJECT_ROOT . '/header.html';
?>


<div class="container">
    <div class="card mb-3">
        <div class="card-body">
            <div class="hstack gap-3">
                <a href="<?= $returnUrl ?>">
                    <?= $user->is_admin() ? 'Back to admin panel' : 'Back to ' . $returnName ?>
                </a>
                <span class="text-muted">This article is
                    <?= $article->removed ? 'Removed' :
                        ($article->approved ? 'Approved for publication' :
                            ($article->published ? 'Pending approval' : 'Unpublished')) ?>
                </span>
                <?php
                if ($user->is_admin()) {
                    echo '
              <a href="' . BASE_URL . '/articleEdit/edit_article.php?articleId=' . $article->articleId . '"
                  class="ms-auto btn btn-secondary bg-secondary-subtle">Edit</a>
              <a href="' . BASE_URL . '/admin/approve_article.php?articleId=' . $article->articleId . '"
                  class="btn btn-success">Approve Publication</a>
            ';
                }
                ?>
            </div>
        </div>
    </div>

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
            <?= $user->firstName . ' ' . $user->lastName ?>
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

    <hr>

    <div class="row mb-5 text-muted">
        <div class="col-md-6 mb-5">
            <h2 id="comments">Comments</h2>
            <p>No comments yet.</p>
        </div>
        <div class="col-md-6">
            <div class="hstack align-items-center gap-3 mb-3">
                <h2 class="mb-0">Rate this Article</h2>
                <div class="btn-group ms-auto">
                    <input type="radio" class="btn-check" name="like" id="like1" aria-label="Like" disabled>
                    <label for="like1" class="btn btn-outline-success" title="Like">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                            <path
                                d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.077.27.113.567.113.856 0 .289-.036.586-.113.856-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.163 3.163 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.82 4.82 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z" />
                        </svg>
                    </label>
                    <input type="radio" class="btn-check" name="like" id="like2" aria-label="Dislike" disabled>
                    <label for="like2" class="btn btn-outline-danger" title="Dislike">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-hand-thumbs-down-fill" viewBox="0 0 16 16">
                            <path
                                d="M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2.094 2.094 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.162 3.162 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.823 4.823 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591z" />
                        </svg>
                    </label>
                </div>
                <span>0 Likes</span>
                <span class="me-4 me-sm-0">0 Dislikes</span>
            </div>
            <h2>Add a Comment</h2>
        </div>
    </div>
</div>

<?php include PROJECT_ROOT . '/footer.html' ?>