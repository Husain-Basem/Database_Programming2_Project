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

    if (isset($_POST['type']) && $_POST['type'] = 'author')
        $type = 'AUTHOR';
    else
        $type = 'VIEWER';

    $username = $_POST['username'];
    if (empty($errors)) {
        $success = User::register_user(
            $fname,
            $lname,
            $username,
            $password,
            $email,
            $type,
            null,
            $country
        );
    }

    if ($success && empty($errors)) {

        // if creating from admin panel
        if (isset($_POST['type']) && $_POST['type'] = 'author') {
            $_SESSION['toasts'][] = array('type' => 'success', 'msg' => "Succesfully created user '$username'");
            unset($_POST);
            header('Location: ' . BASE_URL . '/admin/admin_panel.php', true, 200);
            $fragment = '#register-author-tab';
            require_once PROJECT_ROOT . '/admin/admin_panel.php';
            exit;
        }

        // log in user and redirect
        $_SESSION['username'] = $username;
        $_SESSION['userId'] = User::from_username($username)->userId;
        $_SESSION['toasts'][] = array('type' => 'success', 'msg' => "Succesfully created user '$username'");
        session_write_close();
        header('Location: ' . BASE_URL . '/index.php');
    } else {
        $alert = '<div class="alert alert-danger" role="alert"><h3>Invalid submission:</h3><ul>';
        foreach ($errors as $el => $msg) {
            $alert .= '<li>' . $msg . '</li>';
        }
        $alert .= '</ul></div>';
    }

}


// if creating from admin panel
if (isset($_POST['type']) && $_POST['type'] = 'author') {
    header('Location: ' . BASE_URL . '/admin/admin_panel.php', true, 200);
    $fragment = '#register-author-tab';
    require_once PROJECT_ROOT . '/admin/admin_panel.php';
    exit;
}

include PROJECT_ROOT . '/header.html';
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12 col-md-8 col-lg-6 mx-auto">
            <?= $alert ?>
            <div class="card">
                <div class="card-header">
                    <h1>Register</h1>
                </div>
                <div class="card-body p-3 p-lg-5">
                    <form method="post" class="mb-4 <?= empty($errors) ? 'needs-validation' : 'was-validated' ?>"
                        novalidate>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" name="fname" id="fname" placeholder
                                        value="<?= $_POST['fname'] ?>" required>
                                    <label for="fname">First Name</label>
                                    <div class="invalid-feedback" id="fnameErr">First name must not be empty</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" name="lname" id="lname" placeholder
                                        value="<?= $_POST['lname'] ?>" required>
                                    <label for="lname">Last Name</label>
                                    <div class="invalid-feedback" id="lnameErr">Last name must not be empty</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" name="username" id="username" placeholder
                                        value="<?= $_POST['username'] ?>" pattern="[a-zA-Z0-9._-]{3,}" required>
                                    <label for="username">Username</label>
                                    <div class="invalid-feedback" id="usernameErr">Username must be at least 3
                                        characters (letters, numbers,&emsp;.&nbsp;_&nbsp;-&nbsp;)</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="email" name="email" id="email" placeholder
                                        value="<?= $_POST['email'] ?>" required>
                                    <label for="email">Email</label>
                                    <div class="invalid-feedback" id="emailErr">Please provide a valid email</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="password" name="password" id="password"
                                        minlength="8" placeholder required>
                                    <label for="password">Password</label>
                                    <div class="invalid-feedback" id="passwordErr">
                                        Password must be at least 8 characters long </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="password" name="passwordConfirm"
                                        id="passwordConfirm" placeholder required>
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
                        <button class="w-100 btn btn-primary" type="submit">Register</button>
                        <input type="text" name="submitted" value="submitted" hidden>
                    </form>
                    <span class="text-muted">Are you an article author? Send us an email to create your author
                        account.</span>
                </div>
            </div>
        </div>
    </div>
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
        $('#username').on('blur', function () {
            $.get('<?= BASE_URL ?>' + '/user/exists.php', { u: $(this).val() })
                .done((exists) => {
                    if (exists == "1") {
                        $(this).addClass('is-invalid');
                        this.setCustomValidity('Username is already taken')
                        $('#usernameErr').html('Username is already taken');
                    } else {
                        $(this).removeClass('is-invalid');
                        this.setCustomValidity('')
                        $('#usernameErr').html('Username must be at least 3 characters (letters,numbers,&emsp;.&nbsp;_&nbsp;-&nbsp;)');
                    }
                });
        });

        $('#passwordConfirm').on('input', function () {
            if ($(this).val() != $('#password').val()) {
                $(this).addClass('is-invalid');
                this.setCustomValidity('Passwords must match')
            } else {
                $(this).removeClass('is-invalid');
                this.setCustomValidity('')
            }
        });
        $('#password').on('input', () => $('#passwordConfirm').trigger('input'));

    });
</script>

<?php
include PROJECT_ROOT . '/footer.html';

?>