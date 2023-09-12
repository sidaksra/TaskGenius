<?php
// Starting the Session
session_start();

// Get the entry id from the Link
$entryID = $_GET['listID'];

// Include the library instructions and then connect to the database
include 'includes/library.php';
$pdo = connectDB();

// Query to check if the list is private and fetch list details
$query1 = "SELECT * FROM `Project_List` WHERE entryID = :entryID";
$stmtCheck = $pdo->prepare($query1);
$stmtCheck->bindParam(':entryID', $entryID, PDO::PARAM_INT);
$stmtCheck->execute();
$resultList = $stmtCheck->fetch();

// Check if the query returned any results (list is available)
if ($resultList) {
    $listResultpass = $resultList['privateYN'];

    // If the submit button is clicked -> It will verify the password
    if (isset($_POST['submit'])) {
        // Declare the variables for the password and list password
        $PasswordEntered = $_POST['password'];
        $PasswordDatabase = $resultList['listPassword'];

        // If the password entered by the user is the same as the password from the database, redirect to the Display List page
        if ($PasswordEntered == $PasswordDatabase) {
            header("Location: DisplayList.php?listID={$entryID}");
        }
        // If the Password is wrong
        elseif ($PasswordEntered != $PasswordDatabase) {
             // Password is wrong, show an alert box and stay on the current page
             echo '<script>alert("Wrong password. Please try again.");</script>';
        }
    }
} else {
    // List is not available or deleted, redirect to an error page
    header("Location: error.php");
}
?>


<!-- HTML START -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Verify List</title>

        <!--Stylesheets-->
        <link rel="stylesheet" href="styles/nav.css" />
        <link rel="stylesheet" href="styles/verifyShare.css" />
        <link rel="stylesheet" href="styles/media-queries.css" /> 
        <link rel="stylesheet" href="styles/footer.css" />

    </head>
        <body>
        <main>
            <!-- This is the Logined user nav -->
            <?php include "includes/nav.php" ?>
            
            <section class  ="VerifyDiv">
            <h1>Verify to view the List</h1>
                <!-- IF THE RESULT WE GET FROM THE DATABASE FOR THE PASSWORD IS N, i.e User can 
                share the list to other and they can see the list without typing any password -->
                <?php if($listResultpass=="N"){
                    header("Location: DisplayList.php?listID={$entryID}");
                }
                // IF THE RESULT PASS IS Y, THEN USER OR HIS FRIENDS NEED TO ENTER THE PASSWORD TO ACCESS OR SEE THE LIST CONTENT
                elseif($listResultpass=="Y"){?>
                    <!-- SELF PROCESSING FORM -->
                    <form method="post" enctype="multipart/form-data" class="Verify-Form">
                    <div class="Verify-Inputs"> 
                        <!-- Input for the password from the user for a specific link which is shared -->
                            <input type="password" id="password" name="password" placeholder="Please Enter the Private List Password...." size="40" />
                            <div class="Verify-button">    
                                <!-- Button to verify the password -->
                                <button type="submit" name="submit">Verify List Password</button>
                            </div>
                    </div>
                    </form>
            <?php }
                       
            ?>
        </main>
    <!-- Including the footer element -->
    <?php include "includes/footer.php" ?>   
</body>
</html>