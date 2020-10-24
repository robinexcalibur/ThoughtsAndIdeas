<?php
// Display error message
if ( isset($_SESSION['error']) ) {
    echo('<p class="error">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}

// Display message
if ( isset($_SESSION['message'])) {
    echo('<p class="message">'.htmlentities($_SESSION['message'])."</p>\n");
    unset($_SESSION['message']);
}