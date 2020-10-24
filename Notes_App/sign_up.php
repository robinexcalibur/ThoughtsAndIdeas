<?php // Do not put any HTML above this line
session_start();

// connect to database
include_once __DIR__ . '\..\Notes_App_src\utility\pdo.php';

if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';

// Check to see if we have some POST data, if we do process it
if (isset($_POST['signup']) && isset($dbsession)) {
    $username = isset($_POST['username']) ? $_POST['username'] : false;
    $password = isset($_POST['password']) ? $_POST['password'] : false;

    if (strlen($username) < 1 || strlen($password) < 1) { // no username or password is given
        $_SESSION['error'] = "User name and password are required";
        header("Location: sign_up.php");
        return;
    }

    // Check password length
    if (strlen($password) < 10) {
        $_SESSION['error'] = "Password must be at least 10 characters long.";
        header("Location: sign_up.php");
        return;
    }

    // check username doesn't have spaces
    if (strpos($username, ' ') !== false) {
        $_SESSION['error'] = "Spaces are not allowed in the username.";
        header("Location: sign_up.php");
        return;
    }

    // check if username is already in use
    $find_username = $dbsession->prepare('SELECT user_id FROM users WHERE name = :nm');
    $find_username->execute(array(
        ':nm' => $username));
    $find_username = $find_username->fetch(PDO::FETCH_ASSOC);
    if ($find_username !== false) {
        $_SESSION['error'] = "User name is already in use.";
        header("Location: sign_up.php");
        return;
    }

    // make password
    $salted_password = hash('md5', $salt.$password);

    $stmt = $dbsession->prepare('INSERT INTO users (name, password) 
                                              VALUES ( :nm, :pw)');
    $stmt->execute(array(
            ':nm' => $username,
            ':pw' => $salted_password)
    );

    $user_id = $dbsession->lastInsertId(); // the user_id generated for our last profile

    if ( $user_id !== false ) { // successful login/sign up!
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] =  $user_id;
        $_SESSION['message'] =  "Sign up successful. Welcome, ".htmlentities($username).".";

        header("Location: index.php");
        return;
    } else { // wrong password
        $_SESSION['error'] = "Something went wrong.";
        error_log("Sign Up fail");

        header("Location: sign_up.php");
        return;
    }

}

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>Thoughts and Ideas - Sign up</title>
</head>
<body>
<div class="container">
    <h1>Welcome to Thoughts and Ideas!</h1>
    <?php
    // Display error message
    if ( isset($_SESSION['error']) ) {
        echo('<p class="error">'.htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
    }
    ?>
    <form method="POST">
        <label for="username" class="add-label">Username: </label>
        <input type="text" name="username" id="username" class="add-text"><br/>
        <label for="password" class="add-label">Password</label>
        <input type="password" name="password" id="password" class="add-text"><br/>
        <input type="submit" onclick="return doValidate();" name="signup" value="Sign Up">
        <input type="submit" name="cancel" value="Cancel">
    </form>
    <script>
        function doValidate() {
            console.log('Validating...');
            try {
                let pw = document.getElementById('password').value;
                let email = document.getElementById('username').value;
                console.log("Validating pw="+pw+" email="+email);

                if (pw == null || pw == "" || email == null || email == "") {
                    alert("Both fields must be filled out.");
                    return false;
                }
                return true;
            } catch(e) {
                return false;
            }
        }
    </script>
    <p>
    </p>
</div>
</body>
