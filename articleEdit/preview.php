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
$headerIncludes = '<link rel="stylesheet" href="' . BASE_URL . '/css/quill.snow.css" />';
include PROJECT_ROOT . '/header.html';
?>


<div class="container">
    <div class="hstack gap-3">
        <a href="<?= $returnUrl ?>">
            <?= $user->is_admin() ? 'Back to admin panel' : 'Back to editor' ?>
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
                  class="ms-auto btn btn-secondary">Edit</a>
              <a href="' . BASE_URL . '/admin/approve_article.php?articleId=' . $article->articleId . '"
                  class="btn btn-success">Approve Publication</a>
            ';
        }
        ?>
    </div>
    <hr>
    <h1>
        <?= $article->title; ?>
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

    <hr>

    <h2>Comments</h2>

    <p>No comments yet.</p>

    <hr>

    <h2>Add a Comment</h2>
</div>

<?php include PROJECT_ROOT . '/footer.html' ?>