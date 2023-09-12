<?php

//get email from GET array
$email = $_GET['email'] ?? null;

//make sure it's a valid email
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'error';
    exit();
}

//include the library file
require 'includes/library.php';
// create the database connection
$pdo = connectDB();

//query for record matching provided email
$queryCheckEmail = "SELECT * FROM user_Record WHERE email=?";
$stmtCheck = $pdo->prepare($queryCheckEmail);
$stmtCheck->execute([$email]);
$rows=$stmtCheck->fetchColumn();

//remember fetch returns false when there were no records
if($rows) {
    echo 'true'; //email found
} else {
    echo 'false'; //email not found
}
exit();

?>
