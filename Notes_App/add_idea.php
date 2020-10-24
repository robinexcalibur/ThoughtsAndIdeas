<?php
session_start();

include __DIR__."/../Notes_App_src/utility/user_handling.php";
include __DIR__."/../Notes_App_src/utility/util.php";
include_once __DIR__ . '\..\Notes_App_src\utility\pdo.php';

if ( isset($_POST['add']) ) {
    $idea_name =         isset($_POST["idea_name"])         ? $_POST["idea_name"]         : false;
    $idea_summary =      isset($_POST["idea_summary"])      ? $_POST["idea_summary"]      : false;
    $thought_names =     isset($_POST["thought_names"])     ? $_POST["thought_names"]     : false;
    $thought_summaries = isset($_POST["thought_summaries"]) ? $_POST["thought_summaries"] : false;

    // check idea name and summary exist
    if (!$idea_name or !$idea_summary or strlen($idea_name) < 1 or strlen($idea_summary) < 1) {
        $_SESSION['error'] = "Idea name and summary required.";
        header("Location: add_idea.php");
        return;
    }

    // check all pos summaries have content
    if (!allStringEntiresExist($thought_names)) {
        $_SESSION['error'] = "Each Thought needs a name.";
        header("Location: add_idea.php");
        return;
    }

    // check all schools have content
    if (!allStringEntiresExist($thought_summaries)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add_idea.php");
        return;
    }


    // checks done, perform main profile insertion.
    if (isset($dbsession)) {
        $stmt = $dbsession->prepare('INSERT INTO ideas
                              (user_id, title, summary)
                              VALUES (:uid, :ttl, :smy)');

    }
    $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':ttl' => $idea_name,
            ':smy' => $idea_summary,)
    );

    // perform position insertion.
    $idea_id = $dbsession->lastInsertId(); // the user_id generated for our last profile

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

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thoughts and Ideas - New Idea</title>
    <?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>New Idea</h1>
<h3> Fill out Your Idea below.</h3>
<?php
    include __DIR__."/../Notes_App_src/utility/message_handling.php";
?>
<form method="post">
    <div class="add-title">
        <input type="text" name="idea_name" id="idea_name" class="add-title" placeholder="Title">
        <br><br>
        <textarea rows="6" cols="90" name="idea_summary" id="idea_summary">Summary: Give a broad overview of your idea!</textarea>
    </div>
    <div class="add-thought">
        <a href="#" id="add"> + </a> Add Thought
    </div>
    <br>
    <div id="thoughts"></div>

    <br><br>

    <input type="submit" name="add" value="Add" class="green-button">
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
