<?php
//Declaring Arrays fro error and print
$errors = array();
$print = array();

//Intilizing the variables for the things which are in the database
$username =$_POST['username'] ?? null;
$email=$_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$confirmpass = $_POST['confirmpass'] ?? null;

if (isset($_POST['submit'])){ //only do this code if the form has been submitted

    // Include library, make database connection
    include 'includes/library.php';
    $pdo = connectDB();

    //Checking wheather the email already registered onto the database or not!
    $queryCheckEmail = "SELECT * FROM user_Record WHERE email=?";
    $stmtCheck = $pdo->prepare($queryCheckEmail);
    $stmtCheck->execute([$email]);
    $rows=$stmtCheck->fetchColumn();

    // Checking wheather the username already registered onto the database or not!
    $queryCheckUser = "SELECT * FROM user_Record WHERE username=?";
    $stmtCheckUser = $pdo->prepare($queryCheckUser);
    $stmtCheckUser->execute([$username]);
    $Userrows=$stmtCheckUser->fetchColumn();
        
    //if email already registered
    if($rows)
    {
        $errors['emailexist'] = true; 
    }

    //if username already registered
    if($Userrows)
    {
        $errors['usernameReg'] = true; 
    }


    //if name is not entered by the user
    if (!isset($username) || strlen($username) === 0) {
        $errors['name'] = true;
    }

    //For Strong Password
    if(!empty($password)){
        if (strlen($_POST["password"]) <= '8') {
            $errors['tosmall'] = true; 
        }
        elseif(!preg_match("#[0-9]+#",$password)) {
            $errors['passNum'] = true; 
        }
        elseif(!preg_match("#[A-Z]+#",$password)) {
            $errors['passCaps'] = true; 
        }
        elseif(!preg_match("#[\W]+#",$password)) {
            $errors['passSymbol'] = true; 
        }
    }
    //If the Password entered by the user, doesn't match with the re-entered password
    if ($password != $confirmpass) {
        //Error password does not match
        $errors['passwordmatch'] = true;
    }

    //If there are no erros
    if(count($errors)===0){

        //password hashed
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        //Declaring verifcation as zero
        $verificationcode=0000;

        /********************************************
         * Inserting user credentials to the database
         ********************************************/
        
        //Making query statement to insert users data into user_Record table
        $queryInsert = "INSERT INTO user_Record VALUES (NULL, ?,?,?,?, NOW())";
        $stmtInsert = $pdo->prepare($queryInsert);
        $result = $stmtInsert->execute([$username, $email, $hashPassword,$verificationcode]);

        //if Account is created succesfully, insertion is done!
        if ($result) {
            // Insert a new row for the user's "Plans (Default)" list
            $activeUserID = $pdo->lastInsertId();
            $insertDefaultListQuery = "INSERT INTO Project_List (userID, listName, privateYN, listPassword) VALUES (?, 'plans (Default)', 'N', 'N')";
            $stmtInsertDefaultList = $pdo->prepare($insertDefaultListQuery);
            $stmtInsertDefaultList->execute([$activeUserID]);

            //Moving To the Login Page!
            header("refresh:0.5; url=login.php");
            //It will show an alert box to user. Just to make them sure that their account is created!
            echo '<script type="text/javascript">
            alert("Your Account has been sucessfully created! Please Login into your account.");
            </script>';
            exit();
        } 

        //if insert of user credentials in the database is unsuccessful!
        else {
            $print['unsuccess'] = true;
        }
        
    }
}
?>
        
<!-- HTML START SIGN UP PAGE-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles/signup.css">
    <link rel="stylesheet" href="styles/media-queries.css" />
    <link rel="stylesheet" href="styles/nav.css" />
    <link rel="stylesheet" href="styles/footer.css" />
    <script src="script/main.js"  type="text/javascript"></script>
    </head>
    <!-- Body Elements -->
    <body>
        <main>
        <!-- Including the Navigation for the un-signed user -->
        <?php include "includes/nav.php"?>
        <section class="signupform">
            <!-- Sign Up form  -->
            <form name="signup" id="SignUpForm" method="post">
                <h1>Sign Up</h1>
                <span class="printout <?=!isset($print['unsuccess']) ? 'hidden' : "";?>"><?php echo "Your Signup was unsuccessful!" ?></span>
                <h2>Please fill in this form to create an account.</h2>
                <hr>
                <div class = "enter">
                    <!-- Input of user: username, email, password -->
                    <!-- Taking input of the username -->
                    <div>
                        <label for="username">Username</label>
                        <input type="username" name="username" id="username" value="<?=$username?>" placeholder="Type your username..." required />                            
                        <!-- Error username -->
                        <span class="error <?=!isset($errors['username']) ? 'hidden' : "";?>">Please enter your username</span>
                        <span class="error <?=!isset($errors['usernameReg']) ? 'hidden' : "";?>">The username is already registered!!</span>
                    </div>
                    <!-- Taking the input for the email address -->
                    <div>
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="<?=$email?>" placeholder="name@xyz.com"required />
                        <!-- Error for email -->
                        <span class="error <?=!isset($errors['email']) ? 'hidden' : "";?>">Please enter a correct email</span>
                        <span class="error <?=!isset($errors['emailexist']) ? 'hidden' : "";?>">The email address is already registered!</span>
                    </div>
                    <!-- Taking the input of password (Strong Password from the user as seen in  the php) -->
                    <div>
                        <label for="password">Password</label>
                        <input type="password" class="password" name="password" placeholder="Type your password..." required />
                        <!-- Error for the Password -->
                        <span class="error <?=!isset($errors['tosmall']) ? 'hidden' : "";?>">Your Password Must Contain At Least 8 Characters!</span>
                        <span class="error <?=!isset($errors['passNum']) ? 'hidden' : "";?>">Your Password Must Contain At Least 1 Number!</span>
                        <span class="error <?=!isset($errors['passCaps']) ? 'hidden' : "";?>">Your Password Must Contain At Least 1 Capital Letter!</span>
                        <span class="error <?=!isset($errors['passSymbol']) ? 'hidden' : "";?>">Your Password Must Contain symbols!</span>
                    </div>
                    <div>
                        <!-- Password input again from the user -->
                        <label for="password">Confirm Password</label>
                        <input type="password" class="confirmpass" name="confirmpass" placeholder="Confirm your password..." required />
                        <p>For Password: Use at least 8 or more characters with a mix of letters, numbers & symbols</p>
                        <!-- Error for password -->
                        <span class="error <?=!isset($errors['tosmall']) ? 'hidden' : "";?>">Your Password Must Contain At Least 8 Characters!</span>
                        <span class="error <?=!isset($errors['passNum']) ? 'hidden' : "";?>">Your Password Must Contain At Least 1 Number!</span>
                        <span class="error <?=!isset($errors['passCaps']) ? 'hidden' : "";?>">Your Password Must Contain At Least 1 Capital Letter!</span>
                        <span class="error <?=!isset($errors['passSymbol']) ? 'hidden' : "";?>">Your Password Must Contain symbols!</span>
                        <span class="error <?=!isset($errors['passwordmatch']) ? 'hidden' : "";?>">Your Password Doesn't Match!</span>
                    </div>
                </div>
                <!-- Sign Up Button -->
                <div>
                    <button type="submit" name="submit">Sign Up</button>
                </div>
                <!-- If the user had already have an account -->
                <div class="Terms-Policy-Div">
                    <p>By creating an account you agree to our <a href="#">Terms & Policy</a>.</p>
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </form>
        </section>
     <!-- Include the footer php -->
     <?php include "includes/footer.php" ?>
    </body>
</html>