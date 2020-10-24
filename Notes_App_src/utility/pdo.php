<?php
$dbsession = new PDO('mysql:host=localhost;port=3306;dbname=notes', 'robinthegreat', 'IamPrettyGreat');
$dbsession->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);