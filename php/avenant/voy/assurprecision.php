<?php
session_start();
require_once("../../../../../data/conn5.php");
//on recupere le code du pays
if ($_SESSION['loginAGA']){
}
else {
    header("Location:login.php");
}
$id_user = $_SESSION['id_userAGA'];
$datesys=date("Y-m-d");
if ( isset($_REQUEST['code']))
{
    $code = $_REQUEST['code'];

    try
    {


    // $rqt=$bdd->prepare("DELETE FROM `assure` WHERE `cod_pol`='$code' AND `cod_av`='0' AND `id_user`='$id_user'");
        $rqt=$bdd->prepare("DELETE FROM `modif` WHERE `cod_pol`='$code'  AND `id_user`='$id_user'");
    $rqt->execute();
    } catch (Exception $ex) {
        echo 'Erreur : ' . $ex->getMessage() . '<br />';
        echo 'N° : ' . $ex->getCode();
    }

}