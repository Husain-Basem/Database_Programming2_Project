<?php

include '../prelude.php';

$email = trim($_POST['email']);

if (empty($email)) {
    $errors[] = 'Email must not be empty';
}

$user = User::from_username($_SESSION['username']);
$user->email = $email;
$success = $user->update_user();

if ($success && empty($errors)) {
    $_SESSION['toasts'][] = array('type' => 'success', 'msg' => 'Successfully updated email');
} else {
    $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Email was not updated');
}

session_write_close();
header('Location: ' . BASE_URL . '/user/profile.php');