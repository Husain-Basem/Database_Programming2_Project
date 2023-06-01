<?php
include_once '../prelude.php';
?>


<form id="findUserForm">
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
                <button id="findUserBtn" type="submit" class="btn btn-outline-primary">Find</button>
            </div>
        </div>
    </div>
</form>

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
        $('#findUserForm').on('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();

            $('#findUserBtn').prepend(`
             <span class="spinner-border spinner-border-sm" role="status"></span>
            `);
            $.post('<?= BASE_URL ?>/admin/ajax_get_user.php', {
                userSearch: $('#userSearch').val(),
                userSearchBy: $('#userSearchBy').val()
            }).done(userJson => {
                const user = JSON.parse(userJson);
                $('#userManageActions').html(`
                <form id="editUserForm">
                    <div class="row g-3 mb-3 align-items-center">
                        <div class="col-3 text-end"><label for="MUuserId">User Id</label></div>
                        <div class="col-9"><input class="form-control" type="text" name="userId" id="MUuserId" value="${user.userId}"
                                disabled></div>
                        <div class="col-3 text-end"><label for="MUusername">Username</label></div>
                        <div class="col-9"><input class="editable form-control" type="text" name="username"
                                id="MUusername" value="${user.username}" disabled></div>
                        <div class="col-3 text-end"><label for="MUemail">Email</label></div>
                        <div class="col-9"><input class="editable form-control" type="email" name="email" id="MUemail"
                                value="${user.email}" disabled></div>
                        <div class="col-3 text-end"><label for="MUtype">Type</label></div>
                        <div class="col-9">
                            <select class="editable form-select" name="type" id="MUtype" disabled>
                                <option value="viewer" ${user.type == 'VIEWER' ? 'selected' : ''}>Viewer</option>
                                <option value="author" ${user.type == 'AUTHOR' ? 'selected' : ''}>Author</option>
                                <option value="admin" ${user.type == 'ADMIN' ? 'selected' : ''}>Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="hstack gap-3">
                        <div class="form-check form-switch me-auto">
                            <input class="form-check-input" type="checkbox" role="switch" id="userEditingSwitch">
                            <label class="form-check-label" for="userEditingSwitch">Enable Editing</label>
                        </div>
                        <button id="editUserSaveBtn" class="btn btn-primary" type="submit"
                            style="display: none">Save</button>
                        <button class="btn btn-danger" data-user-id="${user.userId}" type="button"
                            data-username="${user.username}" onclick="deleteUserBtnClicked(this)">
                            Delete User
                        </button>
                    </div>
                </form>
                `);
                $('#findUserBtn span').remove();
                setupForm();
            }).fail(() => {
                $('#userManageActions').html(`<p class="text-center my-2">No User Found</p>`);
                $('#findUserBtn span').remove();
            });
        });

        new bootstrap.Modal('#deleteUserModal');

        $('#deleteUserConfirmBtn').on('click', function () {
            $.post('<?= BASE_URL ?>/admin/ajax_delete_user.php', { userId: $(this).data('userId') })
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

        function setupForm() {
            $('#userEditingSwitch').on('change', function () {
                if (this.checked) {
                    $('#editUserForm .editable').removeAttr('disabled');
                    $('#editUserSaveBtn').show();
                } else {
                    $('#editUserForm .editable').attr('disabled', 1);
                    $('#editUserSaveBtn').hide();
                }
            });

            $('#editUserForm').on('submit', event => {
                event.stopPropagation();
                event.preventDefault();

                $.post('<?= BASE_URL ?>/admin/edit_user.php', {
                    userId: $('#MUuserId').val(),
                    username: $('#MUusername').val(),
                    email: $('#MUemail').val(),
                    type: $('#MUtype').val(),
                }).done(() => {
                    $('.toast-container').append(`
                       <div id="userUpdateToast" class="toast text-bg-success" role="alert" aria-live="polite" aria-atomic="true">
                           <div class="toast-body">User updated successfully</div>
                       </div>
                    `);
                    $('#userEditingSwitch').trigger('click');
                }).fail(({ responseText }) => {
                    $('.toast-container').append(`
                       <div id="userUpdateToast" class="toast text-bg-danger" role="alert" aria-live="polite" aria-atomic="true">
                           <div class="toast-body">User was not updated: ${responseText}</div>
                       </div>
                    `);
                }).always(() => {
                    bootstrap.Modal.getInstance('#articleRemoveModal').hide();
                    $('#userUpdateToast').on('hidden.bs.toast', function () { $(this).remove(); });
                    const toast = new bootstrap.Toast($('#userUpdateToast'), { delay: 3000 });
                    toast.show();
                });

            });
        }


    });

    function deleteUserBtnClicked(e) {
        $('#deleteUserConfirmBtn').data('userId', $(e).data('userId'));
        $('#deleteUserConfirmBtn').data('username', $(e).data('username'));
        bootstrap.Modal.getInstance('#deleteUserModal').show();
    }

</script>