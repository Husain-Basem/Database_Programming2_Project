<?php
include_once '../prelude.php';
?>

<div class="row">
    <div class="col-sm-12 col-lg-7">
        <?= $alert ?>
        <form action="<?= BASE_URL . '/user/register.php?redirect=' . urlencode($_SERVER['REQUEST_URI']) ?>"
            method="post" class="mt-3 <?= empty($errors) ? 'needs-validation' : 'was-validated' ?>" novalidate>
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
                            charachters (letters, numbers,&emsp;.&nbsp;_&nbsp;-&nbsp;)</div>
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
                        <input class="form-control" type="password" name="password" id="password" minlength="8"
                            placeholder required>
                        <label for="password">Password</label>
                        <div class="invalid-feedback" id="passwordErr">Password must be at least 8 characters long</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-floating mb-3">
                        <input class="form-control" type="password" name="passwordConfirm" id="passwordConfirm"
                            placeholder required>
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
            <input type="hidden" name="submitted" value="submitted">
            <input type="hidden" name="type" value="author">
        </form>
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
            $.get('<?= BASE_URL ?>' + '/user/ajax_exists.php', { u: $(this).val() })
                .done((exists) => {
                    if (exists == "1") {
                        $(this).addClass('is-invalid');
                        this.setCustomValidity('Username is already taken')
                        $('#usernameErr').html('Username is already taken');
                    } else {
                        $(this).removeClass('is-invalid');
                        this.setCustomValidity('')
                        $('#usernameErr').html('Username must be at least 3 charachters (letters,numbers,&emsp;.&nbsp;_&nbsp;-&nbsp;)');
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