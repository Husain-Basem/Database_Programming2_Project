<?php
include_once '../prelude.php';

$article = Article::from_articleId($_GET['articleId']);

// TODO: authorize user
$user = User::from_userId($_SESSION['userId']);

// use the website header
if ($user->is_admin()) {
    $pageTitle = 'Review - ' . $article->title;
    $returnUrl = BASE_URL . '/admin/admin_panel.php#pending-articles-tab';
} else {
    $pageTitle = 'Preview - ' . $article->title;
    $returnUrl = BASE_URL . '/articleEdit/edit_article.php?articleId=' . $article->articleId;
}
$headerIncludes = '<link rel="stylesheet" href="' . BASE_URL . '/css/quill.snow.css" />';
include PROJECT_ROOT . '/header.html';
?>


<div class="container">
    <div class="hstack gap-3">
        <a href="<?= $returnUrl ?>">
            <?= $user->is_admin() ? 'Back to admin panel' : 'Back to editor' ?>
        </a>
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