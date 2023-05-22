<?php

include '../prelude.php';

session_unset();
session_destroy();

session_start();
$_SESSION['toasts'][] = array('type' => 'success', 'msg' => 'Successfully logged out.');

header("Location: ".BASE_URL."/index.php");
