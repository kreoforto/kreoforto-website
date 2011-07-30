<?php
    require_once("PHPMailer.php");
    
    date_default_timezone_set('Europe/Berlin');
    setlocale(LC_TIME, "de_DE");

    $kreoforto = "info@kreoforto.de";
    $subject   = "Anfrage";

    $info = (object)$_REQUEST["info"];
    $mail = new PHPMailer;
    
    $message   = "Datum: ".strftime("%c")."\n";
    $message  .= "Firma: ".$info->company."\n";
    $message  .= "Ansprechpartner: ".$info->name."\n";
    $message  .= "Telefon: ".$info->phoneNumber."\n";
    $message  .= "E-Mail: ".$info->mailAddress."\n";
    $message  .= "Nachricht: ".$info->message."\n";
    
    $mail->setRecipient($kreoforto);
    $mail->setReplyAddress($info->mailAddress);
    $mail->setSubject($subject);
    $mail->setSender($kreoforto);
    $mail->setMessage($message);
    $mail->sendMail();
?>