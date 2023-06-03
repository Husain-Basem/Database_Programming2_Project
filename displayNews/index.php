<?php
declare(strict_types=1);
include_once '../prelude.php';

$pageTitle = 'News';
include PROJECT_ROOT . '/header.html';


$pagination = Article::get_published_articles();
$articles = $pagination->get_page(null, function ($row) {
  return Article::__set_state($row);
});

?>

<div class="container">
  <h1>Latest News</h1>
  <div class="d-flex gap-3" style="align-items: first baseline;">
    Page:
    <?= $pagination->pagination_controls(); ?>
  </div>
  <div class="row">
    <?php
    foreach ($articles as $article) {
      echo '
<div class="col-12 col-lg-10">
<div class="card mb-3">
  <div class="row g-0">
    <div class="col-md-4">
<div class="card-img ratio" style="background-image: url(' . $article->thumbnail . ')" role="img"></div>
    </div>
    <div class="col-12 col-md-8">
      <div class="card-body">
        <h5 class="card-title">' . $article->title . '</h5>
        <p class="card-text">' . substr(strip_tags($article->content), 0, 170) . '...</p>
        <p class="card-text">
            <span class="badge rounded-pill text-bg-secondary vertical-align-middle">
                ' . $article->display_category() . '
            </span>
            <small class="ms-2 text-muted">By ' . $article->get_author_name() . '</small>
            <small class="ms-2 text-muted">Read time ' . $article->readTime . ' min</small>
        </p>
        <a href="' . BASE_URL . '/displayNews/article.php?id=' . $article->articleId . '" class="btn btn-primary">Read More</a>
      </div>
    </div>
  </div>
</div>
</div>
        ';
    }
    ?>
  </div>
  <div class="d-flex gap-3" style="align-items: first baseline;">
    Page:
    <?= $pagination->pagination_controls(); ?>
  </div>
</div>

<?php

include PROJECT_ROOT . '/footer.html';

?>