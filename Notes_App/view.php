<?php
session_start();

include __DIR__."/../Notes_App_src/utility/user_handling.php";
include __DIR__."/../Notes_App_src/utility/util.php";
include_once __DIR__ . '\..\Notes_App_src\utility\pdo.php';

// Draw record
if (! isset($_GET['id'])) {
    $_SESSION['error'] = "No record found.";
    header("Location: index.php");
    return;
}

if (isset($dbsession)) {
    $results = $dbsession->prepare("SELECT title, summary 
                                             FROM ideas 
                                             WHERE id =:id AND user_id = :uid");
    $results->execute(array(
            ':id' => $_GET['id'],
            ':uid' => $_SESSION['user_id']
        )
    );
    $results = $results->fetch(PDO::FETCH_ASSOC);

    // Assign Idea variables
    if ($results != false) {
        $idea_name = isset($results["title"]) ? $results["title"] : false;
        $idea_summary = isset($results["summary"]) ? $results["summary"] : false;
    } else {
        $_SESSION['error'] = "Idea not found.";
        header("Location: index.php");
        return;
    }

    // get thoughts
    $thoughts = $dbsession->prepare("SELECT title, body 
                                                   FROM thoughts 
                                                   WHERE idea_id = :id");
    $thoughts->execute(array(
            ':id' => $_GET['id'])
    );
    $thoughts = $thoughts->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Robin Shaw Resume Viewer</title>
    <?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
    <h1><?= htmlentities($idea_name) ?></h1>
    <p class="view-idea-summary"><?= htmlentities($idea_summary) ?></p>
    <div class="view-idea">
        <h3>Thoughts</h3>
        <div id="position_fields">
            <table>
            <?php
            // write each of the positions, if it exists
            $left_cell = true;
            if ($thoughts != false) {
                foreach($thoughts as $item) {
                    $t = $item['title'];
                    $b = $item['body'];

                    if ($left_cell) {
                        echo "<tr><td>
                                <p><b>$t</b></p>
                                <p>$b</p>
                            </td>";
                        $left_cell = false;
                    } else {
                        echo "<td>
                                <p><b>$t</b></p>
                                <p>$b</p>
                            </td></tr>";
                        $left_cell = true;
                    }
                } // end foreach
            }
            ?>
            </table>
        </div>
    </div>

    <a href="index.php">Return to index</a>
</div>
</body>
</html>
