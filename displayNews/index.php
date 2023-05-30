<?php
declare(strict_types=1);
include_once '../prelude.php';

$pageTitle = 'News';
include PROJECT_ROOT . '/header.html';

?>
<a href="<?= BASE_URL . '/Search/search.php'?>">Search</a> <!-- Add link to Search Page -->

<div class="container">
<!--- some debugging  info. feel free to comment it out -->
    <p>
        <?php echo "Database: ";
var_dump(Database::getInstance()->mysqli->get_server_info()) ?>
        <br>
        <?php echo "PROJECT_ROOT: " . PROJECT_ROOT  ?>
        <br>
        <?php echo "BASE_URL: " . BASE_URL ?>
        <br>
        <?php echo "Bob: ";
var_dump(User::from_userId(1)) ?>
        <br>
        <?php 
        $articles = Article::search_articles('shop');
        foreach ($articles as $article) {
                echo '
                <img width=100px src="'.$article->thumbnail.'">
                ';
                echo $article->title;
        }
        ?>


       </p>
</div>

<?php

include PROJECT_ROOT . '/footer.html';

?>
