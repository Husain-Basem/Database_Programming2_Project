


<!-- News Articles Grid -->

    <?php
    include_once '../prelude.php';
// use the website header
$pageTitle = $article['title'];
include PROJECT_ROOT . '/header.html'; ?>


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
                                    <input class="form-control" type="date" name="start_date" id="start_date" placeholder
                                        value="<?= $_GET['start_date'] ?>" required>
                                    <label for="start_Date">Start Date:</label>
                                    <div class="invalid-feedback" id="start_dateErr">Start date must not be empty</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="date" name="end_date" id="end_date" placeholder
                                        value="<?= $_GET['end_date'] ?>" required>
                                    <label for="end_Date">End Date:</label>
                                    <div class="invalid-feedback" id="end_dateErr">End date must not be empty</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" name="title" id="title" placeholder
                                        value="<?= $_GET['title'] ?>" pattern="[a-zA-Z0-9._-]{3,}" required>
                                    <label for="title">Title</label>
                                    <div class="invalid-feedback" id="titleErr">Title must be at least 3
                                        charachters (letters, numbers,&emsp;.&nbsp;_&nbsp;-&nbsp;)</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" name="author" id="author"
                                        minlength="8" placeholder value="<?= $_GET['author'] ?>" pattern="[a-zA-Z0-9._-]{3,}" required>
                                    <label for="password">Author</label>
                                    <div class="invalid-feedback" id="passwordErr">
                                        Author must be at least 3 characters long </div>
                                </div>
                            </div> 
                        </div>

			<div class="row">
			    <div class="col-sm-6">
                                <div class="form-check">
                                      <input class="form-check-input" type="checkbox" name="most_read" id="most_read">
  				      <label class="form-check-label" for="most_read">Most Read:</label>
  			              </label>
                                </div>
                            </div>
			</div>
                        <br>
                        <button class="w-100 btn btn-primary" type="submit">Search</button>
                        <input type="text" name="submitted" value="submitted" hidden>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
// Get search criteria from form submission
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];
$title = $_GET['title'];
$author = $_GET['author'];
$most_read = isset($_GET['most_read']) ? 1 : 0;


// Build SQL query based on search criteria
$sql = "SELECT * FROM articles WHERE";

if (!empty($title)) {
	$sql .= " (title LIKE '%$title%' OR short_description LIKE '%$title%')";
}

if (!empty($start_date) && !empty($end_date)) {
	$sql .= " AND date_published BETWEEN '$start_date' AND '$end_date'";
}

if (!empty($author)) {
	$sql .= " AND author = '$author'";
}

if ($most_read) {
	$sql .= " ORDER BY views DESC";
} else {
	$sql .= " ORDER BY date_published DESC";
}

$sql .= " LIMIT 10";


// Check if search results were found
if (mysqli_num_rows($result) == 0) {
	echo "<p>No results found for your search criteria.</p>";
} else {
	// Loop through search results and display them in a grid format (similar to Home Page)
	while ($row = mysqli_fetch_assoc($result)) {
		echo "<div class='news-item'>";
		echo "<h2'>" . $row['title'] . "</h2>";
		echo "<p>" . $row['short_description'] . "</p>";
		echo "<img src='" . $row['image_url'] . "' alt='" . $row['title'] . "'>";
		echo "<a href='news.php?id=" . $row['id'] . "'>Read More...</a>";
		echo "</div>";
	}
}

?>
<?php
    $db = Database::getInstance();
    // Check for errors
    if ($db->mysqli->connect_errno) {
        die("Connection failed: " . $db->mysqli->connect_error);
    }


    $sql = "SELECT * FROM Articles WHERE articleId = $article_id ";
    $result = $db->query($sql);
    if (!$result) {
        die("Error retrieving article data: " . $db->mysqli->error);
    }

// Retrieve the article data
    $article = $result->fetch_assoc();
    // Loop through news articles and display them in a grid format
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='news-item'>";
        echo "<h2>" . $row['title'] . "</h2>";
        echo "<p>" . $row['short_description'] . "</p>";
        echo "<img src='" . $row['image_url'] . "' alt='" . $row['title'] . "'>";
        echo "<a href='news.php?id=" . $row['id'] . "'>Read More...</a>";
        echo "</div>";
    }

    // Close database connection
    mysqli_close($db);
    ?>
<?php include PROJECT_ROOT . '/footer.html' ?>

