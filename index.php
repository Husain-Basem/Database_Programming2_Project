<?php
declare(strict_types=1);
include_once 'prelude.php';

$pageTitle = 'News';
include 'header.html';

?>


<div class="container">
    <p>
        <?php echo "Database:";
var_dump(Database::getInstance()->mysqli->get_server_info()) ?>
        <br>
        <?php echo "PROJECT_ROOT: " . PROJECT_ROOT  ?>
        <br>
        <?php echo "BASE_URL: " . BASE_URL ?>
    </p>
</div>

<?php

include 'footer.html';

?>
