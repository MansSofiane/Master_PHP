<?php
session_start();
require_once("../../../../data/conn5.php");
//on recupere le code du pays
if (isset($_REQUEST['code'])) {
	$code = $_REQUEST['code'];
}

$rqtc=$bdd->prepare("UPDATE `policew` SET `sel`='0' WHERE `cod_pol`='$code'");
$rqtc->execute();



?>