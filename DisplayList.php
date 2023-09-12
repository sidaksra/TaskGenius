<?php
session_start();
//get the entry id from the link
$entryID = $_GET['listID'];

//include the library instructions and then connect to the database
 include 'includes/library.php';
$pdo =connectDB();
//I am doing this to check if the list is private and if it is then I will ask a password 
//also using this to fetch the list password and verify it           
$query1 = "SELECT * FROM `wishlistitems` WHERE entryID=$entryID";
$stmtCheck = $pdo->prepare($query1);
$stmtCheck->execute();
$result=$stmtCheck->fetchAll();

$getListName = "SELECT listName FROM Project_List JOIN wishlistitems on Project_List.entryID = wishlistitems.entryID WHERE Project_List.entryID=$entryID";
$StmtListName = $pdo->prepare($getListName);
$StmtListName->execute();
$ListNameResult=$StmtListName->fetch();
$nameofList = $ListNameResult['listName']??null;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shared List</title>
    <!--Stylesheets-->
    <link rel="stylesheet" href="styles/DisplayList.css" />
    <link rel="stylesheet" href="styles/media-queries.css" /> 
    <link rel="stylesheet" href="styles/nav.css" />
    <link rel="stylesheet" href="styles/footer.css" />
</head>
<body>
    <main>
        <?php include "includes/nav.php" ?>
        <!--List content-->
        <div class="display-list">
            <h1>My List: <?php echo $nameofList;?></h1>
            <div class="display-list-table">
            <table>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                </tr>
                <?php foreach ($result as $row2): ?>
                    <?php if ($row2['checked'] == "Y"): ?>
                        <tr>
                            <td><?php echo $row2['ItemName']; ?></td>
                            <td><?php echo $row2['description']; ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php foreach ($result as $row2): ?>
                    <?php if ($row2['checked'] == "N"): ?>
                        <tr>
                            <td><?php echo $row2['ItemName']; ?></td>
                            <td><?php echo $row2['description']; ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
            </div>
        </div>
    </main>
    <?php include "includes/footer.php" ?>
</body>
</html>
