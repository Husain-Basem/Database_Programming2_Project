<?php

include_once '../prelude.php';

$user = User::from_username($_SESSION['username']);

include PROJECT_ROOT . '/header.html';

?>

<div class="container">
  <h1>User Profile</h1>


  <div class="flex-column flex-sm-row d-flex gap-5">
    <ul class="nav nav-pills flex-sm-column" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-tab-pane" type="button"
          role="tab" aria-controls="info-tab-pane" aria-selected="true">Information</button>
      </li>
      <?php
      if ($user->is_author() || $user->is_admin()) {
        echo '
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-tab-pane" type="button" role="tab" aria-controls="desc-tab-pane" aria-selected="false">Change Description</button>
  </li>';
      }
      ?>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-tab-pane" type="button"
          role="tab" aria-controls="email-tab-pane" aria-selected="false">Change Email</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-tab-pane"
          type="button" role="tab" aria-controls="password-tab-pane" aria-selected="false">Change Password</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete-tab-pane" type="button"
          role="tab" aria-controls="delete-tab-pane" aria-selected="false">Delete Account</button>
      </li>
    </ul>
    <div class="vr"></div>
    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
        <h2>User Information</h2>
        <dl>
          <dt>First Name</dt>
          <dd>
            <?= $user->firstName ?>
          </dd>
          <dt>Last Name</dt>
          <dd>
            <?= $user->lastName ?>
          </dd>
          <dt>Username</dt>
          <dd>
            <?= $user->userName ?>
          </dd>
          <dt>Email</dt>
          <dd>
            <?= $user->email ?>
          </dd>
          <dt>Country</dt>
          <dd>
            <?= $user->country ?>
          </dd>
          <dt>Date Registered</dt>
          <dd>
            <?= $user->date ?>
          </dd>
          <?php if ($user->is_author() || $user->is_admin()) {
            echo '
          <dt>Author Description</dt>
          <dd>' . $user->description . '</dd>
          ';
          } ?>
        </dl>
      </div>
      <div class="tab-pane fade" id="desc-tab-pane" role="tabpanel" aria-labelledby="desc-tab" tabindex="0">
        <h2>Change Description</h2>
        <form action="change_desc.php" method="post" class="needs-validation" novalidate>
          <div class="form-floating mb-3">
            <textarea class="form-control" name="desc" id="desc" style="height: 200px;"
              placeholder="place your description" required><?= $user->description ?></textarea>
            <label for="desc">Author Description</label>
            <div class="invalid-feedback">Description must not be empty</div>
          </div>
          <button class="btn btn-primary" type="submit" value="submitted" name="submitted">Submit</button>
        </form>
      </div>
      <div class="tab-pane fade" id="email-tab-pane" role="tabpanel" aria-labelledby="email-tab" tabindex="0">
        <h2>Change Email</h2>
        <form action="change_email.php" method="post" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="email">New Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
            <div class="invalid-feedback">Enter a valid email</div>
          </div>
          <button class="btn btn-primary" type="submit" value="submitted" name="submitted">Submit</button>
        </form>
      </div>
      <div class="tab-pane fade" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
        <h2>Change Password</h2>
        <form action="change_password.php" method="post" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="old_password">Old Password</label>
            <input type="password" class="form-control" name="old_password" id="old_password" required>
            <div class="invalid-feedback">Please provide your current password</div>
          </div>
          <div class="mb-3">
            <label for="new_password">New Password</label>
            <input type="password" minlength="8" class="form-control" name="new_password" id="new_password" required>
            <div class="invalid-feedback">Please provide a new password at least 8 characters long</div>
          </div>
          <div class="mb-3">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
            <div class="invalid-feedback">Passwords must match</div>
          </div>
          <button class="btn btn-primary" type="submit" value="submitted" name="submitted">Submit</button>
        </form>
      </div>
      <div class="tab-pane fade" id="delete-tab-pane" role="tabpanel" aria-labelledby="delete-tab" tabindex="0">
        <h2>Delete Account</h2>
        <button type="button" class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#deleteConfirm">
          Delete Account </button>
      </div>
      <div class="modal fade" id="deleteConfirm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Account</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form action="delete_account.php" method="post">
                <label class="mb-3" for="submitted">Are you sure you want to delete your account? This action is not
                  reversible.</label>
                <button class="btn btn-danger" type="submit" name="submitted" value="submitted">Delete account
                  permanently</button>
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
  $(() => {
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

    $('#confirm_password').on('input', function () {
      if ($(this).val() != $('#new_password').val()) {
        $(this).addClass('is-invalid');
        this.setCustomValidity('Passwords must match')
      } else {
        $(this).removeClass('is-invalid');
        this.setCustomValidity('')
      }
    });
    $('#new_password').on('input', () => $('#confirm_password').trigger('input'));
  });
</script>

<?php
include PROJECT_ROOT . '/footer.html';
?>