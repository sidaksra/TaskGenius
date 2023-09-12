<?php
//This page is used to verify the Password(OTP) by checking for the verification message, 
//if the verification message matches the code sent on the email, it will change the password

session_start();            //Start the session
$email=$_SESSION['email'];  //Intilizing the session of email
$errors = array();          //Array to store the error messages

//Verify the OTP
if (isset($_POST['verify'])) { //only do this code if the form has been submitted
    if(count($errors)===0){
        //Including the library and connecting it with the database
            include 'includes/library.php';
            $pdo = connectDB();

            //Query to fetch the result for the email entered by the user
            $query = "SELECT * FROM user_Record WHERE email=?";
            $stmtCheck = $pdo->prepare($query);
            $stmtCheck->execute([$email]);
            $result=$stmtCheck->fetch();

            //If the username entered by the user doesn't exist
            if(!$result){
                $errors['verification'] = true; //Verification false
            }
            //If the username exist in the database -> It will verify the password(OTp)
            else {
                $enteredverifcaiton = $_POST['validation'];
                //If verification code entered by the user matches the verification code for the result
                if ($enteredverifcaiton == $result['verification']) {
                    
                    //Updating the user_Record for the email address for the particular user
                    $query2 = "UPDATE user_Record SET verification = ? WHERE email=?";
                    $stmt = $pdo->prepare($query2);
                    $stmt->execute([0000,$email]);

                    //Redirecting the user to the reset password page
                    header("Location: ResetPassword.php");
                    exit();
                } 
                //If the verification code fails
                else {
                    $errors['verification'] = true;
                }
            }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ConfirmDelete</title>

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
        <div class  ="AccountDiv">
            <!-- Form  -->
            <form method="post" class="AccountForm">
                <h1> Verify Your Email ID</h1>
                <div class="AccountRecord">  
                    <!-- Asking the user to input the OTP  -->
                    <label for="validation">Vaidation</label>
                    <input type="TEXT" name="validation" id="validation" placeholder="Validation Number"required />
                    <span class="error <?=!isset($errors['verification']) ? 'hidden' : "";?>">Empty or invalid Verification Number.</span>
                </div>
                <!-- Form submission button -->
                <div>
                    <button type="submit" name="verify">Verification</button>
                </div>
                <!-- If the user want to cancel the password change -->
                <div class="changemind">
                    <p>Don't wanna change your email? <a href="login.php">Cancel</a></p>
                </div>
            </form>
        </div>
        </main>
    <!-- Including the footer -->
    <?php include "includes/footer.php" ?> 
</body>
</html>