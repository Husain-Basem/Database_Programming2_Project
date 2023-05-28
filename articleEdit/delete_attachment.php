<?php

include_once '../prelude.php';

$result = File::delete_file($_POST['fileId']);

if ($result) {
    http_response_code(200);
} else {
    http_response_code(400);
}