<?php

include_once '../prelude.php';

$file = File::upload_file('myFile', false, $_POST['articleId'], $_POST['userId']);

if ($file == null) {
    echo 'ERROR';
} else {
    echo $file->get_url();
}
