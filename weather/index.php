<?php
declare(strict_types=1);
include_once '../prelude.php';

$pageTitle = 'Weather Forecast';
include '../header.html';

?>


<div class="container">
  <h2>Weather Forecast</h2>
  <iframe class="w-100" src="https://wttr.in"
    style="height: calc(100vh - 9rem)"></iframe>
</div>

<?php

include '../footer.html';

?>