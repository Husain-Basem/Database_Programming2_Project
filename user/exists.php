<?php

include_once '../prelude.php';

if (isset($_GET['u'])) {
    if (User::username_exists($_GET['u'])) {
        header("HTTP/1.1 200 OK");
        echo '1';
    } else {
        header("HTTP/1.1 200 OK");
        echo '0';
    }
    exit;
} else {
    header("HTTP/1.1 400 Bad Request");
    exit;
}
