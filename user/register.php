<?php
declare(strict_types=1);
include_once '../prelude.php';

if (isset($_POST['submitted'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $passwordConfirm = trim($_POST['passwordConfirm']);
    $country = trim($_POST['country']);
    if (empty($fname)) {
        $errors['fname'] = "First name must not be empty";
    }
    if (empty($lname)) {
        $errors['lname'] = "Last name must not be empty";
    }
    if (empty($username)) {
        $errors['username'] = "Username must not be empty";
    }
    if (User::username_exists($username)) {
        $errors['username'] = "Username is already used";
    }
    if (empty($password)) {
        $errors['password'] = "Password must not be empty";
    }
    if (empty($passwordConfirm)) {
        $errors['passwordConfirm'] = "Please enter your password again";
    }
    if ($password != $passwordConfirm) {
        $errors['passwordConfirm'] = "Passwords do not match";
    }
    if (empty($country)) {
        $errors['country'] = "Please choose your country";
    }

    $username = $_POST['username'];
    if (empty($errors)) {
        $success = User::register_user(
            $fname,
            $lname,
            $username,
            $password,
            $email,
            'VIEWER',
            null,
            $country
        );
    }

    if ($success && empty($errors)) {
        $_SESSION['username'] = $username;
        $_SESSION['toasts'][] = array('type' => 'success', 'msg' => "Succesfully created user '$username'");
        session_write_close();
        header('Location: '.BASE_URL.'/index.php');
    } else {
        $alert = '<div class="alert alert-danger" role="alert"><h3>Invalid submission:</h3><ul>';
        foreach ($errors as $el => $msg) {
            $alert .= '<li>'.$msg.'</li>';
        }
        $alert .= '</ul></div>';
    }

}

include PROJECT_ROOT . '/header.html';
?>

<div class="container">
  <h1>Register</h1>
  <?= $alert ?>
  <form method="post" class="mt-3 w-75 <?=empty($errors) ? 'needs-validation' : 'was-validated'?>" novalidate>
  <div class="row">
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="text" name="fname" id="fname" placeholder value="<?=$_POST['fname']?>" required>
        <label for="fname">First Name</label>
        <div class="invalid-feedback" id="fnameErr">First name must not be empty</div>
    </div>
</div>
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="text" name="lname" id="lname" placeholder value="<?=$_POST['lname']?>" required>
        <label for="lname">Last Name</label>
        <div class="invalid-feedback" id="lnameErr">Last name must not be empty</div>
    </div>
    </div>
  </div>

  <div class="row">
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="text" name="username" id="username" placeholder value="<?=$_POST['username']?>" required>
        <label for="username">Username</label>
        <div class="invalid-feedback" id="usernameErr">Username must not be empty</div>
    </div>
</div>
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="email" name="email" id="email" placeholder value="<?=$_POST['email']?>" required>
        <label for="email">Email</label>
        <div class="invalid-feedback" id="emailErr">Please provide a valid email</div>
    </div>
    </div>
  </div>


  <div class="row">
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="password" name="password" id="password" minlength="3" placeholder required>
        <label for="password">Password</label>
        <div class="invalid-feedback" id="passwordErr">Password must be at least 3 characters long</div>
    </div>
</div>
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="password" name="passwordConfirm" id="passwordConfirm" placeholder required>
        <label for="passwordConfirm">Confirm Password</label>
        <div class="invalid-feedback" id="passwordConfirmErr">Passwords must match</div>
    </div>
    </div>
  </div>

<div class="form-floating mb-3">
<select class="form-select" name="country" id="country" required>
<option value="Bahrain" selected>Bahrain</option>
</select>
<label for="country">Country</label>
<div class="invalid-feedback" id="countryErr">Select your country</div>
</div>  

<button class="w-100 btn btn-primary" type="submit">Submit</button>
<input type="text" name="submitted" value="submitted" hidden>
</form>
</div>

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
    $('#username').on('blur', function() {
        $.get('<?=BASE_URL?>' + '/user/exists.php', { u: $(this).val() })
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

    $('#passwordConfirm').on('input', function() {
      if ($(this).val() != $('#password').val()) {
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
      }
    });
    $('#password').on('input', ()=>$('#passwordConfirm').trigger('input'));

});
</script>

<?php
include PROJECT_ROOT . '/footer.html';

?>
