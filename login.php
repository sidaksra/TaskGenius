<?php
//this declares an error array 
$errors = array();

/*
    Form - Login
    Form elements 
    username -----takes the username 
    password -----takes the password
*/
//Declaring Variables
$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;
 
//Triggerd events from submit button

  //IF THE FORM IS SUBMITTED 
 if (isset($_POST['submit'])) { //only do this code if the form has been submitted

    include 'includes/library.php'; // Includes the library
    $pdo = connectDB();             //connnects to the database

    $query = "SELECT * FROM user_Record WHERE username=?";  //selects the user filtered from the username
    $stmtCheck = $pdo->prepare($query);                     //Using Prepare
    $stmtCheck->execute([$username]);
    $result=$stmtCheck->fetch();                            //Fetching the query about the username

    //If the username entered by the user doesn't exist
    if(!$result){
        $errors['login'] = true;                            //login error is true
    }
    if($result) {
        //Verifying the password (Result of Hash password stored in our database)
        if (password_verify($password, $result['password'])) {
            
            //Session elements
            session_start();                                //starting the session
            $_SESSION['username'] = $username;              //saving the username in the session
            $_SESSION['ActiveuserID'] = $result['userID'];  //saving the userID in the database
            
            //Setting the Cookies for the username and password
            if(!empty($_POST["remember"])) {                //If remember is check or clicked
                setcookie ("username",$_POST["username"],time()+ 3600); //Set cookie for user
                setcookie ("password",$_POST["password"],time()+ 3600); //Set cookie for password
            } 

            // Check if the user has a default list
            $activeUserID = $result['userID'];
            $getDefaultListQuery = "SELECT entryID FROM Project_List WHERE userID = ? AND listName = 'Plans (Default)'";
            $stmtGetDefaultList = $pdo->prepare($getDefaultListQuery);
            $stmtGetDefaultList->execute([$activeUserID]);
            $defaultList = $stmtGetDefaultList->fetch();

            if ($defaultList) {
                $listID = $defaultList['entryID'];
                header("Location: Toolpage.php?listID=" . $listID);
                exit();
            } else {
                echo 'Unknown Error 404';
            }
        } 
        //if the verify password fails
        else {
            $errors['login'] = true;
        }
    }
}
?>

<!-- HTML Start -->
<!DOCTYPE html>
<html lang="en">
    <!-- Header -->
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>  Login </title>
        <link rel="stylesheet" href="styles/masterLogin.css" />
        <link rel="stylesheet" href="styles/media-queries.css" />
        <link rel="stylesheet" href="styles/nav.css" />
        <link rel="stylesheet" href="styles/footer.css" />
    </head>
    <!-- Body elements -->
    <body>
            <main>
                <!-- Including the Navigation php for the un-signed user -->
                <?php include "includes/nav.php" ?>
                <div class = "loginform">
                    <!-- Creating a Self-processing Form  -->
                    <form id="loginpage" name="login" method="post">  
                    <h1>Login</h1>
                    <!-- Display the error If the username or password enter by the user doesn't match  -->
                    <span class="error <?=!isset($errors['login']) ? 'hidden' : "";?>">Error: Your username or password was invalid. Please Check Again</span>
                        <div class = "enter">
                            <!-- Taking Input of Username -->
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" size="40" value="<?php if(isset($_COOKIE["username"])) { echo $_COOKIE["username"]; } ?>" placeholder="Type your username" required />
                            <!-- Taking Input of the Password -->
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" size="40" value="<?php if(isset($_COOKIE["password"])) { echo $_COOKIE["password"]; } ?>" placeholder="Type your password" required />
                            <!--Forgot password - if the user forgot about his pswd -->
                            <div class = "forgot underlineHoverEffect">
                                <a href="ManagePassword.php">Forgot Password?</a>
                            </div>
                        </div>
                        <!-- This will work for setting the cookie. If this is checked or clicked (the php code will work for this) -->
                        <div class="RememberCheckBox">
                                <input type="checkbox" name="remember" id="remember"/>
                                <label for="remember">Remember Me</label>
                        </div>
                        <!-- Submit the form button -->
                        <div>
                            <button type="submit" name="submit" value="submit" class="submit">Sign In</button>
                        </div>
                        <!-- If the user want to sign up for the account -->
                        <div id="Signup">
                            <h3>Don't have an account? <a href="signup.php">Sign Up</a></h3>
                        </div>
                    </form>
                    <!-- Form End -->
                </div>
            </main>
        <!-- Including the footer -->
        <?php include "includes/footer.php" ?>
    </body>
</html>
<!-- End -->