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
        <br>
        <?php User::register_user('bob', 'bobson', 'bob', '123', 'bob@bob.com', 'VIEWER', null, 'Bahrain') ?>
        <?php echo "Bob: ";
var_dump(User::from_username('bob')) ?>
        <br>
        <?= var_dump(User::username_exists('bob'), User::check_credentials('bob', '123')) ?>
       </p>
</div>

<?php

include 'footer.html';

?>
