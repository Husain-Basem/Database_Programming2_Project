<?php

include '../prelude.php';

$old_password = trim($_POST['old_password']);
$new_password = trim($_POST['new_password']);

if (User::check_credentials($_SESSION['username'], $old_password)) {


    $user = User::from_username($_SESSION['username']);
    $user->set_password($new_password);
    $success = $user->update_user();

    if ($success && empty($errors)) {
        $_SESSION['toasts'][] = array('type' => 'success', 'msg' => 'Successfully updated password');
    } else {
        $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Password was not updated');
    }

} else {
    // invalid old password
    $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Password was not updated: Old password is not correct');
}

session_write_close();
header('Location: ' . BASE_URL . '/user/profile.php');