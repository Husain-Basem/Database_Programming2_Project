<?php

include_once '../prelude.php';

$file = File::upload_file('myFile', false, $_POST['articleId'], $_POST['userId']);

if ($file == null) {
    header("HTTP/1.1 201 No Content");
    if ($_FILES['myFile']['error'] == UPLOAD_ERR_INI_SIZE)
        echo 'File too large';
    else
        echo 'Couldn\'t move uploaded file';
} else {
    echo $file->fileLocation;
}