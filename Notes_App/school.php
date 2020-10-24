<?php
// Demand logged in
if ( ! isset($_SESSION['user_id']) || strlen($_SESSION['user_id']) < 1  ) {
    die('ACCESS DENIED');
}

// perform database operations
$dbsession = new PDO('mysql:host=localhost;port=3306;dbname=cars', 'carguy', 'zoom');
$dbsession->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$term = isset($_GET["term"]) ?  $_GET["term"] : "";

$stmt = $dbsession->prepare("SELECT name FROM institution WHERE name LIKE :tm");
$stmt->execute(array(
    ":tm" => $term."%"
));
$stmt = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($stmt);

