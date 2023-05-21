<?php
declare(strict_types=1);
include_once '../prelude.php';

if (isset($_POST['submitted'])) {
    // TODO: verify input
    $username = $_POST['username'];
    $success = User::register_user(
        $_POST['fname'],
        $_POST['lname'],
        $_POST['username'],
        $_POST['password'],
        $_POST['email'],
        'VIEWER',
        null,
        $_POST['country']
    );

    if ($success) {
        $_SESSION['username'] = $username;
        header('Location: '.BASE_URL.'/index.php');
    }

    // TODO: error messages

}

include PROJECT_ROOT . '/header.html';
?>

<div class="container">
  <h1>Register</h1>
<form method="post" class="mt-3 w-75">
  <div class="row">
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="text" name="fname" id="fname" placeholder>
        <label for="fname">First Name</label>
    </div>
</div>
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="text" name="lname" id="lname" placeholder>
        <label for="lname">Last Name</label>
    </div>
    </div>
  </div>

  <div class="row">
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="text" name="username" id="username" placeholder>
        <label for="username">Username</label>
    </div>
</div>
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="email" name="email" id="email" placeholder>
        <label for="email">Email</label>
    </div>
    </div>
  </div>


  <div class="row">
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="password" name="password" id="password" placeholder>
        <label for="password">Password</label>
    </div>
</div>
    <div class="col">
    <div class="form-floating mb-3">
        <input class="form-control" type="password" name="passwordConfirm" id="passwordConfirm" placeholder>
        <label for="passwordConfirm">Confirm Password</label>
    </div>
    </div>
  </div>

<div class="form-floating mb-3">
<select class="form-select" name="country" id="country" value="Bahrain">
<option value="Bahrain" selected>Bahrain</option>
</select>
<label for="country">Country</label>
</div>  

<button class="w-100 btn btn-primary" type="submit">Submit</button>
<input type="text" name="submitted" value="submitted" hidden>
</form>
</div>

<?php
include PROJECT_ROOT . '/footer.html';

?>
