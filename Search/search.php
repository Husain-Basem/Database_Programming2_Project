


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
</form>
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

