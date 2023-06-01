<?php

include_once '../prelude.php';

settype($fragment, 'string');
$pageTitle = 'Admin Panel';
include PROJECT_ROOT . '/header.html';

if (empty($_SESSION['username'])) {
    // redirect to login page that returns to this page
    header('Location: ' . BASE_URL . '/user/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$user = User::from_username($_SESSION['username']);

if (!$user->is_admin()) {
    $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Unauthorized request');
    header('Location: ' . BASE_URL . '/index.php');
}
?>

<div class="container">
    <h1>Admin Panel</h1>
    <div class="row g-5">
        <div class="col-sm-12 col-md-3 col-lg-2">
            <ul class="nav nav-pills flex-column" id="myTab" role="tablist" style="--bs-body-text-align: start;">
                <li class="nav-item">
                    <a class="nav-link disabled pb-1 mt-2">User Management</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a id="manage-users-tab" data-bs-target="#manage-users-pane" aria-controls="manage-users-pane"
                        data-bs-toggle="tab" role="tab" href="#" class="w-100 nav-link active" aria-current="page">
                        Delete Users
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a id="register-author-tab" data-bs-target="#register-author-pane"
                        aria-controls="register-author-pane" data-bs-toggle="tab" role="tab" href="#"
                        class="w-100 nav-link">Register Author</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled pb-1 mt-2">Article Management</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a id="pending-articles-tab" aria-controls="pending-articles-pane"
                        data-bs-target="#pending-articles-pane" data-bs-toggle="tab" class="w-100 nav-link"
                        type="button" role="tab">
                        Pending Articles
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a id="manage-articles-tab" aria-controls="manage-articles-pane"
                        data-bs-target="#manage-articles-pane" data-bs-toggle="tab" class="w-100 nav-link" type="button"
                        role="tab">
                        Edit/Remove Articles
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a id="manage-comments-tab" aria-controls="manage-comments-pane"
                        data-bs-target="#manage-comments-pane" data-bs-toggle="tab" class="w-100 nav-link" type="button"
                        role="tab">
                        Manage Comments
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a id="manage-own-tab" aria-controls="manage-own-pane" data-bs-target="#manage-own-pane"
                        data-bs-toggle="tab" class="w-100 nav-link" type="button" role="tab">
                        Manage Own Articles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled pb-1 mt-2">Admin Actions</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a id="report-tab" aria-controls="report-pane" data-bs-target="#report-pane" data-bs-toggle="tab"
                        class="w-100 nav-link" type="button" role="tab">
                        Admin Report
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-sm-12 col-md-9 col-lg-10 tab-content border-start" id="myTabContent">
            <div id="manage-users-pane" class="tab-pane fade show active" role="tabpanel"
                aria-labelleddy="manage-users-tab" tabindex="0">
                <h2>Delete Users</h2>
                <?php require_once PROJECT_ROOT . '/admin/manage_users.php'; ?>
            </div>
            <div id="register-author-pane" class="tab-pane fade" role="tabpanel" aria-labelleddy="register-author-tab"
                tabindex="0">
                <h2>Register Author</h2>
                <?php require_once PROJECT_ROOT . '/admin/register_author.php'; ?>
            </div>
            <div id="pending-articles-pane" class="tab-pane fade" role="tabpanel" aria-labelleddy="pending-articles-tab"
                tabindex="0">
                <!-- <h2>Pending Articles</h2> -->
                <?php require_once PROJECT_ROOT . '/admin/pending_articles.php'; ?>
            </div>
            <div id="manage-articles-pane" class="tab-pane fade" role="tabpanel" aria-labelleddy="manage-articles-tab"
                tabindex="0">
                <h2>Edit or Delete Articles</h2>
                <?php require_once PROJECT_ROOT . '/admin/manage_articles.php'; ?>
            </div>
            <div id="manage-comments-pane" class="tab-pane fade" role="tabpanel" aria-labelleddy="manage-comments-tab"
                tabindex="0">
                <h2>Manage Comments</h2>
                <?php require_once PROJECT_ROOT . '/admin/manage_comments.php'; ?>
            </div>
            <div id="manage-own-pane" class="tab-pane fade" role="tabpanel" aria-labelleddy="manage-own-tab"
                tabindex="0">
                <h2>Manage Own Articles</h2>
                <?php require_once PROJECT_ROOT . '/admin/author_panel.php'; ?>
            </div>
            <div id="report-pane" class="tab-pane fade" role="tabpanel" aria-labelleddy="report-tab" tabindex="0">
                <h2>Admin Report Dashboard</h2>
                <?php require_once PROJECT_ROOT . '/admin/admin_reports.php'; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // auto select tab from url fragment
    $(() => {
        <?= !empty($fragment) ? "location.hash = '$fragment';" : '' ?>
        $(location.hash).tab('show');
    });
</script>

<!--  register form validation -->
<script>
    $(() => {
        'use strict';

        const forms = $('.needs-validation');

        // stop forms from submitting
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false)
        });

        // check username using AJAX with JQuery
        $('#username').on('blur', function () {
            $.get('<?= BASE_URL ?>' + '/user/exists.php', { u: $(this).val() })
                .done((exists) => {
                    if (exists == "1") {
                        $(this).addClass('is-invalid');
                        $('#usernameErr').html('Username is already used');
                    } else {
                        $(this).removeClass('is-invalid');
                        $('#usernameErr').html('Username must not be empty');
                    }
                });
        });

        $('#passwordConfirm').on('input', function () {
            if ($(this).val() != $('#password').val()) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        $('#password').on('input', () => $('#passwordConfirm').trigger('input'));

    });
</script>

<?php
include PROJECT_ROOT . '/footer.html';
?>