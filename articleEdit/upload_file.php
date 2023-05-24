<?php

include_once '../prelude.php';

$file = File::upload_file('myFile', false, 1, 1);

if ($file == null) {
    echo 'ERROR';
} else {
    echo $file->get_url();
}
