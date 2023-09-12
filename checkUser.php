<?php

$username =$_POST['username'] ?? null;

//include the library file
require 'includes/library.php';
// create the database connection
$pdo = connectDB();

//query for record matching provided email
$queryCheckUser = "SELECT * FROM user_Record WHERE username=?";
    $stmtCheckUser = $pdo->prepare($queryCheckUser);
    $stmtCheckUser->execute([$username]);
    $Userrows=$stmtCheckUser->fetchColumn();

//remember fetch returns false when there were no records
if($Userrows) {
    echo 'true'; //user found
} else {
    echo 'false'; //user not found
}
exit();

?>
