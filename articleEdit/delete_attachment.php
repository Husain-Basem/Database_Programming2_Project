<?php

include_once '../prelude.php';

$file = File::from_fileId($_POST['fileId']);
$result = unlink($file->get_absolute_fileLocation());
$result = $result && File::delete_file($_POST['fileId']);

if ($result) {
    http_response_code(200);
} else {
    http_response_code(400);
}