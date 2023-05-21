<?php

include_once '../prelude.php';

if (isset($_POST['submitted'])) {
    // TODO: validate input
    $success = User::check_credentials($_POST['username'], $_POST['password']);
    if ($success) {
        $_SESSION['username'] = $_POST['username'];
        header("Location: ".BASE_URL."/index.php");
    }

    // TODO: error messages
}

include PROJECT_ROOT . '/header.html';

?>

<div class="container">
  <h1>Login</h1>

<form method="post" class="mt-3 w-50">
    <div class="form-floating mb-3">
        <input class="form-control" type="text" name="username" id="username" placeholder>
        <label for="username">Username</label>
    </div>
   <div class="form-floating mb-3">
       <input type="password" name="password" id="password" class="form-control" placeholder>
       <label for="password">Password</label>
   </div>

<button class="w-100 btn btn-primary" type="submit">Submit</button>
<input type="text" name="submitted" value="submitted" hidden>
</form>

</div>

<?php
include PROJECT_ROOT . '/footer.html';
?>
