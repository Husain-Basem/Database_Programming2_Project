<?php
include_once '../prelude.php';

// Get search criteria from form submission
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];
$title = $_GET['title'];
$author = $_GET['author'];


// Build SQL query based on search criteria
$sql = "SELECT * FROM Articles WHERE 1=1";

if (!empty($title)) {
    $sql .= " and match (title,content) against ('$title')";
}

if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND date BETWEEN '$start_date' AND '$end_date'";
}

if (!empty($author)) {
    $sql .= " AND author like '%$author%'";
}

$sql .= " ORDER BY date DESC";

$pagination = new Pagination(10, $sql);


// use the website header
$pageTitle = 'Search Articles';
include PROJECT_ROOT . '/header.html'; ?>

?>

<!-- Search Bar -->
<!--<form action="search.php" method="GET">
    <input type="text" name="query" placeholder="Search News...">
    <button type="submit">Search</button>
</form>-->
<!--
<form action="search.php" method="GET">
    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" id="start_date">
    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" id="end_date">
    <br>
    <label for="title">Title:</label>
    <input type="text" name="title" id="title">
    <br>
    <label for="author">Author:</label>
    <input type="text" name="author" id="author">
    <br>
    <label for="most_read">Most Read:</label>
    <input type="checkbox" name="most_read" id="most_read">
    <br>
    <button type="submit">Search</button>
</form>-->


<div class="container">
    <div class="row">
        <div class="col-sm-12 col-md-8 col-lg-6 mx-auto">
            <?= $alert ?>
            <div class="card">
                <div class="card-header">
                    <h1>Search</h1>
                </div>
                <div class="card-body p-3 p-lg-5">
                    <form action="search.php" method="GET">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="date" name="start_date" id="start_date"
                                        placeholder value="<?= $_GET['start_date'] ?>" >
                                    <label for="start_Date">Start Date:</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="date" name="end_date" id="end_date" placeholder
                                        value="<?= $_GET['end_date'] ?>" >
                                    <label for="end_Date">End Date:</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" name="title" id="title" placeholder
                                        value="<?= $_GET['title'] ?>" >
                                    <label for="title">Title</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" name="author" id="author" minlength="8"
                                        placeholder value="<?= $_GET['author'] ?>" 
                                        >
                                    <label for="author">Author</label>
                                </div>
                            </div>
                        </div>

                        <button class="w-100 btn btn-primary" type="submit">Search</button>
                        <input type="text" name="submitted" value="submitted" hidden>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Check if search results were found
    if ($pagination->get_total_entries() == 0) {
        echo "<p>No results found for your search criteria.</p>";
    } else {
        // Loop through search results and display them in a grid format (similar to Home Page)
        $articles = $pagination->get_page(null, function ($row) {
            return Article::__set_state($row);
        });

        foreach ($articles as $article) {
            echo "<div class='card-body'>";
            echo "<h2'>" . $article->title . "</h2>";
            echo "<p>" . $article->author . "</p>";
            echo "<img width=100px src='" . $article->thumbnail . "' alt='" . $article->title . "'>";
            echo '<a href="' . BASE_URL . '/displayNews/article.php?id=' . $article->articleId . '">Read More...</a>';
            echo "</div>";
        }
    }
    ?>
</div>


<?php include PROJECT_ROOT . '/footer.html' ?>