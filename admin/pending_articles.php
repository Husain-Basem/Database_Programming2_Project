<?php
include_once '../prelude.php';

// get published but not approved articles
$pagination = Article::search_articles_exact(null, 1, 0, null);
$pages = $pagination->get_total_pages();
$count = $pagination->get_total_entries();

?>

<div class="d-flex align-items-baseline mb-2">
    <h2>Pending Articles</h2>
    <small class="ms-3 text-muted">
        <?= $count ?> Published articles awaiting approval
    </small>
</div>

<!--  Pagination controls -->
<div class="d-flex align-items-baseline mb-2">
    <span class="me-2">Page</span>
    <?= $pagination->pagination_controls() ?>
</div>


<div class="row">
    <div class="col-sm-12 col-lg-8">
        <ul class="list-group" id="pendingArticlesList">
        </ul>
    </div>
</div>

<script>
    $(() => {

        $.each($('#pending-articles-pane .page-number'), function () {
            const thisEl = $(this);
            thisEl.on('click', (event) => {
                event.preventDefault();
                $('#pendingArticlesList').html('');
                $.post('<?= BASE_URL ?>/admin/ajax_get_articles.php', {
                    page: thisEl.data('page'),
                    published: 1,
                    approved: 0
                })
                    .done(articlesJson => {
                        const { articles } = JSON.parse(articlesJson);
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
                        $('#pending-articles-pane .page-item.active').removeClass('active');
                        $('#pending-articles-pane .page-number[data-page="' + thisEl.data('page') + '"]').parent().addClass('active');
                    }).fail(() => {
                        $('#pendingArticlesList').append(`
                          <li class="list-group-item">
                              <small class="text-muted">No published articles awaiting approval</small>
                          </li>
                        `);
                    });

            });
        });

        $('#pending-articles-pane .page-number').first().trigger('click');
    });
</script>