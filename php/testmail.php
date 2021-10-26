<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';
require_once 'PHPMailer-master/src/Exception.php';




$bodytext="Bonjour";
$email = new PHPMailer();

$email->SetFrom('admin@aglic.dz', 'AGLIC-SPA'); //Name is optional
$email->Subject   = 'Documents-Sinistre';
$email->Body      = $bodytext;
$email->AddAddress( 'yacine.louda@aglic.dz' );

$file_to_attach = '../doc/CG-AVA.pdf';

$email->AddAttachment( $file_to_attach , 'logo' );

return $email->Send();

?>