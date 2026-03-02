<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../phpmailer/Exception.php';
require __DIR__ . '/../phpmailer/PHPMailer.php';
require __DIR__ . '/../phpmailer/SMTP.php';

function sendMail($to, $subject, $body) {

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'admin@nielitbhubaneswar.in';
        $mail->Password = 'Nielitbbsr@2025';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('admin@nielitbhubaneswar.in', 'NIELIT Job Fair');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}
?>