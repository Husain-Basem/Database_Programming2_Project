<?php
include_once '../prelude.php';

// Get search criteria from form submission
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];
$query = $_GET['q'];
$author = $_GET['author'];


// Build SQL query based on search criteria
$sql = "SELECT Articles.*, CONCAT(Users.firstName, ' ', Users.lastName) as author
        FROM Articles JOIN Users on (Articles.writtenBy = Users.userId) 
        WHERE published = 1 and approved = 1";

// search using fulltext index
if (!empty($query)) {
    $sql .= " and match (title,content) against ('$query')";
}

if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND Articles.date BETWEEN '$start_date' AND '$end_date'";
}

if (!empty($author)) {
    $sql .= " AND CONCAT(Users.firstName, ' ', Users.lastName) like '%$author%'";
}

$sql .= " ORDER BY Articles.date DESC";

$pagination = new Pagination(10, $sql);


// use the website header
$pageTitle = 'Search Articles';
include PROJECT_ROOT . '/header.html'; ?>

<div class="container">
    <h4>Search results for: 
        '<?= $_GET['q'] ?>'
    </h4>
    <div class="row">
        <div class="col-12 col-lg-10 mb-4">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            More search Options
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <form action="search.php" method="GET">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" type="text" name="q" id="title" placeholder
                                                value="<?= $_GET['q'] ?>">
                                            <label for="title">Search query</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" type="text" name="author" id="author"
                                                placeholder value="<?= $_GET['author'] ?>">
                                            <label for="author">Author</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" type="date" name="start_date" id="start_date"
                                                placeholder
                                                value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '1970-01-01' ?>">
                                            <label for="start_Date">Start Date:</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" type="date" name="end_date" id="end_date"
                                                placeholder value="<?= $_GET['end_date'] ?>">
                                            <label for="end_Date">End Date:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($pagination->get_total_entries() > 0) { ?>
        <div class="d-flex gap-3" style="align-items: first baseline;">
            Page:
            <?= $pagination->pagination_controls(null, $_SERVER['QUERY_STRING']); ?>
        </div>
    <?php } ?>
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
            echo '
<div class="col-12 col-lg-10">
<div class="card mb-3">
  <div class="row g-0">
    <div class="col-md-4">
<div class="card-img ratio" style="background-image: url(' . $article->thumbnail . ')" role="img"></div>
    </div>
    <div class="col-12 col-md-8">
      <div class="card-body">
        <h5 class="card-title">' . $article->title . '</h5>
        <p class="card-text">' . substr(strip_tags($article->content), 0, 170) . '...</p>
        <p class="card-text">
            <span class="badge rounded-pill text-bg-secondary vertical-align-middle">
                ' . $article->display_category() . '
            </span>
            <small class="ms-2 text-muted">By ' . $article->author . '</small>
            <small class="ms-2 text-muted">Read time ' . $article->readTime . ' min</small>
        </p>
        <a href="' . BASE_URL . '/displayNews/article.php?id=' . $article->articleId . '" class="btn btn-primary">Read More</a>
      </div>
    </div>
  </div>
</div>
</div>
        ';
        }
    }
    ?>
    <?php if ($pagination->get_total_entries() > 0) { ?>
        <div class="d-flex gap-3" style="align-items: first baseline;">
            Page:
            <?= $pagination->pagination_controls(null, $_SERVER['QUERY_STRING']); ?>
        </div>
    <?php } ?>
</div>


<?php include PROJECT_ROOT . '/footer.html' ?>