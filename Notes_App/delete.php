<?php // Do not put any HTML above this line
session_start();

include __DIR__."/../Notes_App_src/utility/user_handling.php";
include_once __DIR__ . '\..\Notes_App_src\utility\pdo.php';

if (!isset($dbsession)) {
    $_SESSION['error'] = "Database not found.";
    header("Location: index.php");
    return;
}
$title = false;
// Check if data exists
if (isset($_GET['id'])) {
    $data_exists = $dbsession->prepare("SELECT title FROM ideas WHERE user_id=:uid AND id=:id");
    $data_exists->execute(array(
            ':id'  => $_GET['id'],
            ':uid' => $_SESSION['user_id'])
    );

    $data_exists = $data_exists->fetch(PDO::FETCH_ASSOC);
    if ($data_exists != false) {
        $title = $data_exists['title'];
    } else {
        $_SESSION['error'] = "Idea not found.";
        header("Location: index.php");
        return;
    }
}

// Check to if user confirmed to delete
if ( isset($_POST['delete']) && isset($_POST['id']) ) {
    $stmt = $dbsession->prepare("DELETE FROM ideas WHERE id=:id");
    $stmt->execute(array(
            ':id' => $_POST['id'])
    );

    $_SESSION['message'] = "Idea successfully deleted.";
    header("Location: index.php");
    return;
}

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>Thoughts and Ideas - Delete</title>
</head>
<body>
<div class="container">
    <h2>Are you sure you want to delete <?= htmlentities($title)?>?</h2>
    <form method="POST" style="text-align: center">
        <input type="hidden" name="id" value="<?= htmlentities($_GET['id']) ?>" />
        <input type="submit" name="delete" value="Delete" class="red-button">
        <input type="submit" name="cancel" value="Cancel" class="green-button">
    </form>
</div>
</body>
