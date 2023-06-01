<?php
include_once '../prelude.php';
?>

<div class="row">
    <div class="col-sm-12 col-lg-8">
        <form id="ARform">
            <div class="mb-3">
                <label for="ARquery" class="form-label">Report Type</label>
                <select class="form-select" name="query" id="ARquery">
                    <option value="popular" selected>10 Most Popular Articles</option>
                    <option value="author">Articles by Author</option>
                </select>
            </div>
            <div id="ARarticleDates" class="row align-items-end" style="display: none">
                <div class="col mb-3">
                    <label for="ARdateBegin" class="form-label">From</label>
                    <input class="form-control" type="date" name="dateBegin" id="ARdateBegin" value="1970-01-01">
                </div>
                <div class="col mb-3">
                    <label for="ARdateEnd" class="form-label">To</label>
                    <input class="form-control" type="date" name="dateEnd" id="ARdateEnd" value="">
                </div>
                <div class="col mb-3">
                    <button id="ARarticlesDateFindBtn" type="submit" class="btn btn-outline-primary">Find</button>
                </div>
            </div>
            <div id="ARauthorSearch" class="row align-items-end" style="display: none">
                <div class="col mb-3">
                    <label for="ARuserSearch" class="form-label">&nbsp;</label>
                    <input class="form-control" type="text" name="search" id="ARuserSearch"
                        placeholder="Username, Id or email">
                </div>
                <div class="col-sm-12 col-md-5 mb-3">
                    <label for="ARuserSearchBy" class="form-label">Find Author By</label>
                    <div class="input-group">
                        <select class="form-select" name="userSearchBy" id="ARuserSearchBy">
                            <option selected value="userName">Username</option>
                            <option value="userId">User Id</option>
                            <option value="email">Email</option>
                        </select>
                        <button id="ARfindUserBtn" type="submit" class="btn btn-outline-primary">Find</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-lg-8">
        <ul class="list-group" id="ARarticlesList"></ul>
    </div>
</div>

<script>
    $(() => {
        $('#ARquery').on('change', function () {
            $('#ARarticlesList').html('');
            if ($(this).val() == 'author') {
                $('#ARauthorSearch').show();
                $('#ARarticleDates').hide();
            } else {
                $('#ARauthorSearch').hide();
                $('#ARarticleDates').show();
                $('#ARarticlesDateFindBtn').trigger('click');
            }
        });

        $('#ARarticlesDateFindBtn').on('click', displayPopularArticles);
        const date = new Date();
        date.setDate(date.getDate() + 1);
        $('#ARdateEnd').val(date.toISOString().substr(0, 10));
        $('#ARquery').trigger('change');

        function displayPopularArticles(event) {
            event.stopPropagation();
            event.preventDefault();
            $('#ARarticlesList').html('');
            $.get('<?= BASE_URL ?>/admin/ajax_get_popular_articles.php', {
                dateBegin: $('#ARdateBegin').val(),
                dateEnd: $('#ARdateEnd').val(),
            })
                .done(articlesJSON => {
                    const articles = JSON.parse(articlesJSON);
                    for (const article of articles) {
                        const status =
                            article.removed ? 'Removed' :
                                article.approved ? 'Approved' :
                                    article.published ? 'Pending Approval' : 'Unpublished';
                        $('#ARarticlesList').append(`
                       <li class="list-group-item d-flex align-items-baseline" title="'${article.title}' by ${article.author} - ${status} on ${article.date}">
                           <span class="text-truncate">${article.title}</span>
                           <small class="ms-2 text-muted text-truncate">By ${article.author}</small>
                           <div class="ms-auto">
                           <small class="text-muted">${article.likes} Likes, ${article.dislikes} Dislikes</small>
                           </div>
                       </li>
                    `);
                    }
                }).fail(() => {
                    $('#ARarticlesList').html('<li class="list-group-item">No Articles found</li>');
                });
        }


        $('#ARform').on('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();

            $('#ARfindUserBtn').prepend(`
             <span class="spinner-border spinner-border-sm" role="status"></span>
            `);
            $('#ARarticlesList').html('');
            $.post('<?= BASE_URL ?>/admin/ajax_get_user_articles.php', {
                userSearch: $('#ARuserSearch').val(),
                userSearchBy: $('#ARuserSearchBy').val()
            }).done(articlesJSON => {
                $('#ARfindUserBtn span').remove();
                const { published, unpublished } = JSON.parse(articlesJSON);

                for (const article of published.concat(unpublished)) {
                    const status =
                        article.removed ? 'Removed' :
                            article.approved ? 'Approved' :
                                article.published ? 'Pending Approval' : 'Unpublished';
                    const statusBG =
                        article.removed ? 'danger' :
                            article.approved ? 'success' :
                                article.published ? 'warning' : 'secondary';
                    $('#ARarticlesList').append(`
                       <li class="list-group-item d-flex align-items-baseline" title="'${article.title}' by ${article.author} - ${status} on ${article.date}">
                           <span class="badge rounded-pill me-2 text-bg-${statusBG}">${status}</span> 
                           <span class="text-truncate">${article.title}</span>
                           <small class="ms-2 text-muted text-truncate">By ${article.author}</small>
                           <div class="btn-group ms-auto">
                               <a class="btn btn-outline-primary"
                                   href="<?= BASE_URL ?>/articleEdit/edit_article.php?articleId=${article.articleId}&returnUrl=<?= urlencode(BASE_URL . '/admin/admin_panel.php#admin-report-tab') ?>&returnName=Admin%20Panel">
                                   Edit
                                </a>
                           </div>
                       </li>
                    `);
                }
            }).fail(({ responseText }) => {
                if (responseText == 'notfound')
                    $('#ARarticlesList').html(`<li class="list-group-item">No User Found</li>`);
                else if (responseText == 'notauthor')
                    $('#ARarticlesList').html(`<li class="list-group-item">Not an author</li>`);
                else
                    $('#ARarticlesList').html(`<li class="list-group-item">Error</li>`);
                $('#ARfindUserBtn span').remove();
            });
        });
    });
</script>