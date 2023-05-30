<?php
include_once '../prelude.php';

$article = Article::from_articleId($_GET['articleId']);

// use the website header
$pageTitle = 'Preview - ' . $article->title;
$headerIncludes = '<link rel="stylesheet" href="' . BASE_URL . '/css/quill.snow.css" />';
include PROJECT_ROOT . '/header.html';
?>


<div class="container">
    <a href="<?= BASE_URL . '/articleEdit/edit_article.php?articleId=' . $article->articleId ?>">
        Back to editor
    </a>
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