<?php

include_once '../prelude.php';

if ($_SESSION['username'] != null) {
    if (User::from_username($_SESSION['username'])->is_author()) {
        echo '
<li class="nav-item">
    <a class="nav-link" href="'. BASE_URL . '/articleEdit/author_panel.php">Author Panel</a>
</li>
';
    }
    echo '
<li class="nav-item">
    <a class="nav-link" href="'. BASE_URL . '/user/profile.php' . '">Hello '.$_SESSION['username'].'</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="'. BASE_URL . '/user/logout.php'. '">Logout</a>
</li>
';
} else {
    echo '
<li class="nav-item">
    <a class="nav-link" href="'. BASE_URL . '/user/register.php' . '">Register</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="'. BASE_URL . '/user/login.php'. '">Login</a>
</li>
';
}
