<?php
session_start();
//If the user has not login into his account, it will redirect them to the login page. i.e Session for this user doesn't exist
if(empty($_SESSION['ActiveuserID'])){
    header("Location: login.php");
}
//this declares an error array 
$errors = array();
$activeuser = $_SESSION['ActiveuserID'];
$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;
 
//IF THE FORM IS SUBMITTED 
 if (isset($_POST['ConfirmDelete'])) { //only do this code if the form has been submitted
    
    include 'includes/library.php';// Includes the library
    $pdo = connectDB();//connnects to the database

    $query = "SELECT * FROM user_Record WHERE username=?";  //selects the user filtered from the username
    $stmtCheck = $pdo->prepare($query);
    $stmtCheck->execute([$username]);
    $result=$stmtCheck->fetch();

    //If the username entered by the user doesn't exist
    if(!$result){
        $errors['NotExist'] = true; //login error is true
    }
    //If the username matched from the result from the query
    if($result) {
        if (password_verify($password, $result['password'])) {

            //Delecting the user_record for the particular username 
            $Deletequery = "DELETE FROM user_Record WHERE username=?";  //selects the user filtered from the username
            $stmtDeleteAccount = $pdo->prepare($Deletequery);
            $stmtDeleteAccount->execute([$username]);
            $AccountDeleteResult=$stmtDeleteAccount->fetch();

            // Retrieve the entryId from Project_List for the user
            $SelectEntryId = "SELECT entryId FROM Project_List WHERE userID=$activeuser";
            $stmtSelectEntryId = $pdo->prepare($SelectEntryId);
            $stmtSelectEntryId->execute();
            $entryIdResult = $stmtSelectEntryId->fetch();

            //Deleting all his/her projects (Wish List)
            $DeleteProject = "DELETE FROM Project_List WHERE userID=$activeuser";  //selects the user filtered from the username
            $stmtDeleteProject = $pdo->prepare($DeleteProject);
            $stmtDeleteProject->execute();
            $ProjectDeleteResult=$stmtDeleteProject->fetch();

            if ($entryIdResult) {
                // Delete every item in the list using the retrieved entryId
                $DeleteListItems = "DELETE FROM wishlistitems WHERE entryId=?";
                $stmtDeleteList = $pdo->prepare($DeleteListItems);
                $stmtDeleteList->execute([$entryIdResult['entryId']]);
            }

            //If all these don't exist in the database(i.e they are deleted). It will redirect to the index page and show the user that their account has been deleted.
            if(!$AccountDeleteResult && !$ProjectDeleteResult){
                //Destroying the session
                session_destroy();
                header("refresh:0.2; url=index.php");
                echo '<script type="text/javascript">
                alert("Your Account has been sucessfully deleted!");
                </script>';
                exit();
            }  
        } 
        else {
            $errors['NotValid'] = true;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Delete Your Account</title>

        <!--Stylesheets-->
        <link rel="stylesheet" href="styles/resetPassword.css" />
        <link rel="stylesheet" href="styles/media-queries.css" /> 
        <link rel="stylesheet" href="styles/nav.css" />
        <link rel="stylesheet" href="styles/footer.css" />
        <script src="script/main.js"  type="text/javascript"></script>

        <!--Stylesheets-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">       
        <script src="https://kit.fontawesome.com/1d089da2a3.js" crossorigin="anonymous"></script>

    </head>
    <body>
        <main>
        <!-- Including to UserNav php for the logined user -->
        <?php include "includes/UserNav.php" ?>
        <div class  ="AccountDiv">
            <!-- FORM  -->
            <form method="post" class="AccountForm">
                <h1><i class="fa-solid fa-face-sad-tear"></i>Sad to see you go!!</h1>
                <div class="AccountRecord">
                    <!-- ERROR FOR THE USERNAME AND PASSWORD ACCOUNT -->
                    <span class="error <?=!isset($errors['NotValid']) ? 'hidden' : "";?>">Error: Your username or password was invalid. Please Check Again</span>
                    <span class="error <?=!isset($errors['NotExist']) ? 'hidden' : "";?>">Error: No Account connected with this username</span>
                    <!-- TAKING THE INPUT FOR THE USERNAME -->
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" size="40" value="<?=$username?>" placeholder="Type your Username" required />
                    <!-- TAKING INPUT FOR THE PASSWORD -->
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" size="40" placeholder="Type your password" required />
                    <label for="feedback"> Is there something we can do better? </label>
                    <div>                
                        <input type="text" placeholder="Why leave ?">
                    </div>
                    <!-- If the checkbox is clicked only then form willl be submitted -->
                    <div class="checkboxToDelete">
                        <input  type="checkbox" class="checkbox" name="agree" id="agree" value="Y" required/>
                        <label for="agree">Are you sure you want to delete your account?</a></label>
                    </div>                
                </div>
                <div>
                    <!-- TO SUBMIT THE FORM -->
                    <button type="submit" name="ConfirmDelete">Confirm</button>
                </div>     
                <!-- IF THE USER WANT TO CANCEL, HE OR SHE SIMPLY CLICK ON CANCEL, IT WILL REDIRECT THEM TO THE PREV PAGE -->
                <div class="changemind">
                    <p>Don't wanna delete? <a href="#" onclick="history.go(-1)">Cancel</a></p>
                </div>
            </form>
        </main>
    </body>
</html>