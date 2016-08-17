<?php
/**
 * Created by PhpStorm.
 * User: kuahusg
 * Date: 16-8-14
 * Time: 下午11:07
 */
require_once 'lib/mail/class.phpmailer.php';
require_once 'lib/mail/class.smtp.php';

function send_email($address, $query)
{
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->isHTML();
    $mail->SMTPAuth = true;
    $mail->Port = EMAIL_PORT;
    $mail->Username = EMAIL_USER_NAME;
    $mail->Password = EMAIL_PWD;

    $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
    $mail->addAddress($address);
    $mail->Subject = EMAIL_SUBJECT;
    $mail->Body = EMAIL_BODY . '<a href=' . EMAIL_VERIFY_LINK . $query . '>' . EMAIL_VERIFY_LINK . $query . '</a>';


    if ($mail->send())
        print_r("email send");
    else
        echo $mail->ErrorInfo;
}
