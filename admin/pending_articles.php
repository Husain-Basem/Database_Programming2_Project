<?php
include_once '../prelude.php';

$articles = Article::get_published_articles();
$count = Article::count_articles(null, true, false);

?>

<div class="d-flex align-items-center">
    <h2>Pending Articles</h2>
    <small class="ms-3 text-muted">
        <?= $count ?> Published articles awaiting approval
    </small>
</div>

<div class="row mb-3">
    <div class="col-sm-12 col-lg-8">
        <div class="input-group">
            <input type="text" name="articleSearch" id="pendingArticleSearch" class="form-control"
                placeholder="Article title contains">
            <button id="pendingArticleSearchBtn" class="btn btn-primary">Search</button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-lg-8">
        <ul class="list-group" id="pendingArticlesList">
        </ul>
    </div>
</div>

<script>
    $(() => {
        $('#pendingArticleSearchBtn').on('click', () => {
            $('#pendingArticlesList').html('');
            $.post('<?= BASE_URL ?>/admin/ajax_get_articles.php', {
                articleSearch: $('#pendingArticleSearch').val(),
                published: 1,
                approved: 0
            }).done(articlesJson => {
                const articles = JSON.parse(articlesJson);
                console.log(articles);
                for (const article of articles) {
                    $('#pendingArticlesList').append(`
                      <li class="list-group-item d-flex align-items-baseline" title="'${article.title}' by ${article.author}">
                          <span class="text-truncate">${article.title}</span>
                          <small class="ms-2 text-muted text-truncate">By ${article.author}</small>
                          <a class="btn btn-outline-primary ms-auto" href="<?= BASE_URL ?>/articleEdit/preview.php?articleId=${article.articleId}"
                                  onclick(reviewPendingArticle(this)">Review</a>
                      </li>
                    `);
                }
                if (articles.length < <?= $count ?>)
                    $('#pendingArticlesList').append(`
                      <li class="list-group-item">
                          <small class="text-muted">And ${<?= $count ?> - articles.length} more article(s)</small>
                      </li>
                    `);
            }).fail(() => {
                $('#pendingArticlesList').append(`
                      <li class="list-group-item">
                          <small class="text-muted">No articles matching query found</small>
                      </li>
                    `);
            });
        });

        $('#pendingArticleSearchBtn').trigger('click');
    });
</script>