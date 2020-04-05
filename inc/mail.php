<?php
require_once __DIR__.'/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;


function gcs_mail($toEmail, $subject, $message, $headerArray) {
  global $config;

  $mail = new PHPMailer;
  $mail->isSMTP();
  $mail->Host = $config['phpmailer']['host'];
  $mail->Port = $config['phpmailer']['port'];
  $mail->SMTPSecure = $config['phpmailer']['smtpsecure'];
  $mail->SMTPAuth = $config['phpmailer']['smtpauth'];

  $mail->Username = $config['phpmailer']['username'];
  $mail->Password = $config['phpmailer']['password'];

  $mail->setFrom($config['phpmailer']['email'], $config['phpmailer']['fromname']);
  if (isset($headerArray['from'])) {
    $mail->addReplyTo($headerArray['from'], $config['phpmailer']['fromname']);
  }

  $to_ids = explode(',',$toEmail);
  foreach ($to_ids as $to_id) {
    $mail->addAddress($to_id);
  }

  if (isset($headerArray['cc'])) {
    $ccs = explode(',',$headerArray['cc']);
    foreach ($ccs as $cc) {
      $mail->addCC($cc);
    }
  }

  //$msg = str_replace('\n', '<br>\n', $message);

  $mail->Subject = $subject;
  $mail->isHTML(false);
  $mail->Body = $message;

  if (!$mail->send()) {
    $error = "Mailer Error: " . $mail->ErrorInfo;
    error_log($error);
    error_log("    - to '$toEmail'");

    return 0;
    //echo '<p id="para">'.$error.'</p>';
  } else {
    //echo '<p id="para">Message sent!</p>';
    return 1;
  }

}

// TEST / How to use
/*(
$to_id = 'test@test.com';
$subject = 'php mailer testing';
$message = "I'm testing the php mailer";
gcs_mail ($to_id, $subject, $message, array());
*/

?>
