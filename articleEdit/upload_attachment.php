<?php

include_once '../prelude.php';

$file = File::upload_file('myFile', true, $_POST['articleId'], $_POST['userId']);

if ($file == null) {
    header("HTTP/1.1 201 No Content");
    if ($_FILES['myFile']['error'] == UPLOAD_ERR_INI_SIZE)
        echo 'File too large';
    else
        echo 'Couldn\'t move uploaded file';
} else {
    header("HTTP/1.1 200 Ok");
    // header("Content-Type: application/json; charset=utf-8", false);
    echo json_encode(
        array(
            'fileId' => $file->fileId,
            'fileName' => $file->fileName,
            'fileUrl' => $file->get_url(),
        )
    );
}