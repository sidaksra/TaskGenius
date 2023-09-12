<?php

$itemID = $_GET['listID'];
$valueresults = explode("###",$itemID);
$valueresults= $valueresults[count($valueresults)-1];
include 'includes/library.php';//Including the database
$pdo =connectDB();


$query1 = "SELECT * FROM `wishlistitems` WHERE itemID=$itemID";
$stmtCheck = $pdo->prepare($query1);
$stmtCheck->execute();
$result=$stmtCheck->fetch();
$data = array();
$internalvar=1;
$listName=$result['ItemName'];
$description =$result['description'];
$imagelink=$result['imagelink'];
$link=$result['link'];

$wholeHTMLtoPass="<h1>{$listName}</h1>
<h2>Description</h2><p>{$description}</p>

<img src='{$imagelink}'></img>
<a href='{$link}'>Link to Product</a>
";
echo $wholeHTMLtoPass;
   
?>