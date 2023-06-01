<?php
declare(strict_types=1);
include_once '../prelude.php';

$pageTitle = 'News';
include PROJECT_ROOT . '/header.html';


$articles = Article::get_published_articles();

?>

<div class="container">
  <?php
  foreach ($articles as $article) {
    echo '
<div class="card mb-3">
  <div class="row g-0">
    <div class="col">
<div class="card-img ratio" style="background-image: url(' . $article->thumbnail . ')" role="img"></div>
    </div>
    <div class="col-7">
      <div class="card-body">
        <h5 class="card-title">' . $article->title . '</h5>
        <p class="card-text">' . substr(strip_tags($article->content), 0, 200) . '...</p>
        <p class="card-text"><small class="text-muted">Read time ' . $article->readTime . ' min</small></p>
        <a href="' . BASE_URL . '/displayNews/article.php?id=' . $article->articleId . '" class="btn btn-primary">Read More</a>
      </div>
    </div>
  </div>
</div>
        ';
  }
  ?>
</div>

<?php

include PROJECT_ROOT . '/footer.html';

?>