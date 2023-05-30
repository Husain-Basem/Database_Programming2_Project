<?php
include_once '../prelude.php';
?>


<div class="row align-items-end">
    <div class="col-sm-12 col-md-5 col-lg-4 mb-3">
        <label for="userSearch" class="form-label">&nbsp;</label>
        <input class="form-control" type="text" name="search" id="userSearch" placeholder="Username, Id or email">
    </div>
    <div class="col-sm-12 col-md-5 col-lg-3 mb-3">
        <label for="userSearchBy" class="form-label">Find User By</label>
        <div class="input-group">
            <select class="form-select" name="userSearchBy" id="userSearchBy">
                <option selected value="userName">Username</option>
                <option value="userId">User Id</option>
                <option value="email">Email</option>
            </select>
            <button id="findUserBtn" type="button" class="btn btn-outline-primary">Find</button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-md-10 col-lg-7">
        <div class="card">
            <div class="card-body" id="userManageActions">
                <p class="text-muted text-center my-5">Use the inputs to find users</p>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal Body -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUsermodalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUsermodalTitleId">Delete User Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user? This action is not reversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="deleteUserConfirmBtn" type="button" class="btn btn-danger">Delete User</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(() => {
        $('#findUserBtn').on('click', function () {
            $(this).prepend(`
             <span class="spinner-border spinner-border-sm" role="status"></span>
            `);
            $.post('<?= BASE_URL ?>/admin/get_user.php', {
                userSearch: $('#userSearch').val(),
                userSearchBy: $('#userSearchBy').val()
            }).done(userJson => {
                const user = JSON.parse(userJson);
                $('#userManageActions').html(`
                    <dl>
                    <div class="row">
                        <dt class="col-3">User Id</dt>
                        <dd class="col-9">${user.userId}</dd>
                        <dt class="col-3">Username</dt>
                        <dd class="col-9">${user.username}</dd>
                        <dt class="col-3">Email</dt>
                        <dd class="col-9">${user.email}</dd>
                        <dt class="col-3">Type</dt>
                        <dd class="col-9">${user.type}</dd>
                    </div>
                    </dl>
                    <button class="btn btn-danger float-end" data-user-id="${user.userId}"  data-username="${user.username}"
                            onclick="deleteUserBtnClicked(this)">
                      Delete User
                    </button>
                `);
                $('#findUserBtn span').remove();
            }).fail(() => {
                $('#userManageActions').html(`<p class="text-center my-2">No User Found</p>`);
                $('#findUserBtn span').remove();
            });
        });

        new bootstrap.Modal('#deleteUserModal');

        $('#deleteUserConfirmBtn').on('click', function () {
            $.post('<?= BASE_URL ?>/admin/delete_user.php', { userId: $(this).data('userId') })
                .done(() => {
                    $('.toast-container').append(`
                       <div id="userDeleteToast" class="toast text-bg-success" role="alert" aria-live="polite" aria-atomic="true">
                           <div class="toast-body">User '${$(this).data('username')}' was deleted</div>
                       </div>
                    `);
                    $('#findUserBtn').trigger('click');
                }).fail(() => {
                    $('.toast-container').append(`
                       <div id="userDeleteToast" class="toast text-bg-danger" role="alert" aria-live="polite" aria-atomic="true">
                           <div class="toast-body">User was not deleted</div>
                       </div>
                    `);
                }).always(() => {
                    bootstrap.Modal.getInstance('#deleteUserModal').hide();
                    $('#userDeleteToast').on('hidden.bs.toast', function () { $(this).remove(); });
                    const toast = new bootstrap.Toast($('#userDeleteToast'), { delay: 3000 });
                    toast.show();
                });
        });

    });

    function deleteUserBtnClicked(e) {
        $('#deleteUserConfirmBtn').data('userId', $(e).data('userId'));
        $('#deleteUserConfirmBtn').data('username', $(e).data('username'));
        bootstrap.Modal.getInstance('#deleteUserModal').show();
    }
</script>