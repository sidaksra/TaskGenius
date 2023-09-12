<?php

$errors = array();              //Declaring an array to store the errors into it
$email=$_POST['email'] ?? null; //Declaring the variable email

if (isset($_POST['submit'])) {  //only do this code if the form has been submitted

    //Validating the email ID
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email1'] = true;
        
    }
    //If there are no errors on the page
    if(count($errors)===0){
            
            //Including the library and connecting it to the database
            include 'includes/library.php';
            $pdo = connectDB();

            //Making an query for the email in user_Record 
            $query = "SELECT * FROM user_Record WHERE email=?";
            $stmtCheck = $pdo->prepare($query);
            $stmtCheck->execute([$email]);
            $result=$stmtCheck->fetch();

            //If the email isn't registered
            if(!$result){
                $errors['email2'] = true; //login error is true
            }
            
            //if the email is registered
            else {      
                //Starting the session for the user              
                    session_start();
                    $_SESSION['email'] = $email;    //Recording the session for the email in email variable
                    
                    //Using the random to generate the random pin for the user for the OTP. This OTp
                    //will be sent to the user on the email provided by him.
                    $randompin = rand(1000,9999);
                    
                    //Updating the user_Record for the entered email id, we are storing this because, it will match for the OTp entered by the user
                    //On the next page.
                    $query2 = "UPDATE user_Record SET verification = ? WHERE email=?";
                    $stmt2 = $pdo->prepare($query2);
                    $stmt2->execute([$randompin,$email]);
                    
                    //Declaring the variable and storing the value rec from the query fro the email and username
                    $recieveremail = $result['email'];
                    $recieverusername = $result['username'];
                    
                    
                    //To send the Mail to the specific email defined by the user
                    //Reference: Lecture Slides

                    require_once "Mail.php";                                        //this includes the pear SMTP mail library
                    $from = " Plan-To-Do Password Reset <noreply@loki.trentu.ca>";
                    $to = "$recieverusername <$recieveremail>";                     //putting the user's email here
                    $subject = "Password Reset Verification Code";
                    //Message sent to the user on his email
                    $body = "Hi,

We have received a request to reset the password for your Plan-To-Do account. To ensure the security of your account, we have generated a verification code for you:
                    
Verification Code: $randompin
                    
Please treat this code with the utmost confidentiality and do not share it with anyone. If you did not initiate this password reset request, please contact our support team immediately by replying to this email or reaching out to us at [Your Support Email Address]. We take your account security seriously and will assist you in resolving any concerns.
                    
Thank you for choosing Plan-To-Do. We are committed to providing you with a safe and secure online experience.
                    
Best regards,

Customer Support Team
Plan-To-Do";

                    $host = "smtp.trentu.ca";
                    $headers = array ('From' => $from,
                    'To' => $to,
                    'Subject' => $subject);
                    $smtp = Mail::factory('smtp',
                    array ('host' => $host));
                    
                    $mail = $smtp->send($to, $headers, $body);
                    if (PEAR::isError($mail)) {
                     echo("<p>" . $mail->getMessage() . "</p>");
                    } else {
                        echo("<p>Message successfully sent!</p>");
                    }
                    //Redirecting to the Password Verification php to match the password entered by the user to the database password
                    //So, that user can change his pswd by entering the OTP
                    header("Location: PasswordVerification.php");
                    exit();
                }          
    }
}
?>
<!-- This Page will get the email ID from the user in order to send the OTP Request  -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Manage Password</title>

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
            <form method="post" class="AccountForm">
                <h1>Change / Forgot Password</h1>
                <div class="AccountRecord">   
                    <!-- email input -->
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Type your Email"required />
                    <!-- Print Erros -->
                    <span class="error <?=!isset($errors['email1']) ? 'hidden' : "";?>">Please Input a Valid Email</span>
                    <span class="error <?=!isset($errors['email2']) ? 'hidden' : "";?>">No account connected with this Email!</span>         
                </div>
                <div>
                    <!-- Form SUbmit Button  -->
                    <button type="submit" name="submit">Send Reset OTP</button>
                </div>
                <!-- If the user don't want to change the password, he or she can simply cancel and it will redirect them to the previous page -->
                <div class="changemind">
                    <p>Don't wanna change? <a href="#" onclick="history.go(-1)">Cancel</a></p>
                </div>
            </form>
        </div>
        </main>
    <!-- Including the Footer -->
    <?php include "includes/footer.php" ?> 
</body>
</html>