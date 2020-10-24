<?php
session_start();

include __DIR__."/../Notes_App_src/utility/user_handling.php";
include __DIR__."/../Notes_App_src/utility/util.php";
include_once __DIR__ . '\..\Notes_App_src\utility\pdo.php';

if ( isset($_POST['add'])) {
    $idea_name =         isset($_POST["idea_name"])         ? $_POST["idea_name"]         : false;
    $idea_summary =      isset($_POST["idea_summary"])      ? $_POST["idea_summary"]      : false;
    $thought_names =     isset($_POST["thought_names"])     ? $_POST["thought_names"]     : false;
    $thought_summaries = isset($_POST["thought_summaries"]) ? $_POST["thought_summaries"] : false;
    $idea_id =           isset($_POST['idea_id'])           ? $_POST['idea_id']           : false;

    // check idea name and summary exist
    if (!$idea_name or !$idea_summary or strlen($idea_name) < 1 or strlen($idea_summary) < 1 or !$idea_id) {
        $_SESSION['error'] = "Idea name and summary required.";
        header("Location: edit.php?id=".htmlentities($idea_id));
        return;
    }

    // check all pos summaries have content
    if (!allStringEntiresExist($thought_names)) {
        $_SESSION['error'] = "Each Thought needs a name.";
        header("Location: edit.php?id=".htmlentities($idea_id));
        return;
    }

    // check all schools have content
    if (!allStringEntiresExist($thought_summaries)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?id=".htmlentities($idea_id));
        return;
    }


    // checks done, perform main profile insertion.
    if (isset($dbsession)) {
        $stmt = $dbsession->prepare('UPDATE ideas
                              SET title=:ttl, summary=:smy
                              WHERE id=:iid AND user_id=:uid');

    }
    $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':iid' => $idea_id,
            ':ttl' => $idea_name,
            ':smy' => $idea_summary,)
    );

    // perform thought insertion.
    $stmt = $dbsession->prepare("DELETE FROM thoughts WHERE idea_id=:iid");
    $stmt->execute(array(
        ':iid' => $idea_id
    ));

    for($i = 0; $i < sizeof($thought_names); $i++) {
        $stmt = $dbsession->prepare('INSERT INTO thoughts (idea_id, title, body) 
                                              VALUES ( :iid, :ttl, :smy)');
        $stmt->execute(array(
                ':iid' => $idea_id,
                ':ttl' => $thought_names[$i],
                ':smy' => $thought_summaries[$i])
        );
    }

    // return home!
    $_SESSION['message'] = "Record added";
    header("Location: index.php");
    return;

} else { // Draw record
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

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thoughts and Ideas - Edit</title>
    <?php require_once "bootstrap.php"; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <h1>Edit Idea</h1>
    <h3>Edit Your Idea below.</h3>
    <?php
    include __DIR__."/../Notes_App_src/utility/message_handling.php";
    ?>
    <form method="post">
        <div class="add-title">
            <input type="text" name="idea_name" id="idea_name" class="add-title" value="<?= htmlentities($idea_name) ?>">
            <br><br>
            <textarea rows="6" cols="90" name="idea_summary" id="idea_summary"><?= htmlentities($idea_summary) ?></textarea>
        </div>
        <div class="add-thought">
            <a href="#" id="add"> + </a> Add Thought
        </div>
        <br>
        <div id="thoughts">
            <?php
                // write each of the educations, if it exists
                if ($thoughts != false) {
                    foreach($thoughts as $item) {
                        $t = $item['title'];
                        $b = $item['body'];
                        echo "<div class='new-thought'>
                                <input type='text' name='thought_names[]' id='thought_names[]' value=".htmlentities($t)."><br><br>
                                <textarea name='thought_summaries[]' id='thought_summaries[]'>".htmlentities($b)."</textarea><br><br>
                                <a href='#' class='remove'>Remove</a>
                             </div>";
                    }
                }
            ?>

        </div>

        <br><br>

        <input type="hidden" name="idea_id" id="idea_id" value="<?= $_GET['id'] ?>">
        <input type="submit" name="add" value="Edit" class="green-button">
        <input type="submit" name="cancel" value="Cancel" class="red-button">
    </form>
</div>
<script>

    function addField(event, location, html) {
        event.preventDefault();
        $(location).append(html);
    }

    $(document).ready(function(){
        // add position
        let thoughts_location = '#thoughts'
        let thoughts_html =
            `<div class="new-thought">
                <input type="text" name="thought_names[]" id="thought_names[]" placeholder="Thought Name"><br><br>
                <textarea name="thought_summaries[]" id="thought_summaries[]">Jot down your thought!</textarea><br><br>
                <a href='#' class='remove'>Remove</a>
             </div>`

        $('#add').click(function(event){
            addField(event, thoughts_location, thoughts_html)
        });


        // remove field function
        $('#thoughts').on("click", ".remove", function(event) {
            event.preventDefault();
            $(this).parent('div').remove(); // removes the input field
        })


    });

</script>
</body>
</html>