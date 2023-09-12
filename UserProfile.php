<?php

session_start();  //Starting the Session 

//If the user session is not declared, it means the user is not logined, therefore, it will redirect him to the Login Page
if(empty($_SESSION['ActiveuserID'])){
    header("Location: login.php");
}

//Declaring arrays
$errors = array();
$print =array();

//Setting the variable for the session of active userID in activer user varriable
$activeuser = $_SESSION['ActiveuserID'];

//Declaring the variables for username and email
$username = $_POST['username'] ?? NULL;
$email = $_POST['email']  ?? NULL;

include 'includes/library.php'; // Includeing the library
$pdo = connectDB();             //connnecting it to the database

//To change the username: If the update button is clicked, it will change the username for the active user
if (isset($_POST['update'])){
    $query = "SELECT * FROM user_Record WHERE username= ?";  //select the user filtered from the username
    $stmtCheck = $pdo->prepare($query);
    $stmtCheck->execute([$username]);
    $result=$stmtCheck->fetch();                             //fetching the username
    
    //If the result we get for the username exist in the database
    if($result){
        $errors['UserExist'] = true;    //print error
    }

    //Checking the username
    if(!isset($username) || strlen($username) === 0){
        $errors['username'] = true;
    }

    //If there is no error, It will update the user record for the username
    if(count($errors)===0){
        $UserNamequery = "UPDATE user_Record SET username = ? WHERE userID=$activeuser";    //Where activer user is logined [Getting the username]
        $userstmt = $pdo->prepare($UserNamequery);
        $UserResult = $userstmt->execute([$username]);
        //If the user result is executed
        if($UserResult){
            //Moving to the UserProfile.php page to see the changes
            header("Location: UserProfile.php" );
            exit();
        }
        //unknown Error
        else{
            $errors['UnknownError'] = true;
        }
    }

   
}
//To change the Email ID for the user: If the updateEmail button is clicked, it will change the Email for the active user
if (isset($_POST['updateEmail'])){
    
    //Making and executing the query
    $query = "SELECT * FROM user_Record WHERE email= ?";  
    $stmtCheck = $pdo->prepare($query);
    $stmtCheck->execute([$email]);
    $result=$stmtCheck->fetch();
    
    //If the result we get from fetching the abover query - > it will show that email exists in the database
    if($result){
        $errors['EmailExist'] = true;
    }
    
    //Validating & Sanitize email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = true;
    }

    //If the errors are 0, It will update the email id for a activer user in the database
    if(count($errors)===0){
        $Emailquery = "UPDATE user_Record SET email = ? WHERE userID=$activeuser";
        $Emailstmt = $pdo->prepare($Emailquery);
        $EmailResult = $Emailstmt->execute([$email]);
        //Redirecting to the userprofile to see the changes
        if($EmailResult){
            header("Location: UserProfile.php" );
            exit();
        }
    }

}

//In order to display the username: We are fetching the email id from the database for the activeuser (taking it's user id through)
$GetEmailQuery = "SELECT * FROM user_Record WHERE userID=$activeuser";  //selects the user filtered from the username
$EQuery = $pdo->prepare($GetEmailQuery);
$EQuery->execute();
$AnsEmail=$EQuery->fetch();
$email = $AnsEmail['email']; //Stores the value in variable email (It will store the Email ID)

//In order to display the email : We are fetching the username from the database for the activeuser (taking it's user id through)
$GetUsernameQuery = "SELECT * FROM user_Record WHERE userID=$activeuser";  //selects the user filtered from the username
$UQuery = $pdo->prepare($GetUsernameQuery);
$UQuery->execute();
$AnsUser=$UQuery->fetch();
$user = $AnsUser['username'];   //Stores the value in variable username (It will store the Username)
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>  My Profile Plan-To-Do </title>
        <link rel="stylesheet" href="styles/UserProfile.css" />
        <link rel="stylesheet" href="styles/nav.css" />
        <link rel="stylesheet" href="styles/media-queries.css" /> 
        <link rel="stylesheet" href="styles/footer.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">       
        <script src="https://kit.fontawesome.com/1d089da2a3.js" crossorigin="anonymous"></script>
        <script src="script/main.js"  type="text/javascript"></script>
    </head>
    <body>
            <main>
                <!-- Including the User Navigation for the Logined in user -->
                <?php include "includes/UserNav.php" ?>
                <div class = "ProfileDiv">
                        <div id="MyProfile">
                            <h1> My Profile</h1>    
                            <div class="topProfileContent">
                                <div>
                                    <!-- Reference: https://pixabay.com/images/id-1577909/ -->
                                    <img src="images/avatar.png" alt="Avatar Image">
                                    <h2> <?php echo $user; ?></h2>
                                </div>
                                <div>
                                    <!-- Showing the Current username and email id for the activer user -->
                                    <p>User Name: <?php echo $user; ?></p>
                                    <p>Email ID: <?php echo $email; ?></p>
                                </div>
                            </div>
                           
                        <div class="updateRecord">
                            <div>
                                <form method="post">
                                    <h3 id="ChangeUsername">Change Username</h3>
                                    <!-- Print Error for the username is already taken -->
                                    <span class="error <?=!isset($errors['UserExist']) ? 'hidden' : "";?>">This Username already Exist! Please Choose another username.</span>
                                    <!-- Take input for the username -->
                                    
                                    <input type="text" id="username" name="username" size="40" placeholder="Type your Username..." required value="<?=$username?>" />
                                    <!-- Print other errors -->
                                    <span class="error <?=!isset($errors['UnknownError']) ? 'hidden' : "";?>">An Error has Occured!</span>
                                    <span class="error <?=!isset($errors['username']) ? 'hidden' : "";?>">Please Enter Your Username</span>
                                    <!-- Button submission to update the username -->
                                    <button type="submit" name="update" value="update">Update Username</button>
                                </form>
                            </div>
                            <div>
                                <form method="post">
                                    <h3 id="ChangeEmail">Change Email Address</h3>
                                    <span class="error <?=!isset($errors['EmailExist']) ? 'hidden' : "";?>">The email address is already registered!</span>
                                    <!-- Taking the input of the Email ID from the user -->
                                    
                                    <input type="email" id="email" name="email" size="40" required placeholder="Type your Email ID..." />
                                    <span class="error <?=!isset($errors['email']) ? 'hidden' : "";?>">Please enter a correct email</span> 
                                    <!-- Button Submission to update the Email ID -->
                                    <button type="submit" name="updateEmail" value="updateEmail">Update Email</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        <!-- Including the footer -->
        <?php include "includes/footer.php" ?>
    </body>
</html>


