<?php

include_once '../prelude.php';

if ($_SESSION['username'] != null) {
    $user = User::from_username($_SESSION['username']);
    if ($user->is_author()) {
        echo '
        <li class="nav-item">
            <a class="nav-link" href="' . BASE_URL . '/articleEdit/author_panel.php">Author Panel</a>
        </li>
        ';
    } 
    if ($user->is_admin()) {
        echo '
        <li class="nav-item">
            <a class="nav-link" href="' . BASE_URL . '/admin/admin_panel.php">Admin Panel</a>
        </li>
        ';
    } 
    echo '
    <li class="nav-item">
        <a class="nav-link" href="' . BASE_URL . '/user/profile.php' . '">Hello ' . $_SESSION['username'] . '</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="' . BASE_URL . '/user/logout.php' . '">Logout</a>
    </li>
    ';
} else {
    // not logged in
    echo '
    <li class="nav-item">
        <a class="nav-link" href="' . BASE_URL . '/user/register.php' . '">Register</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="' . BASE_URL . '/user/login.php' . '">Login</a>
    </li>
    ';
}