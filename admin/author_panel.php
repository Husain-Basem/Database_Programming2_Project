<?php
include_once '../prelude.php';
?>

<div class="hstack">
  <h3>Unpublished Articles</h3>
  <form class="ms-auto" action="<?= BASE_URL . '/articleEdit/create_article.php' ?>" method="post">
    <button id="createBtn" class="btn btn-primary" type="submit">Create Article</button>
  </form>
</div>

<div class="list-group">
  <?php
  $unpublished = Article::get_author_articles($_SESSION['userId']);
  foreach ($unpublished as $article) {
    echo '
  <a href="' . BASE_URL . '/articleEdit/edit_article.php?articleId=' . $article->articleId . '" class="list-group-item list-group-item-action flex-column align-items-start">
    <div class="d-flex w-100">
      <span class="badge rounded-pill text-bg-primary vertical-align-middle">' . $article->display_category() . '</span>
      <h5 class="mb-0 ms-3">' . $article->title . '</h5>
    </div>
    <p class="mb-1 text-truncate">' . strip_tags(substr($article->content, 0, 1000)) . '</p>
  </a>
    ';
  }
  ?>
</div>

<hr>

<h3>Published Articles</h3>

<div class="list-group">
  <?php
  $published = Article::get_author_articles($_SESSION['userId'], true);
  foreach ($published as $article) {
    $href = $article->approved ?
      (BASE_URL . '/displayNews/article.php?id=' . $article->articleId)
      : (BASE_URL . '/articleEdit/preview.php?articleId=' . $article->articleId);
    echo '
  <a href="' . $href . '" class="list-group-item list-group-item-action flex-column align-items-start">
    <div class="d-flex w-100">
      <span class="badge rounded-pill text-bg-primary vertical-align-middle">' . $article->display_category() . '</span>
      <h5 class="mb-0 ms-3">' . $article->title . '</h5>
      <small class="text-muted ms-auto">' . $article->date . '</small>
    </div>
    <p class="mb-1 text-truncate">' . strip_tags(substr($article->content, 0, 1000)) . '</p>
  </a>
    ';
  }
  ?>
</div>