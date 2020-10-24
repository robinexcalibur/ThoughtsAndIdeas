<?php
session_start();

// connect to database
include_once __DIR__ . '\..\Notes_App_src\utility\pdo.php';

// get data, make table
if (isset($dbsession) and isset($_SESSION['user_id'])) {
    $results = $dbsession->prepare("SELECT id, title, summary FROM ideas WHERE user_id=:uid");
    $results->execute(array(
        ':uid' => $_SESSION['user_id']
    ));
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Thoughts and Ideas by Robin Shaw</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
    <div class="container">
        <h1>Thoughts and Ideas</h1>
        <h3>a simple note taking app</h3>
        <br>
        <p> Welcome to Thoughts and Ideas, where you can write and see ideas with connected thoughts.</p>

        <?php
            include __DIR__."/../Notes_App_src/utility/message_handling.php";

            if (!isset($results)) { //user is probably not logged in
                echo "<p>Please Sign Up or Log In.</p>";
            } else if ($results->rowCount() === 0) { // user probably doesn't have ideas
                echo "<p>Write your first idea!</p>";
            } else {
                echo "Hello, <b>".htmlentities($_SESSION['username'])."</b>!";
                // make table head
                echo '<p><table>';
                echo '<tr>
                        <th>View Idea</th>
                        <th>Summary</th>';
                if (isset($_SESSION['user_id'])) {
                    echo "<th></th>";
                }
                echo '</tr>';

                // make table rows
                while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr><td><a href='view.php?id=".$row['id']."'>".htmlentities($row['title'])."</a</td>";

                    echo "<td>";
                    if(strlen(htmlentities($row['summary'])) > 300) {
                        echo substr(htmlentities($row['summary']), 0, 300)."...";
                    } else {
                        echo htmlentities($row['summary']);
                    }
                    echo "</td>";

                    if (isset($_SESSION['user_id'])) {
                        echo "<td><a href='edit.php?id=" . $row['id'] . "''>Edit</a> ";
                        echo "<a href='delete.php?id=".$row['id']."'>Delete</a></tr>";
                    }
                }
                echo '</table></p>';

            }
        ?>
        <p><a href="add_idea.php">+ Add New Thought</a></p>

        <?php
            // Offer login or logout
            if (isset($_SESSION['user_id'])) {
                echo '<p><a href="logout.php">Logout</a></p>';
            } else {
                echo '<p><a href="login.php">Please Log In</a> <a href="sign_up.php">Or Sign Up</a></p>';
            }
        ?>
    </div>
</body>

