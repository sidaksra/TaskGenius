<?php
//This is the Reset Password Page

if (isset($_POST['reset'])){ //only do this code if the form has been submitted
    
    //Starting the session for the particular user.
    session_start();
    $email = $_SESSION['email'];

    //Declaring the array for print and erros
    $print =array();
    $errors = array();

    // Including library and making a database connection
    include 'includes/library.php';
    $pdo = connectDB();
    
    //intilizing the password and confirm password variable 
    $password = $_POST['password'] ?? NULL;
    $confirmpassword = $_POST['confirmpassword'] ?? NULL;

    //CHecking for the strong password from the user
    if( !empty($password) && !empty($confirmpassword)  ){
        //IF password is less than 8, it will print the error
        if (strlen($_POST["password"]) <= '8') {
            $errors['tosmall'] = true; 
        }
        //IF THE PASSWORD IS NOT HAVING NUMBER, SHOW ERROR
        elseif(!preg_match("#[0-9]+#",$password)) {
            $errors['passNum'] = true; 
        }
        //IF THE PASSWORD IS NOT HAVING A CAPITAL LETTER, PRINT ERROR
        elseif(!preg_match("#[A-Z]+#",$password)) {
            $errors['passCaps'] = true; 
        }
        //IF THE PASSWORD IS NOT HAVING ANY SYMBOL, IT WILL SHOW ERROR
        elseif(!preg_match("#[\W]+#",$password)) {
            $errors['passSymbol'] = true; 
        }
        //IF THE ENTERED PASSWORD, DOES'NT MATCH WITH RE-ENTERED PASSWORD, IT WILL SHOW ERRORA
        if($password != $confirmpassword){
            $errors['match'] = true;
        }
    }

    //IF THE ERROR IS NULL
    if(count($errors)===0){

        //password hashed
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        
        /********************************************
         * UPDATING user PASSWORD to the database
         ********************************************/
        $query3 = "UPDATE user_Record SET password = ? WHERE email = ?";
        $stmt3 = $pdo->prepare($query3);
        $result2 = $stmt3->execute([$hashPassword, $email]);

        //if insert is done!
        if ($result2) {
            header("refresh:0.5; url=login.php");
            echo '<script type="text/javascript">
            alert("Your Password has been sucessfully updated! Please Login again to your account");
            </script>';
        } 

        //if insert is unsuccessful!
        else {
            $print['unsuccess'] = true;
        }
        
    }
}
?>

<!-- HTML START -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Reset Password</title>

        <!--Stylesheets-->
        <link rel="stylesheet" href="styles/resetPassword.css" />
        <link rel="stylesheet" href="styles/media-queries.css" /> 
        <link rel="stylesheet" href="styles/nav.css" />
        <link rel="stylesheet" href="styles/footer.css" />

        <!--Stylesheets-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">       
        <script src="https://kit.fontawesome.com/1d089da2a3.js" crossorigin="anonymous"></script>

    </head>
    <body>
        <main>
        <div class ="AccountDiv">    
            <form method="post" class="AccountForm">
                <h1>Set New Password</h1>
                <div class="AccountRecord">   
                    <!-- New Password -->
                    <label for="validation">New Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter a new Password..."required />
                    <!-- PRINT ERRORS -->
                    <span class="error <?=!isset($errors['tosmall']) ? 'hidden' : "";?>">Your Password Must Contain At Least 8 Characters!</span>
                    <span class="error <?=!isset($errors['passNum']) ? 'hidden' : "";?>">Your Password Must Contain At Least 1 Number!</span>
                    <span class="error <?=!isset($errors['passCaps']) ? 'hidden' : "";?>">Your Password Must Contain At Least 1 Capital Letter!</span>
                    <span class="error <?=!isset($errors['passSymbol']) ? 'hidden' : "";?>">Your Password Must Contain symbols!</span>

                    <!-- Confirm Password -->
                    <label for="validation">Confirm New Password</label>
                    <input type="password" name="confirmpassword" id="password" placeholder="Confirm your password..."required />
                    <!-- PRINT ERRORS -->
                    <span class="error <?=!isset($errors['match']) ? 'hidden' : "";?>">Password don't match</span>
                    <span class="print <?=!isset($errors['unsuccess']) ? 'hidden' : "";?>">Unsuccessful</span>
                    <p>Use at least 8 or more characters with a mix of letters, numbers & symbols</p>
                </div>
                <div>
                    <!-- BUTTON SUBMISSION FOR THE REST PASSWORD -->
                    <button type="submit" name="reset">Reset password</button>
                </div>
                <!-- if the user wants to return to the previous page and don't want to change the password -->
                <div class="changemind">
                    <p>Don't wanna change your email? <a href="login.php">Cancel</a></p>
                </div>
            </form>
        </div>
        </main>
        <!-- Including the footer elements -->
        <?php include "includes/footer.php" ?>
    </body>
</html>