<?php
// Demand logged in
if ( ! isset($_SESSION['user_id']) || strlen($_SESSION['user_id']) < 1  ) {
    die('ACCESS DENIED');
}

// If the user requested logout move to logout page
if ( isset($_POST['logout']) ) {
    header('Location: logout.php');
    return;
}

// if the user cancels the action
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
}