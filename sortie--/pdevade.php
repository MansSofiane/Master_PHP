<?php 
session_start();
if ($_SESSION['loginAGA']){
//authentification acceptee !!!

}
else {
header("Location:../index.html?erreur=login"); // redirection en cas d'echec
}
require_once("../../../data/conn5.php");
include("convert.php");
$a1 = new chiffreEnLettre();
$errone = false;
require('fpdf.php');
if (isset($_REQUEST['warda'])) {$row = substr($_REQUEST['warda'],10);}
class PDF extends FPDF
{
// En-t?te
    function Header()
    {
        $this->SetFont('Arial','B',10);
        //$this->Image('../img/entete_bna.png',6,4,190);
        $this->Cell(150,5,'','O','0','L');
        $this->SetFont('Arial','B',12);
        // $this->Cell(60,5,'MAPFRE | Assistance','O','0','L');
        $this->SetFont('Arial','B',10);
        $this->Ln(8);
    }
    /*
    function Footer()
    {
        // Positionnement ? 1,5 cm du bas
        $this->SetY(-15);
        // Police Arial italique 8
        $this->SetFont('Arial','I',6);
        // Num?ro de page
        $this->Cell(0,8,'Page '.$this->PageNo().'/{nb}',0,0,'C');$this->Ln(3);
        $this->Cell(0,8,"Algerian Gulf Life Insurance Company, SPA au capital social de 1.000.000.000 de dinars algériens, 01 Rue Tripoli, Hussein Dey Alger,  ",0,0,'C');
        $this->Ln(2);
        $this->Cell(0,8,"RC : 16/00-1009727 B 15   NIF : 001516100972762-NIS :0015160900296000",0,0,'C');
        $this->Ln(2);
        $this->Cell(0,8,"Tel : +213 (0) 21 77 30 12/14/15 Fax : +213 (0) 21 77 29 56 Site Web : www.aglic.dz  ",0,0,'C');
        }*/
    function RotatedText($x,$y,$txt,$angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }

}
//Preparation du PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','B',50);
$pdf->Cell(0,6,"Devis - Gratuit",0,0,'C');$pdf->Ln(20);
$pdf->SetFont('Arial','B',30);
$pdf->Cell(0,6,"N° ".$row."",0,0,'C');$pdf->Ln(15);
$pdf->SetFont('Arial','B',20);
//$pdf->Ln(2);
$pdf->Cell(190,8,'Assurance Décès Emprunteur','0','0','C');$pdf->Ln();

$pdf->Ln(5);
//$pdf->Cell(190,8,'Devis Gratuit','0','0','C');$pdf->Ln();$pdf->Ln();
$pdf->SetFont('Arial','B',14);
//$pdf->Ln(2);


//Requete generale
$rqtg=$bdd->prepare("SELECT d.*,t.`mtt_dt`,c.`mtt_cpl`,o.`lib_opt`,p.`code_prod`, s.`nom_sous`, s.`pnom_sous`, s.`mail_sous`, s.`tel_sous`, s.`adr_sous`, s.`rp_sous`,s.`dnais_sous`,s.`age`, b.`nom_benef`, b.`ag_benef`,b.`tel_benef`,b.`adr_benef`  FROM `devisw` as d, `dtimbre` as t , `cpolice` as c,`option` as o,`produit` as p,`souscripteurw` as s,`beneficiaire` as b  WHERE d.`cod_dt`=t.`cod_dt` AND d.`cod_cpl`=c.`cod_cpl` AND d.`cod_opt`=o.`cod_opt` AND d.`cod_prod`=p.`cod_prod` AND d.`cod_sous`=s.`cod_sous` AND b.`cod_sous`=s.`cod_sous` AND d.`cod_dev`='$row'");
$rqtg->execute();

while ($row_g=$rqtg->fetch()){
// debut du traitement de la requete generale

// Le Souscripteur
    $pdf->SetFont('Arial','B',10);
    $pdf->Ln(3);
    $pdf->Cell(190,5,'Souscripteur ','1','1','C','1');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(40,5,'Nom et Prénom','1','0','L','1');
    $pdf->Cell(150,5,"".$row_g['nom_sous']." ".$row_g['pnom_sous']."",'1','0','C');$pdf->Ln();
    $pdf->Cell(40,5,'Adresse','1','0','L','1');$pdf->Cell(150,5,"".$row_g['adr_sous']."",'1','0','C');$pdf->Ln();
    $pdf->Cell(40,5,'Téléphone','1','0','L','1');$pdf->Cell(55,5,"".$row_g['tel_sous']."",'1','0','C');
    $pdf->Cell(40,5,'E-mail','1','0','L','1');$pdf->Cell(55,5,"".$row_g['mail_sous']."",'1','0','C');$pdf->Ln();
    $pdf->Ln(6);
// L'assuré
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,'Assuré ','1','1','C','1');
    $pdf->SetFont('Arial','B',8);
// la condition sur le souscripteur et l'assure
    if($row_g['rp_sous']==1){
        $pdf->Cell(40,5,'Nom et Prénom','1','0','L','1');$pdf->Cell(150,5,"".$row_g['nom_sous']." ".$row_g['pnom_sous']."",'1','0','C');$pdf->Ln();
        $pdf->Cell(40,5,'Adresse','1','0','L','1');$pdf->Cell(150,5,"".$row_g['adr_sous']."",'1','0','C');$pdf->Ln();
        $pdf->Cell(40,5,'Téléphone','1','0','L','1');$pdf->Cell(55,5,"".$row_g['tel_sous']."",'1','0','C');
        $pdf->Cell(40,5,'E-mail','1','0','L','1');$pdf->Cell(55,5,"".$row_g['mail_sous']."",'1','0','C');$pdf->Ln();
        $pdf->Cell(40,5,'D.Naissance','1','0','L','1');$pdf->Cell(55,5,"".date("d/m/Y",strtotime($row_g['dnais_sous']))."",'1','0','C');
        $pdf->Cell(40,5,'Age','1','0','L','1');$pdf->Cell(55,5,"".$row_g['age']."",'1','0','C');$pdf->Ln(6);
    }else{
// le souscripteur n'est pas l'assuré
        $rowa=$row_g['cod_sous'];
        $rqta=$bdd->prepare("SELECT s.`nom_sous`, s.`pnom_sous`, s.`mail_sous`, s.`tel_sous`, s.`adr_sous`, s.`rp_sous`,s.`dnais_sous`,s.`age`  FROM `souscripteurw` as s  WHERE  s.`cod_par`='$rowa'");
        $rqta->execute();
        while ($row_a=$rqta->fetch()){
            $pdf->Cell(40,5,'Nom et Prénom','1','0','L','1');$pdf->Cell(150,5,"".$row_a['nom_sous']." ".$row_a['pnom_sous']."",'1','0','C');$pdf->Ln();
            $pdf->Cell(40,5,'Adresse','1','0','L','1');$pdf->Cell(150,5,"".$row_a['adr_sous']."",'1','0','C');$pdf->Ln();
            $pdf->Cell(40,5,'Téléphone','1','0','L','1');$pdf->Cell(55,5,"".$row_a['tel_sous']."",'1','0','C');
            $pdf->Cell(40,5,'E-mail','1','0','L','1');$pdf->Cell(55,5,"".$row_a['mail_sous']."",'1','0','C');$pdf->Ln();
            $pdf->Cell(40,5,'D.Naissance','1','0','L','1');$pdf->Cell(55,5,"".date("d/m/Y",strtotime($row_a['dnais_sous']))."",'1','0','C');
            $pdf->Cell(40,5,'Age','1','0','L','1');$pdf->Cell(55,5,"".$row_a['age']."",'1','0','C');$pdf->Ln();
        }
//fin de la condition
    }

// Contrat
    $pdf->Ln(6);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,' Contrat ','1','0','C','1');$pdf->Ln();
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(50,5,'Effet le','1','0','L','1');$pdf->Cell(45,5,"".date("d/m/Y", strtotime($row_g['dat_eff']))."",'1','0','C');
    $pdf->Cell(50,5,'Echéance le','1','0','L','1');$pdf->Cell(45,5,"".date("d/m/Y", strtotime($row_g['dat_ech']))."",'1','0','C');$pdf->Ln();
    $pdf->Ln(6);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,' Garanties et Capital assuré','1','0','C','1');$pdf->Ln();
    $pdf->Cell(90,5,'Garanties','1','0','L','1');$pdf->Cell(100,5,'Décès et Invalidité Absolue et Définitive','1','0','C');$pdf->Ln();
    $pdf->Cell(90,5,'Capital Assuré','1','0','L','1');$pdf->Cell(100,5,"".number_format($row_g['cap1'], 2, ',', ' ')." DZD",'1','0','C');$pdf->Ln();
// Beneficiaire (Organisme preteur)
    $pdf->Ln(6);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(190,5,' Bénéficiaires','1','0','C','1');$pdf->Ln();
    $pdf->Cell(35,5,'Organisme préteur','1','0','L','1');$pdf->Cell(155,5,"".$row_g['nom_benef']."",'1','0','L');$pdf->Ln();
    $pdf->Cell(35,5,'Code agence','1','0','L','1');$pdf->Cell(155,5,"".$row_g['ag_benef']."",'1','0','L');$pdf->Ln();
    $pdf->Cell(35,5,'Téléphone','1','0','L','1');$pdf->Cell(155,5,"".$row_g['tel_benef']."",'1','0','L');$pdf->Ln();
    $pdf->Cell(35,5,'Adresse','1','0','L','1');$pdf->Cell(155,5,"".$row_g['adr_benef']."",'1','0','L');$pdf->Ln();


    $pdf->Ln(10);


    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(45,5,' Prime Nette ','1','0','C','1');$pdf->Cell(45,5,' Cout de Police ','1','0','C','1');
    $pdf->Cell(50,5,' Droit de timbre ','1','0','C','1');$pdf->Cell(50,5,' Prime Totale (DZD) ','1','0','C','1');
    $pdf->Ln();$pdf->SetFont('Arial','B',8);
    $pdf->Cell(45,5,"".number_format($row_g['pn'], 2, ',', ' ')."",'1','0','C');
    $pdf->Cell(45,5,"".number_format($row_g['mtt_cpl'], 2, ',', ' ')."",'1','0','C');
    $pdf->Cell(50,5,"".number_format($row_g['mtt_dt'], 2, ',', ' ')."",'1','0','C');
    $pdf->Cell(50,5,"".number_format($row_g['pt'], 2, ',', ' ')."",'1','0','C');$pdf->Ln();

    $pdf->Ln(35);$pdf->SetFont('Arial','B',50);
    $pdf->Cell(0,6,"Devis-Gratuit",0,0,'C');$pdf->Ln(2);$pdf->Ln(2);


// Fin du traitement de la requete generale
}



$pdf->Output();



?>








