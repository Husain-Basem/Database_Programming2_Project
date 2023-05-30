<?php

include_once '../prelude.php';

$cat = $_GET['c'];
if ($cat == 'local')
    $pageTitle = 'Local News';
if ($cat == 'international')
    $pageTitle = 'International News';
if ($cat == 'economy')
    $pageTitle = 'Economy News';
if ($cat == 'tourism')
    $pageTitle = 'Tourism';
include_once PROJECT_ROOT . '/header.html';
?>

<div class="container">
    <h1>
        <?= $pageTitle ?>
    </h1>

    <!--  display news list -->

</div>

<?php
include_once PROJECT_ROOT . '/footer.html';
?>