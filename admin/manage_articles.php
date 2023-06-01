<?php
include_once '../prelude.php';

$pagination = Article::search_articles_exact(null, null, null, null);
$pages = $pagination->get_total_pages();
$count = $pagination->get_total_entries();

?>

<div class="row mb-3">
    <div class="col-sm-12 col-lg-8">
        <form id="manageArticlesForm">
            <div class="input-group">
                <input type="text" name="articleSearch" id="manageArticleSearch" class="form-control"
                    placeholder="Article title contains">
                <button type="submit" id="manageArticleSearchBtn" class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>
</div>

<!--  Pagination controls -->
<div class="d-flex align-items-baseline mb-2">
    <span class="me-2">Page</span>
    <?= $pagination->pagination_controls() ?>
</div>


<div class="row">
    <div class="col-sm-12 col-lg-8">
        <ul class="list-group" id="manageArticlesList">
        </ul>
    </div>
</div>

<div class="modal fade" id="articleRemoveModal" tabindex="-1" role="dialog" aria-labelledby="articleRemovemodalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="articleRemovemodalTitleId">Remove Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove this article:
                <br>
                <span id="articleRemoveTitle"></span>
                <br>
                Doing so will hide the article from viewers, and display 'this article has been removed by admin' for
                the author. The article will not be deleted permanently until done so in the article editor.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="articleRemoveConfirmBtn" type="button" class="btn btn-danger">Remove</a>
            </div>
        </div>
    </div>
</div>


<script>
    $(() => {
        let pages = <?= $pages ?>;

        $('#manageArticlesForm').on('submit', event => {
            event.preventDefault();
            event.stopPropagation();
            $('#manage-articles-pane .page-number').first().trigger('click');
        });

        $('#manage-articles-pane .page-number')
            .on('click', paginationOnClick);

        function paginationOnClick(event) {
            const thisEl = $(this);
            event.preventDefault();
            $('#manageArticlesList').html('');
            $.post('<?= BASE_URL ?>/admin/ajax_get_articles.php',
                {
                    page: thisEl.data('page'),
                    search: $('#manageArticleSearch').val()
                })
                .done(articlesJson => {
                    const { articles, totalPages } = JSON.parse(articlesJson);
                    for (const article of articles) {
                        const status =
                            article.removed ? 'Removed' :
                                article.approved ? 'Approved' :
                                    article.published ? 'Pending Approval' : 'Unpublished';
                        const statusBG =
                            article.removed ? 'danger' :
                                article.approved ? 'success' :
                                    article.published ? 'warning' : 'secondary';
                        const href = article.approved ?
                            '<?= BASE_URL ?>/displayNews/article.php?id=' + article.articleId :
                            '<?= BASE_URL ?>/articleEdit/preview.php?articleId=' + article.articleId + '&returnUrl=<?= urlencode(BASE_URL . '/admin/admin_panel.php#manage-articles-tab') ?>';
                        $('#manageArticlesList').append(`
                               <li class="list-group-item d-flex align-items-baseline" title="'${article.title}' by ${article.author} - ${status} on ${article.date}">
                                   <span class="badge rounded-pill me-2 text-bg-${statusBG}">${status}</span> 
                                   <a href="${href}" class="text-truncate">${article.title}</a>
                                   <small class="ms-2 text-muted text-truncate">By ${article.author}</small>
                                   <div class="btn-group ms-auto">
                                       <a class="btn btn-outline-primary"
                                           href="<?= BASE_URL ?>/articleEdit/edit_article.php?articleId=${article.articleId}&returnUrl=<?= urlencode(BASE_URL . '/admin/admin_panel.php#manage-articles-tab') ?>&returnName=Admin%20Panel">
                                           Edit
                                        </a>
                                       <button class="articleRemoveBtn btn btn-outline-danger" data-article-id="${article.articleId}" data-article-title="${article.title}">Remove</button>
                                   </div>
                               </li>
                            `);
                    }
                    $('.articleRemoveBtn').on('click', articleRemoveBtnOnClick);
                    $('#manage-articles-pane .page-item.active').removeClass('active');
                    $('#manage-articles-pane .page-number[data-page="' + thisEl.data('page') + '"]').parent().addClass('active');
                    if (totalPages != pages) {
                        updatePagination(totalPages);
                    }
                }).fail(() => {
                    $('#manageArticlesList').append(`
                          <li class="list-group-item">
                              <small class="text-muted">No published articles awaiting approval</small>
                          </li>
                        `);
                });

        }

        $('#manage-articles-pane .page-number').first().trigger('click');

        function updatePagination(totalPages) {
            console.log({ totalPages, pages });
            if (pages > totalPages) {
                $('#manage-articles-pane .page-item').slice(totalPages).remove();
            } else {
                for (let p = pages + 1; p <= totalPages; p++) {
                    $('#manage-articles-pane ul.pagination').append(`
                        <li class="page-item"><a class="page-link page-number" data-page="${p}" href="?p=${p}">${p}</a></li>
                    `);
                }
                $('#manage-articles-pane .page-number').slice(pages)
                    .on('click', paginationOnClick);
            }
            pages = totalPages;
        }

        const modal = new bootstrap.Modal('#articleRemoveModal');

        function articleRemoveBtnOnClick() {
            $('#articleRemoveTitle').text($(this).data('articleTitle'));
            $('#articleRemoveConfirmBtn').on('click',
                () => {
                    $.post('<?= BASE_URL ?>/admin/remove_article.php', {
                        articleId: $(this).data('articleId')
                    }).done(() => {
                        $('.toast-container').append(`
                       <div id="articleRemoveToast" class="toast text-bg-success" role="alert" aria-live="polite" aria-atomic="true">
                           <div class="toast-body">Article '${$(this).data('articleTitle')}' was removed</div>
                       </div>
                    `);
                        $('#manageArticlesForm').trigger('submit');
                    }).fail(() => {
                        $('.toast-container').append(`
                       <div id="articleRemoveToast" class="toast text-bg-danger" role="alert" aria-live="polite" aria-atomic="true">
                           <div class="toast-body">Article was not removed</div>
                       </div>
                    `);
                    }).always(() => {
                        bootstrap.Modal.getInstance('#articleRemoveModal').hide();
                        $('#articleRemoveToast').on('hidden.bs.toast', function () { $(this).remove(); });
                        const toast = new bootstrap.Toast($('#articleRemoveToast'), { delay: 3000 });
                        toast.show();
                    });
                }
            );
            modal.show();
        }

    });
</script>