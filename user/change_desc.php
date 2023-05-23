<?php

include '../prelude.php';

$desc = trim($_POST['desc']);

if (empty($desc)) {
    $errors[] = 'Description must not be empty';
}

$user = User::from_username($_SESSION['username']);
$user->description = $desc;
$success = $user->update_user();

if ($success && empty($errors)) {
    $_SESSION['toasts'][] = array('type' => 'success', 'msg' => 'Successfully updated description');
} else {
    $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Descirption was not updated');
}

session_write_close();
header('Location: ' . BASE_URL . '/user/profile.php');
