<?php

include '../prelude.php';

$user = User::from_username($_SESSION['username']);
$success = $user->delete_user();

if ($success) {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['toasts'][] = array('type' => 'success', 'msg' => 'Account was deleted');
} else {
    $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Account was not deleted');
}

session_write_close();
header('Location: ' . BASE_URL . '/index.php');
