<?php session_start();
require_once("../../../data/conn5.php");
if ($_SESSION['loginAGA']){$user=$_SESSION['id_userAGA'];}
else {
    header("Location:../index.html?erreur=login"); // redirection en cas d'echec
}

if (isset($_REQUEST['d1']) && isset($_REQUEST['ag']) && isset($_REQUEST['d2'])) {
    $date1 = $_REQUEST['d1'];
    $agence = $_REQUEST['ag'];
    $date2 = $_REQUEST['d2'];
    $datesys = date("Y/m/d");

    include("convert.php");
    include("entete.php");

    $a1 = new chiffreEnLettre();



    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(255,255,255);

    //requete
    $rqtut=$bdd->prepare("select adr_user,nom,prenom,nif,com  from utilisateurs where agence='$agence' LIMIT 0,1");
    $rqtut->execute();

    $adr_user="";
    $nom="";
    $nif="";
    while($rowsu=$rqtut->fetch())
    {
        $adr_user=$rowsu['adr_user'];
        $nom=$rowsu['nom']." ".$rowsu['prenom'];
        $nif=$rowsu['nif'];
        $tcom=$rowsu['com'];
    }

    $pdf->Cell(190,5,"Le: ".date("d/m/Y", strtotime($datesys))."" ,'0','1','R');$pdf->Ln();
    $pdf->Cell(30,5,"AGA:" ,'0','0','L');
    $pdf->SetFillColor(231,229,231);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(70,5," ".$nom ,'0','0','L','1');$pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(40,5,"Adresse:" ,'0','0','L');
    $pdf->SetXY(40,55);
    $pdf->SetFont('Arial','',10);
    $pdf->MultiCell(70,5,"".$adr_user ,0,'L','1',false);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,5,"N.I.F.:" ,'0','0','L');
    $pdf->Cell(70,5," ".$nif."" ,'0','0','L','1');$pdf->Ln(5);
    $pdf->Cell(30,5,"Code Agence:" ,'0','0','L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(70,5," ".$agence."" ,'0','0','L','1');$pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,5,"A.I.:" ,'0','0','L');$pdf->Ln(5);
$pdf->SetXY(130,60);
    $pdf->MultiCell(90,5,"Doit a:
AGLIC Spa
01 Rue Tripoli Hussein Dey Alger " ,0,'L',false);
    $pdf->Ln(15);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(190,5,"Facture de Commission de Distribution" ,'0','0','C'); $pdf->Ln(10);
    $pdf->Cell(10,5,"N°:" ,'0','0','L');
    $pdf->SetFillColor(231,229,231);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,"       ".substr($date1,5,2)."/".substr($date1,0,4)."" ,'0','0','R','1');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,5,"         Période:" ,'0','0','L');
    $pdf->SetFillColor(231,229,231);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,5,'du         '.date("d/m/Y", strtotime($date1)).'             au        '.date("d/m/Y", strtotime($date2)) ,'0','0','L','1');
    $pdf->SetFont('Arial','B',10);
    $pdf->Ln(20);
    $pdf->Cell(20,10,"Code" ,'1','0','C','1');$pdf->Cell(40,10,"Désignation" ,'1','0','C','1');$pdf->Cell(30,10,"Prime Encaissée" ,'1','0','C','1');$pdf->Cell(40,10,"Mt primes ristournées" ,'1','0','L','1');
   $pdf->Cell(20,10,"Taux %" ,'1','0','C','1');$pdf->Cell(40,10,"Mt commission" ,'1','0','C','1');
    $pdf->Ln(10);

    $montantHT=0;
    $MontantTVA1=0;
    $MontantTVA2=0;
    $MontantTVA=0;
    $MontantTTC=0;
    $Montant_prim_com=0;
    $Montant_prime_rist=0;
    $montant_base=0;

    $Montant_prod=0;

    //LISTE PRODUITS
    $rqtprod=$bdd->prepare("select * from produit WHERE cod_prod not in (3,4)");
    $rqtprod->execute();
    // production positive
while ($rowprod=$rqtprod->fetch())
{

    $cod_prod=$rowprod['cod_prod'];
    $lib_prod=$rowprod['lib_prod'];
    $code_prod=$rowprod['code_prod'];
    $facteur=$rowprod['facteur'];

    $rqt = $bdd->prepare("
select count(cod_doc) as nb, sum(prime_nette) as prime_nette, sum(cout_police) as cout_police,sum(prime_com)as prime_com, sum(droit_timbre) as droit_timbre, sum(prime_totale) as prime_totale,sum(TVA) as TVA,table1.agence as agence,table1.cod_prod,table1.produits as produits
from
(

select p.sequence as cod_doc, p.dat_val as dat_val,p.ndat_eff as ndat_eff,p.ndat_ech as ndat_ech, p.pn as prime_nette, c.mtt_cpl as cout_police,p.pn+c.mtt_cpl as prime_com, d.mtt_dt as droit_timbre, p.pt as prime_totale,((p.pn+c.mtt_cpl)*pr.facteur/100*p.com)/100 as TVA,u.agence as agence,pr.cod_prod as cod_prod,pr.lib_prod as produits
from policew as p, dtimbre as d,cpolice as c ,souscripteurw as s,utilisateurs as u,produit as pr
where DATE_FORMAT(p.`dat_val`,'%Y-%m-%d') between '$date1' and '$date2'  and p.cod_dt=d.cod_dt and p.cod_cpl=c.cod_cpl and p.cod_prod=pr.cod_prod
and p.cod_sous=s.cod_sous and s.id_user=u.id_user and u.agence='$agence' and p.cod_prod='$cod_prod'

union

select v.sequence as cod_doc,p.dat_val as dat_val,p.ndat_eff as ndat_eff,p.ndat_ech as ndat_ech,v.pn as prime_nette, c.mtt_cpl as cout_police,v.pn+c.mtt_cpl as prime_com, d.mtt_dt as droit_timbre, v.pt as prime_totale,((v.pn+c.mtt_cpl)*pr.facteur/100*v.com)/100 as TVA,u.agence as agence,pr.cod_prod as cod_prod,pr.lib_prod as produits

from avenantw as v,policew as p, dtimbre as d,cpolice as c ,souscripteurw as s,utilisateurs as u,produit as pr

where DATE_FORMAT(v.`dat_val`,'%Y-%m-%d') between '$date1' and '$date2'  and v.cod_dt=d.cod_dt and v.cod_cpl=c.cod_cpl and v.cod_prod=pr.cod_prod
and p.cod_sous=s.cod_sous and s.id_user=u.id_user and v.cod_pol= p.cod_pol and u.agence='$agence' and v.lib_mpay not in ('30') and v.cod_prod='$cod_prod') as table1
group by table1.agence,table1.cod_prod

");
    $rqt->execute();
    $MontantTVA1=0;
    $Montant_prim_com=0;
    $mttTVA=0;
    while ($rows=$rqt->fetch())
    {

        $Montant_prim_com=$rows['prime_com'];
        $MontantTVA1=$rows['TVA'];
    }



    //production avec ristourne

    $rqtv = $bdd->prepare("
select count(cod_doc) as nb, sum(prime_nette) as prime_nette, sum(cout_police) as cout_police,sum(prime_com)as prime_com, sum(droit_timbre) as droit_timbre, sum(prime_totale) as prime_totale,sum(TVA) as TVA,table1.agence as agence,table1.cod_prod,table1.produits as produits
from
(


select v.sequence as cod_doc,p.dat_val as dat_val,p.ndat_eff as ndat_eff,p.ndat_ech as ndat_ech,v.pn as prime_nette, c.mtt_cpl as cout_police,v.pn+c.mtt_cpl as prime_com, d.mtt_dt as droit_timbre, v.pt as prime_totale,((v.pn+c.mtt_cpl)*pr.facteur/100*v.com)/100 as TVA,u.agence as agence,pr.cod_prod as cod_prod,pr.lib_prod as produits

from avenantw as v,policew as p, dtimbre as d,cpolice as c ,souscripteurw as s,utilisateurs as u,produit as pr

where DATE_FORMAT(v.`dat_val`,'%Y-%m-%d') between '$date1' and '$date2'  and v.cod_dt=d.cod_dt and v.cod_cpl=c.cod_cpl and v.cod_prod=pr.cod_prod
and p.cod_sous=s.cod_sous and s.id_user=u.id_user and v.cod_pol= p.cod_pol and u.agence='$agence' AND v.lib_mpay='30' and v.cod_prod='$cod_prod') as table1
group by table1.agence,table1.cod_prod

");
    $rqtv->execute();
    $MontantTVA2=0;
    $Montant_prime_rist=0;
    while ($rowsv=$rqtv->fetch())
    {

        $Montant_prime_rist=$rowsv['prime_com'];
        $MontantTVA2=$rowsv['TVA'];
    }

$montant_base=$Montant_prim_com+$Montant_prime_rist;
    $Montant_prod=$montant_base*$facteur/100;
    $montantHT=$montantHT+$Montant_prod;
    $MontantTVA=$MontantTVA+$MontantTVA1+$MontantTVA2;

    $pdf->Cell(20,7,"".$code_prod ,'1','0','C');
    $pdf->Cell(40,7,"".$lib_prod ,'1','0','C');
    $pdf->Cell(30,7,"".number_format($Montant_prim_com, 2,',',' ') ,'1','0','C');
    $pdf->Cell(40,7,"".number_format($Montant_prime_rist, 2,',',' ') ,'1','0','C');
    $pdf->Cell(20,7,"".number_format($facteur, 2,',',' ') ,'1','0','C');
    $pdf->Cell(40,7,"".number_format($Montant_prod, 2,',',' ') ,'1','0','C');
    $pdf->Ln();

    $Montant_prim_com=0;
    $Montant_prime_rist=0;
    $facteur=0;
    $MontantTVA1=0;$MontantTVA2=0;
}
$y=$pdf->GetY();
    $pdf->SetXY(70,$y);
    $pdf->Cell(90,7,"Montant HT" ,'1','0','L','1'); $pdf->Cell(40,7,"".number_format($montantHT, 2,',',' ') ,'1','0','C');
    $pdf->Ln();
    $y=$pdf->GetY();
    $pdf->SetXY(70,$y);
    $pdf->Cell(90,7,"TVA 19% " ,'1','0','L','1'); $pdf->Cell(40,7,"".number_format(($montantHT*($tcom/100)), 2,',',' ') ,'1','0','C');
    $pdf->Ln();
    $y=$pdf->GetY();
    $pdf->SetXY(70,$y);
    //$MontantTTC=$montantHT+$MontantTVA;
    $MontantTTC=$montantHT+($montantHT*($tcom/100));
    $pdf->Cell(90,7,"Montant TTC" ,'1','0','L','1'); $pdf->Cell(40,7,"".number_format($MontantTTC, 2,',',' ') ,'1','0','C');
    $pdf->Ln(15);


    $somme=$a1->ConvNumberLetter("".$MontantTTC."",1,0);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,5,"Le montant de la facture en lettres",'0','0','L');$pdf->Ln();
    $pdf->SetFont('Arial','B',12);$pdf->SetFillColor(255,255,255);
    $pdf->MultiCell(190,12,"".$somme."",1,'C',true);
    $pdf->Ln(15);
    $y=$pdf->GetY();
    $pdf->SetXY(100,$y);
    $pdf->MultiCell(100,5,"Le Directeur",0,'C',false);








    $pdf->Output();
}
?>