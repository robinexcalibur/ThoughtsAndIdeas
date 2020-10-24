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
if (isset($_POST['login']) && isset($dbsession)) {
    $username = isset($_POST['username']) ? $_POST['username'] : false;
    $password = isset($_POST['password']) ? $_POST['password'] : false;

    if (strlen($username) < 1 || strlen($password) < 1) { // no username or password is given
        $_SESSION['error'] = "User name and password are required";
        header("Location: login.php");
        return;
    }

    // check password/user combo
    $check = hash('md5', $salt.$password);

    $stmt = $dbsession->prepare('SELECT user_id, name FROM users WHERE name = :nm AND password = :pw');
    $stmt->execute(array(
            ':nm' => $username,
            ':pw' => $check));

    // see if row exists
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ( $row !== false ) { // successful login!
        $_SESSION['username'] = $row['name'];
        $_SESSION['user_id'] =  $row['user_id'];
        $_SESSION['message'] =  "Login Successful. Welcome, ".htmlentities($username).".";

        header("Location: index.php");
        return;
    } else { // wrong password
        $_SESSION['error'] = "Incorrect password.";
        error_log("Login fail ".$username." $check");

        header("Location: login.php");
        return;
    }

}

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Thoughts and Ideas - Login</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
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
    <input type="submit" onclick="return doValidate();" name="login" value="Log In">
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
