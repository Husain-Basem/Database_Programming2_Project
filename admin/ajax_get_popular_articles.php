<?php

include '../prelude.php';

if (isset($_GET['num']))
    $numArticles = $_GET['num'];
else
    $numArticles = 10;
if (isset($_GET['dateBegin']))
    $dateBegin = $_GET['dateBegin'];
else
    $dateBegin = '1970-01-01';
if (isset($_GET['dateEnd']))
    $dateEnd = $_GET['dateEnd'];
else
    $dateEnd = date('Y-m-d');

$articles = Article::get_popular_articles($numArticles, $dateBegin, $dateEnd);

echo json_encode($articles);