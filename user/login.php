<?php

include_once '../prelude.php';

if (isset($_POST['submitted'])) {
    $success = User::check_credentials($_POST['username'], $_POST['password']);
    if ($success) {
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['userId'] = User::from_username($_POST['username'])->userId;
        $_SESSION['toasts'][] = array('type' => 'success', 'msg' => 'Successfully logged in');
        session_write_close();
        if (isset($_POST['redirect'])) {
            header('Location: ' . $_POST['redirect']);
        } else {
            header('Location: ' . BASE_URL . '/index.php');
        }
    } else {
        $error = '
<div class="alert alert-danger" role="alert">
    Invalid Credentials.
</div>
';
    }

}

include PROJECT_ROOT . '/header.html';

?>

<div class="container">

    <?= $error ?>
    <div class="row h-100">
        <div class="col-sm-12 col-md-8 col-lg-5 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h1>Login</h1>
                </div>
                <div class="card-body">
                    <form method="post" class="mb-4">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="username" id="username" placeholder required
                                value="<?= $_POST['username'] ?>">
                            <label for="username">Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" name="password" id="password" class="form-control" placeholder
                                required>
                            <label for="password">Password</label>
                        </div>

                        <button class="w-100 btn btn-primary" type="submit">Login</button>
                        <input type="text" name="submitted" value="submitted" hidden>
                        <?php if (isset($_GET['redirect'])) {
                            echo '<input type="text" name="redirect" value="' . $_GET['redirect'] . '" hidden>';
                        }
                        ?>
                    </form>
                    <span class="text-muted">Not registered?
                        <a href="register.php" class="link-primary ms-1">Create an account</a> </span>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
include PROJECT_ROOT . '/footer.html';
?>