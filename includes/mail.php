<?php
require_once "Mail.php";  //this includes the pear SMTP mail library
$from = "Password System Reset <noreply@loki.trentu.ca>";
$to;  //put user's email here
$subject = "Password reset";
$body = "Hey,
        A recent password update has been made for you account at event registry. Your temporary password in 'default'. Use the link below 
        to login and reset the password";
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

?>