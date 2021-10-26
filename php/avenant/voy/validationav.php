<?php
session_start();
require_once("../../../../../data/conn5.php");
//on recupere le code du pays
// xhr.open("GET", "php/avenant/voy/validationav.php?code=" + codedev + "&date1=" + datedeb + "&date2=" + datfin + "&av=" + av+"&mode="+mode+"&datop="+dateop+"&pays="+pays, false);

$id_user = $_SESSION['id_userAGA'];
$datesys=date("y-m-d H:i:s");
if ( isset($_REQUEST['code']) && isset($_REQUEST['av']))
{
	$code = $_REQUEST['code'];
	$av = $_REQUEST['av'];
	$date1="2099-12-31";$date2="2099-12-31";
	//$datesys=date("y-m-d H:i:s");
	if($_REQUEST['date1']!= NULL ){$date1=$_REQUEST['date1']; echo $date1; }
	if($_REQUEST['date2']!= NULL ){$date2=$_REQUEST['date2']; echo $date2;}
	$pays=$_REQUEST['pays'];//cas changement de la destination.
	$new_duree=$_REQUEST['dur'];// dans le cas d'augmentation de la dur�e

	$modeav=$_REQUEST['mode'];
	$datopav=$_REQUEST['datop'];

	$rqtds=$bdd->prepare("SELECT `com` as com from `utilisateurs` WHERE `id_user`='$id_user'");
	$rqtds->execute();
	$com=0;
	while($rowsusr=$rqtds->fetch())
	{
		$com=$rowsusr['com'];
	}

//On r�cupere les infos de la police
	
	$rqtd=$bdd->prepare("SELECT * from `policew` WHERE `cod_pol`='$code'");
	$rqtd->execute();
	while ($row_res=$rqtd->fetch()){
	$tar=$row_res['cod_tar'];
	$prod=$row_res['cod_prod'];
	$per=$row_res['cod_per'];
	$opt=$row_res['cod_opt'];
	$zone=$row_res['cod_zone'];
	$cod_pays=	$row_res['cod_pays'];
	$formul=$row_res['cod_formul'];
	$dt=$row_res['cod_dt'];
	$cpl=$row_res['cod_cpl'];
	$deff=$row_res['dat_eff'];
	$dech=$row_res['dat_ech'];
	$ndeff=$row_res['ndat_eff'];
	$ndech=$row_res['ndat_ech'];
	$cap1=$row_res['cap1'];
	$cap2=$row_res['cap2'];
	$cap3=$row_res['cap3'];
	$p1=$row_res['p1'];
	$p2=$row_res['p2'];
	$p3=$row_res['p3'];
	$pn=$row_res['pn'];
	$pt=$row_res['pt'];
	$cod_sous=$row_res['cod_sous'];
	//On r�cupere la sequence du produit
	$rqts=$bdd->prepare("SELECT sequence2 FROM `produit` WHERE `cod_prod`='$prod'");
	$rqts->execute();
	while ($row_ress=$rqts->fetch()){
	$seq=$row_ress['sequence2'];
	}
	$rqt_zn_pays=$bdd->prepare("select cod_zone,cod_pays from avenantw where cod_pol='$code' and cod_av= (select max(cod_av) from avenantw where cod_pol='$code');");
		$rqt_zn_pays->execute();
		while ($rw=$rqt_zn_pays->fetch())
		{
			$zone=$rw['cod_zone'];
			$cod_pays=	$rw['cod_pays'];
		}

$seq++;
	$rqtsous = $bdd->prepare("select * from souscripteurw where cod_sous='$cod_sous'");
	$rqtsous->execute();
	$nb_assur=0;
	while ($rowsous=$rqtsous->fetch())
	{
		$rp_sous=$rowsous['rp_sous'];
		$nb_assur=$rowsous['nb_assu'];
		$agesous=$rowsous['age'];

	}

//selectionner les information de souscripteur. dans le cas d'avenant de changement de destination.
	if($pays!="") {

		$rqtzone = $bdd->prepare("select cod_zone from pays where cod_pays='$pays'");
		$rqtzone->execute();
		while ($rowszone=$rqtzone->fetch())
		{
			$cod_zonenew=$rowszone['cod_zone'];

		}
	}

	// recu�rer les informations necessaires en cas d'augmentation du dur�e.


if($av==74){
//Avenant de modification de date
//on insere dans la table avenantw
$rqtiav=$bdd->prepare("INSERT INTO `avenantw`(`cod_av`, `dat_val`, `cod_tar`, `cod_prod`, `cod_per`, `cod_opt`, `cod_zone`, `cod_pays`, `cod_formul`, `cod_dt`, `cod_cpl`, `dat_eff`, `dat_ech`, `cap1`, `cap2`, `cap3`, `p1`, `p2`, `p3`, `pn`, `pt`, `ndat_eff`, `ndat_ech`, `mode`, `dat_op`, `lib_mpay`, `sequence`, `etat`, `cod_pol`, `cod_mot`,`com`)  VALUES ('', '$datesys', '$tar', '$prod', '$per', '$opt', '$zone','$cod_pays', '$formul', '$dt', '6', '$date1', '$date2','$cap1', '$cap2', '$cap3', '0', '0', '0', '0', '140', '$date1','$date2', '1','','$av','$seq', '0', '$code','','$com')");
$rqtiav->execute();
//on met � jour la ndat_effet et ndat_ech de la police
$rqtc=$bdd->prepare("UPDATE `policew` SET `ndat_eff`= '$date1',`ndat_ech`= '$date2' WHERE `cod_pol`='$code'");
$rqtc->execute();
//On incremente la sequence
$rqtc=$bdd->prepare("UPDATE `produit` SET `sequence2`= '$seq' WHERE `cod_prod`='$prod'");
$rqtc->execute();
}
if($av==70){
//Avenant de precision
//on insere dans la table policew
	$rqtiav=$bdd->prepare("INSERT INTO `avenantw`(`cod_av`, `dat_val`, `cod_tar`, `cod_prod`, `cod_per`, `cod_opt`, `cod_zone`, `cod_pays`, `cod_formul`, `cod_dt`, `cod_cpl`, `dat_eff`, `dat_ech`, `cap1`, `cap2`, `cap3`, `p1`, `p2`, `p3`, `pn`, `pt`, `ndat_eff`, `ndat_ech`, `mode`, `dat_op`, `lib_mpay`, `sequence`, `etat`, `cod_pol`, `cod_mot`)  VALUES ('', '$datesys', '$tar', '$prod', '$per', '$opt', '$zone','$cod_pays', '$formul', '$dt', '6', '$date1', '$date2','$cap1', '$cap2', '$cap3', '0', '0', '0', '0', '140', '$deff','$dech', '$modeav','$datopav','$av','$seq', '0', '$code','')");
	$rqtiav->execute();
//On incremente la sequence
	$rqtc=$bdd->prepare("UPDATE `produit` SET `sequence2`= '$seq' WHERE `cod_prod`='$prod'");
	$rqtc->execute();
//recup�rer le max cod_av
	$rqtmav=$bdd->prepare("SELECT max(cod_av) as maxav FROM avenantw where cod_pol='$code' and lib_mpay='70' ");
	$rqtmav->execute();
	while ($rowav=$rqtmav->fetch())
	{
		$cod_av=$rowav['maxav'];
	}
//mise a jour modif
	$rqtassur=$bdd->prepare("UPDATE modif set cod_av='$cod_av' where cod_pol='$code' and id_user='$id_user' and cod_par='$cod_sous'");
	$rqtassur->execute();

	// inserer le resultat dans assure

	$rqtassur=$bdd->prepare("insert into assure (`nom_assu`, `pnom_assu`, `passport`, `datedpass`, `datefpass`, `mail_assu`, `tel_assu`, `adr_assu`, `age_assu`, `sexe`, `cod_sous`, `cod_pol`, `cod_av`, `id_user`, `cod_par` , `modif_sous`) (select `nom_assu`, `pnom_assu`, `passport`, `datedpass`, `datefpass`, `mail_assu`, `tel_assu`, `adr_assu`, `age_assu`, `sexe`, `cod_sous`, `cod_pol`, `cod_av`, `id_user`, `cod_par` , `modif_sous` from  modif where cod_pol='$code' and id_user='$id_user' and cod_par='$cod_sous')");
	$rqtassur->execute();

	//supprimer modif

	$rqtassur=$bdd->prepare("delete from  modif  where cod_pol='$code' and id_user='$id_user' and cod_par='$cod_sous'");
	$rqtassur->execute();


}

	if($av==14) {
		$date1=$ndeff;
		$date2=$ndech;
		//avenant de changement de destination
		$rqtmx=$bdd->prepare("SELECT MAX(p.cod_av) as cod from avenantw as p  where p.cod_pol='$code' and p.lib_mpay='14'");
		$rqtmx->execute();
		$cod_avmaw="";
		while($rowwx=$rqtmx->fetch())
		{
			$cod_avmaw=$rowwx['cod'];
		}

		if($cod_avmaw!="")
		{
			$rqtzone=$bdd->prepare("select p.cod_av as cod,p.cod_zone as cod_zone,p.cod_pays as cod_pays,p.dat_val as dat_val ,y.lib_pays as lib_pays
                            from avenantw as p , pays as y where cod_pol='$code' and p.cod_pays=y.cod_pays and p.cod_zone=y.cod_zone and p.lib_mpay='14'  and p.cod_av ='$cod_avmaw'");

			$rqtzone->execute();
		}
		else
		{
			$rqtzone=$bdd->prepare("select p.cod_pol as cod,p.cod_zone as cod_zone,p.cod_pays as cod_pays,p.dat_val as dat_val ,y.lib_pays as lib_pays from policew as p , pays as y where cod_pol='$code' and p.cod_pays=y.cod_pays ");
			$rqtzone->execute();
		}
		while ($rowszone=$rqtzone->fetch()) {
			$cod_zoneinit=$rowszone['cod_zone'];
			$cod_pays=$rowszone['cod_pays'];
			$lib_pays=$rowszone['lib_pays'];
		}
		if($cod_zonenew==$cod_zoneinit)
		{
			$rqtiav=$bdd->prepare("INSERT INTO `avenantw`(`cod_av`, `dat_val`, `cod_tar`, `cod_prod`, `cod_per`, `cod_opt`, `cod_zone`, `cod_pays`, `cod_formul`, `cod_dt`, `cod_cpl`, `dat_eff`, `dat_ech`, `cap1`, `cap2`, `cap3`, `p1`, `p2`, `p3`, `pn`, `pt`, `ndat_eff`, `ndat_ech`, `mode`, `dat_op`, `lib_mpay`, `sequence`, `etat`, `cod_pol`, `cod_mot`,`com`)  VALUES ('', '$datesys', '$tar', '$prod', '$per', '$opt', '$cod_zonenew','$pays', '$formul', '$dt', '6', '$date1', '$date2','$cap1', '$cap2', '$cap3', '$p1', '$p2', '$p3', '0', '140', '$date1','$date2', '1','','$av','$seq', '0', '$code','','$com')");
			$rqtiav->execute();
			//on supprime de devis
			//$rqtc=$bdd->prepare("UPDATE `policew` SET `ndat_eff`= '$date1',`ndat_ech`= '$date2' WHERE `cod_pol`='$code'");
			//$rqtc->execute();
			//On incremente la sequence
			$rqtc=$bdd->prepare("UPDATE `produit` SET `sequence2`= '$seq' WHERE `cod_prod`='$prod'");
			$rqtc->execute();

		}
		else {
			$pnga_global=0;$pngb_global=0;$prime_nette=0;
			$rqtd=$bdd->prepare("SELECT * from `avenantw` WHERE `cod_pol`='$code' order by cod_av DESC LIMIT 0,1 ");
			$rqtd->execute();
			while ($row_res=$rqtd->fetch())
			{
			$per=$row_res['cod_per'];
			}
			if($formul==2)//indeviduelle
			{
				$rqtaffich = $bdd->prepare("select * from souscripteurw where cod_sous = '$cod_sous' ");
				$rqtaffich-> execute();
				while($rowind = $rqtaffich->fetch())
				{
					$ageind = $rowind['age'];
				}
				$rqtassur = $bdd->prepare("select * from souscripteurw where cod_par = '$cod_sous' ");
				$rqtassur-> execute();
				//si le souscripteur est l'assur� la boucle suivante ne retourne rien d'ou on $age garde l'ancienne valeur. [le souscripteur est l'assur�]
				while($rowas = $rqtassur->fetch())
				{
					$ageind = $rowas['age'];
				}
				$rqtselect = $bdd->prepare("SELECT a.* FROM `tarif` as a , pays as b WHERE a.cod_prod='1' and a.cod_formul='1' and a.cod_opt= '$opt'
       				and a.agemin <= '$ageind' and a.agemax >= '$ageind' and a.cod_per= '$per'  and b.cod_pays= '$pays'
       				and b.cod_zone = a.cod_zone and a.cod_cpl ='$cpl' ");
				

				
				$rqtselect -> execute();
				$i=0;
				$paind=0;$pbind=0;
				while ($row1=$rqtselect->fetch())
				{
					$paind = $row1['pa'] + (($row1['maj_pa'] * $row1['pa']) / 100) - (($row1['rab_pa'] * $row1['pa']) / 100);
					$pbind = $row1['pe'] + (($row1['maj_pe'] * $row1['pe']) / 100) - (($row1['rab_pe'] * $row1['pe']) / 100);
				}
				$pnga_global=$paind;
				$pngb_global=$pbind;
				$prime_nette=$paind+$pbind;
				//fin individuelle

			}
			if ($formul==3)//COUPLE
			{
				$reqtnb=$bdd->prepare("select * from souscripteurw where cod_par='$cod_sous'");
				$reqtnb->execute();
				$nbsous= $reqtnb->rowCount();

				// recuperation de l'age des assures. et le code de ouscripteur de chacun.
				$codsous1=0;
				$codsous2=0;
				$age1=0;
				$age2=0;
				if($nbsous==1)//le souscripteurs est lui assure
				{
					$codsous1=$cod_sous;

					$reqt1=$bdd->prepare("select *from souscripteurw where cod_sous='$cod_sous'");
					$reqt1->execute();

					while ($row1=$reqt1->fetch())
					{
						$age1=$row1['age'];
					}

					$reqt2=$bdd->prepare("select *from souscripteurw where cod_par='$cod_sous' LIMIT 1");
					$reqt2->execute();

					while ($row2=$reqt2->fetch())
					{
						$codsous2=$row2['cod_sous'];
						$age2=$row2['age'];
					}
				}
				else
				{
					$reqt1=$bdd->prepare("select *from souscripteurw where cod_par='$cod_sous' LIMIT 1");
					$reqt1->execute();

					while ($row1=$reqt1->fetch())
					{
						$codsous1=$row1['cod_sous'];
						$age1=$row1['age'];
					}
					// SELECT * FROM `souscripteurw` WHERE `cod_par`='8250' LIMIT 1,1
					$reqt2=$bdd->prepare("select *from souscripteurw where cod_par='$cod_sous' LIMIT 1,1");
					$reqt2->execute();

					while ($row2=$reqt2->fetch())
					{
						$codsous2=$row2['cod_sous'];
						$age2=$row2['age'];
					}
				}
				$reqtar1=$bdd->prepare("SELECT a.* FROM `tarif` as a , pays as b WHERE cod_prod='1' and cod_formul='1' and cod_opt='$opt'  and agemin <= '$age1' and agemax >='$age1' and cod_per='$per' and b.cod_pays='$pays'  and b.cod_zone=a.cod_zone and cod_cpl='$cpl'");
				$reqtar1->execute();

				while($row_a1=$reqtar1->fetch())
				{
					$pa1 = $row_a1['pa'] + (($row_a1['maj_pa'] * $row_a1['pa']) / 100) - (($row_a1['rab_pa'] * $row_a1['pa']) / 100);
					$pg1 = $row_a1['pe']+ (($row_a1['maj_pe'] * $row_a1['pe']) / 100) - (($row_a1['rab_pe'] * $row_a1['pe']) / 100);
				}

				$reqtar2=$bdd->prepare("SELECT a.* FROM `tarif` as a , pays as b WHERE cod_prod='1' and cod_formul='1' and cod_opt='$opt'  and agemin <= '$age2' and agemax >='$age2' and cod_per='$per' and b.cod_pays='$pays'  and b.cod_zone=a.cod_zone and cod_cpl='$cpl'");

				$reqtar2->execute();
				while($row_a2=$reqtar2->fetch())
				{
					$pa2 =$row_a2['pa'] + (($row_a2['maj_pa']*$row_a2['pa'])/100)- (($row_a2['rab_pa']*$row_a2['pa'])/100);
					$pg2= $row_a2['pe'] + (($row_a2['maj_pe']*$row_a2['pe'])/100)- (($row_a2['rab_pe']*$row_a2['pe'])/100);
				}
					$pngb_global = $pg1 + $pg2;
					$pnga_global = ($pa1 + $pa2) * 0.75;
					$prime_nette = $pngb_global + $pnga_global;
			}

			if ($formul==4)//famille
			{
				$rqtaffich = $bdd->prepare("select * from souscripteurw where cod_par = '$cod_sous' ");
				$rqtaffich->execute();
				while ($row = $rqtaffich->fetch()) {
					$age = $row['age'];
					$primei = 0;

					$rqtselect = $bdd->prepare("SELECT a.* FROM `tarif` as a , pays as b WHERE a.cod_prod='1' and a.cod_formul='1' and a.cod_opt= '$opt'
                                                 and a.agemin <= '$age' and a.agemax >= '$age' and a.cod_per= '$per'  and b.cod_pays= '$pays' and b.cod_zone = a.cod_zone and a.cod_cpl ='$cpl' ");
					$rqtselect->execute();

					while ($row1 = $rqtselect->fetch()) {

						$pngb_global = $pngb_global + ($row1['pe'] + (($row1['maj_pe'] * $row1['pe']) / 100) - (($row1['rab_pe'] * $row1['pe']) / 100));
						$pt1 = $row1['pa'] + (($row1['maj_pa'] * $row1['pa']) / 100) - (($row1['rab_pa'] * $row1['pa']) / 100);
						$pnga_global = $pnga_global + $pt1;
						$cpt++;
					}
				}
				if ($cpt == 3) {
					$rabf = (3 - 2) / 3;
				}
				if ($cpt == 4) {
					$rabf = (4 - 2.5) / 4;
				}
				if ($cpt == 5) {
					$rabf = (5 - 3) / 5;
				}
				if ($cpt == 6) {
					$rabf = (6 - 3.5) / 6;
				}
				if ($cpt >= 7) {
					$rabf = ($cpt - 4) / $cpt;
				}
				$pnga_global = $pnga_global *(1-$rabf);
				$prime_nette = $pnga_global + $pngb_global;
			}

			if ($formul==5)//Groupe
			{
				$rqtaffich = $bdd->prepare("select * from souscripteurw where cod_par = '$cod_sous' ");
				$rqtaffich-> execute();
				$cpt=0;
				while($row = $rqtaffich->fetch()) {
					$age = $row['age'];
					$primei = 0;
					// pour chaque assur� on calcule la prime qui lui conevient
					$rqtselect = $bdd->prepare("SELECT a.* FROM `tarif` as a , pays as b WHERE a.cod_prod='1' and a.cod_formul='1' and a.cod_opt= '$opt'
       				and a.agemin <= '$age' and a.agemax >= '$age' and a.cod_per= '$per'  and b.cod_pays= '$pays'
       				and b.cod_zone = a.cod_zone and a.cod_cpl ='$cpl' ");
					$rqtselect->execute();

					while ($row1 = $rqtselect->fetch()) {

						$pngb_global = $pngb_global + ( $row1['pe'] + (($row1['maj_pe'] * $row1['pe']) / 100) - (($row1['rab_pe'] * $row1['pe']) / 100));
						$pt1 = $row1['pa'] + (($row1['maj_pa'] * $row1['pa']) / 100) - (($row1['rab_pa'] * $row1['pa']) / 100);
						$pnga_global = $pnga_global + $pt1;
						$cpt++;
					}
				}
				if($opt<30){
					if($cpt >= 10 && $cpt <= 20){ $rabg=0.95;}
					if($cpt >= 21 && $cpt <= 50){ $rabg=0.90;}
					if($cpt >= 51 && $cpt <= 100){ $rabg=0.85;}
					if($cpt >= 101){ $rabg=0.75;}
				}else{
					$rabg=1;
				}
				$pnga_global=$pnga_global*$rabg;
				$prime_nette=$pnga_global+$pngb_global;
			}
			//FAIRE LA DIFFERENCE
			$reqt=$bdd->prepare("select sum(pn) as pnav,sum(p1) as p1av,sum(p2) as p2av from avenantw where cod_pol='$code'");
        	$reqt->execute();
        	$pnav=0;$p1av=0;$p1av=0;
        	while ($row1=$reqt->fetch()) 
        	{
        	    $pnav = $row1['pnav'];
        	    $p1av = $row1['p1av'];
        	    $p2av = $row1['p2av'];
        	}
			$diffprime=$prime_nette-($pn+$pnav);
			//$diffprime=$prime_nette-$pn;
			$primtotal=$diffprime+140;
			$rqtiav = $bdd->prepare("INSERT INTO `avenantw`(`cod_av`, `dat_val`, `cod_tar`, `cod_prod`, `cod_per`, `cod_opt`, `cod_zone`, `cod_pays`, `cod_formul`, `cod_dt`, `cod_cpl`, `dat_eff`, `dat_ech`, `cap1`, `cap2`, `cap3`, `p1`, `p2`, `p3`, `pn`, `pt`, `ndat_eff`, `ndat_ech`, `mode`, `dat_op`, `lib_mpay`, `sequence`, `etat`, `cod_pol`, `cod_mot`,`com`)  VALUES ('', '$datesys', '$tar', '$prod', '$per', '$opt', '$cod_zonenew','$pays', '$formul', '$dt', '6', '$date1', '$date2','$cap1', '$cap2', '$cap3', '$p1', '$p2', '$p3', '$diffprime', '$primtotal', '$deff','$dech', '$modeav','$datopav','$av','$seq', '0', '$code','','$com')");
			$rqtiav->execute();
            //On incremente la sequence
			$rqtc = $bdd->prepare("UPDATE `produit` SET `sequence2`= '$seq' WHERE `cod_prod`='$prod'");
			$rqtc->execute();
		}
	}
if($av==30){
//Avenant d'annulation Avec Ristourne
//recup�rer la prime nette des avenants
	$rqt_av=$bdd->prepare("select sum(pn) as pnav from avenantw where cod_pol='$code'");
	$rqt_av->execute();
	$pn_av=0;
	while ($row_av=$rqt_av->fetch())
	{
		$pn_av=$row_av['pnav'];
	}
$pnn=($pn+$pn_av)*(-1);
$ptn=$pnn+140;
	$p1n=-$p1;
	$p2n=-$p2;
$rqtiav=$bdd->prepare("INSERT INTO `avenantw`(`cod_av`, `dat_val`, `cod_tar`, `cod_prod`, `cod_per`, `cod_opt`, `cod_zone`, `cod_pays`, `cod_formul`, `cod_dt`, `cod_cpl`, `dat_eff`, `dat_ech`, `cap1`, `cap2`, `cap3`, `p1`, `p2`, `p3`, `pn`, `pt`, `ndat_eff`, `ndat_ech`, `mode`, `dat_op`, `lib_mpay`, `sequence`, `etat`, `cod_pol`, `cod_mot`,`com`)  VALUES ('', '$datesys', '$tar', '$prod', '$per', '$opt', '$zone','$cod_pays', '$formul', '$dt', '6', '$deff', '$date2','$cap1', '$cap2', '$cap3', '$p1n', '$p2n', '$p3', '$pnn', '$ptn', '$deff','$dech', '1','','$av','$seq', '0', '$code','','$com')");
$rqtiav->execute();
//on supprime de devis
$rqtc=$bdd->prepare("UPDATE `policew` SET `etat`= '2' WHERE `cod_pol`='$code'");
$rqtc->execute();
//On incremente la sequence
$rqtc=$bdd->prepare("UPDATE `produit` SET `sequence2`= '$seq' WHERE `cod_prod`='$prod'");
$rqtc->execute();
}
if($av==50){
//Avenant d'annulation Avec Ristourne
//
	$rqt_av=$bdd->prepare("select sum(pn) as pnav,sum(pt) as ptav from avenantw where cod_pol='$code'");
	$rqt_av->execute();
	$pn_av=0;$pt_av=0;
	$cod_cplav=4;
	if ($cpl==3)
	{
		$cod_cplav=5;
	}
	while ($row_av=$rqt_av->fetch())
	{
		$pn_av=$row_av['pnav'];
		$pt_av=$row_av['ptav'];
	}
    $pnn=($pn+$pn_av)*(-1);
	$ptn=($pt+$pt_av)*(-1);
	$p1n=$p1*(-1);
	$p2n=$p2*(-1);
$rqtiav=$bdd->prepare("INSERT INTO `avenantw`(`cod_av`, `dat_val`, `cod_tar`, `cod_prod`, `cod_per`, `cod_opt`, `cod_zone`, `cod_pays`, `cod_formul`, `cod_dt`, `cod_cpl`, `dat_eff`, `dat_ech`, `cap1`, `cap2`, `cap3`, `p1`, `p2`, `p3`, `pn`, `pt`, `ndat_eff`, `ndat_ech`, `mode`, `dat_op`, `lib_mpay`, `sequence`, `etat`, `cod_pol`, `cod_mot`,`com`)  VALUES ('', '$datesys', '$tar', '$prod', '$per', '$opt', '$zone','$cod_pays', '$formul', '4', '$cod_cplav', '$deff', '$date2','$cap1', '$cap2', '$cap3', '$p1n', '$p2n', '$p3', '$pnn', '$ptn', '$deff','$dech', '1','','$av','$seq', '0', '$code','','$com')");
$rqtiav->execute();
//on supprime de devis
$rqtc=$bdd->prepare("UPDATE `policew` SET `etat`= '3' WHERE `cod_pol`='$code'");
$rqtc->execute();
//On incremente la sequence
$rqtc=$bdd->prepare("UPDATE `produit` SET `sequence2`= '$seq' WHERE `cod_prod`='$prod'");
$rqtc->execute();
}

if($av==17)	//augmentation de dur�e
	{

		$reqper = $bdd->prepare("SELECT * FROM `periode` WHERE `max_jour`>= '$new_duree' AND `trt_per`>=1 LIMIT 1 ");
		$reqper->execute();
		$cod_per = 0;
		while ($rowper = $reqper->fetch()) {
			$cod_per = $rowper['cod_per'];
		}
		//corps
		if($formul==2)//formule individuelle
		{
			$rqt_age_assur=$bdd->prepare("SELECT age from souscripteurw where cod_par='$cod_sous'");
			$rqt_age_assur->execute();
		
		
			while($rw=$rqt_age_assur->fetch())
			{
			$agesous=$rw['age'];
			}
			//echo "cod_per=".$cod_per;
			//recuperer l etarif
	   		$rqtselect = $bdd->prepare("SELECT a.* FROM `tarif` as a , pays as b WHERE a.cod_prod='1' and a.cod_formul='1' and a.cod_opt= '$opt'
       		and a.agemin <= '$agesous' and a.agemax >= '$agesous' and a.cod_per= '$cod_per'  and b.cod_pays= '$cod_pays'
       		and b.cod_zone = a.cod_zone and a.cod_cpl ='$cpl' ");
			$rqtselect -> execute();
			$i=0;
			$paind=0;$pbind=0;
			while ($row1=$rqtselect->fetch())
			{
				$per=$row1['cod_per'];
				$tar=$row1['cod_tar'];
				$paind = $row1['pa'] + (($row1['maj_pa'] * $row1['pa']) / 100) - (($row1['rab_pa'] * $row1['pa']) / 100);
				$pbind = $row1['pe'] + (($row1['maj_pe'] * $row1['pe']) / 100) - (($row1['rab_pe'] * $row1['pe']) / 100);
			}
			$pnga_global=$paind;$pngb_global=$pbind;
			$prime_nette=$paind+$pbind;
		}
		//corps
		if($formul==3)//formule Couple
		{
			// recuperer les souscripteurs 1 et 2.
    		// si le nombre de lignes egale a un donc le souscripteur cest lui assure
    		// si le nombre de lignes egale a deux donc le souscripteur nest pas l assure
    		$reqtnb=$bdd->prepare("select * from souscripteurw where cod_par='$cod_sous'");
    		$reqtnb->execute();
    		$nbsous= $reqtnb->rowCount();
    		// recuperation de l'age des assures. et le code de ouscripteur de chacun.
    		$codsous1=0;
    		$codsous2=0;
    		$age1=0;
    		$age2=0;
    		if($nbsous==1)
			{
        		$codsous1=$cod_sous;
        		$reqt1=$bdd->prepare("select *from souscripteurw where cod_sous='$cod_sous'");
        		$reqt1->execute();
        		while ($row1=$reqt1->fetch())
        		{
        		    $age1=$row1['age'];
        		}
        		$reqt2=$bdd->prepare("select *from souscripteurw where cod_par='$cod_sous' LIMIT 1");
        		$reqt2->execute();

        		while ($row2=$reqt2->fetch())
        		{
        		    $codsous2=$row2['cod_sous'];
        		    $age2=$row2['age'];

        		}
			}
    		else
    		{
        		$reqt1=$bdd->prepare("select *from souscripteurw where cod_par='$cod_sous' LIMIT 1");
        		$reqt1->execute();
        		while ($row1=$reqt1->fetch())
        		{
        		    $codsous1=$row1['cod_sous'];
        		    $age1=$row1['age'];
        		}
       			// SELECT * FROM `souscripteurw` WHERE `cod_par`='8250' LIMIT 1,1
        		$reqt2=$bdd->prepare("select *from souscripteurw where cod_par='$cod_sous' LIMIT 1,1");
        		$reqt2->execute();
        		while ($row2=$reqt2->fetch())
        		{
        		    $codsous2=$row2['cod_sous'];
        		    $age2=$row2['age'];

        		}

    		}
			$reqtar1=$bdd->prepare("SELECT a.* FROM `tarif` as a , pays as b WHERE cod_prod='1' and cod_formul='1' and cod_opt='$opt'  and agemin <= '$age1' and agemax >='$age1' and cod_per='$cod_per' and b.cod_pays='$cod_pays'  and b.cod_zone=a.cod_zone and cod_cpl='$cpl'");
			$reqtar1->execute();
			$trouve1=0;$trouve2=0;
			while($row_a1=$reqtar1->fetch())
			{
				$pa1 = $row_a1['pa'] + (($row_a1['maj_pa'] * $row_a1['pa']) / 100) - (($row_a1['rab_pa'] * $row_a1['pa']) / 100);
				$pg1 = $row_a1['pe']+ (($row_a1['maj_pe'] * $row_a1['pe']) / 100) - (($row_a1['rab_pe'] * $row_a1['pe']) / 100);
				$cod_zone=$row_a1['cod_zone'];
				$trouve1=1;
			}

			$reqtar2=$bdd->prepare("SELECT a.* FROM `tarif` as a , pays as b WHERE cod_prod='1' and cod_formul='1' and cod_opt='$opt'  and agemin <= '$age2' and agemax >='$age2' and cod_per='$cod_per' and b.cod_pays='$cod_pays'  and b.cod_zone=a.cod_zone and cod_cpl='$cpl'");

			$reqtar2->execute();
			while($row_a2=$reqtar2->fetch())
			{
				$pa2 =$row_a2['pa'] + (($row_a2['maj_pa']*$row_a2['pa'])/100)- (($row_a2['rab_pa']*$row_a2['pa'])/100);
				$pg2= $row_a2['pe'] + (($row_a2['maj_pe']*$row_a2['pe'])/100)- (($row_a2['rab_pe']*$row_a2['pe'])/100);
				$trouve2=1;
			}

			$pnga_global=($pa1+$pa2)*0.75;
			$pngb_global=$pg1+$pg2;

			$prime_nette = $pnga_global + $pngb_global;
		}
		if($formul==4)//formule famille
		{
			$nb_assur;
			if ($nb_assur == 3) {
				$rabf = (3 - 2) / 3;
			}
			if ($nb_assur == 4) {
				$rabf = (4 - 2.5) / 4;
			}
			if ($nb_assur == 5) {
				$rabf = (5 - 3) / 5;
			}
			if ($nb_assur == 6) {
				$rabf = (6 - 3.5) / 6;
			}
			if ($nb_assur >= 7) {
				$rabf = ($nb_assur - 4) / $nb_assur;
			}

    		$pnga_global = 0;
    		$pngb_global = 0;
    		$prime_nette = 0;
    		$prime_total = 0;
    		$primei = 0;
    		$age = 0;
    		$cod_tar = 0;
    		$cpt = 0;
    		$rqtaffich = $bdd->prepare("select * from souscripteurw where cod_par = '$cod_sous' ");
    		$rqtaffich->execute();
		
			while ($row = $rqtaffich->fetch()) 
			{
				$age = $row['age'];
				$primei = 0;
			
				$rqtselect = $bdd->prepare("SELECT a.* FROM `tarif` as a , pays as b WHERE a.cod_prod='1' and a.cod_formul='1' and a.cod_opt= '$opt'
				and a.agemin <= '$age' and a.agemax >= '$age' and a.cod_per= '$cod_per'  and b.cod_pays= '$cod_pays'
				and b.cod_zone = a.cod_zone and a.cod_cpl ='2' ");
				$rqtselect->execute();
			
				while ($row1 = $rqtselect->fetch()) {
					$cod_tar = $row1['cod_tar'];
					$pngb_global = $pngb_global + ($row1['pe'] + (($row1['maj_pe'] * $row1['pe']) / 100) - (($row1['rab_pe'] * $row1['pe']) / 100));
					$pt1 = $row1['pa'] + (($row1['maj_pa'] * $row1['pa']) / 100) - (($row1['rab_pa'] * $row1['pa']) / 100);
					$pnga_global = $pnga_global + $pt1;
					$cpt++;
				}
			}
			if ($nb_assur == $cpt) 
			{  
				// $pnga_global = ($pnga_global/$nb_assur) *2.5; MAPFE
				 $pnga_global = $pnga_global*(1-$rabf) ;//IPA
				 $prime_nette = $pnga_global + $pngb_global;
				 $prime_total = $prime_nette + 290;
				
				
				 //$rqtinsert = $bdd->prepare("INSERT INTO `devisw`(`cod_dev`, `dat_dev`, `cod_tar`, `cod_prod`, `cod_per`, `cod_opt`, `cod_zone`, `cod_pays`,
				 //`cod_formul`, `cod_dt`,`cod_cpl`, `dat_eff`, `dat_ech`, `cap1`, `cap2`, `cap3`, `p1`, `p2`, `p3`, `pn`, `pt`, `bool`, `etat`, `cod_sous`, `taux_int`, `diff_pay`,`com`) VALUES ('','$datesys','$cod_tar','1','$cod_per','$opt ','$code_zone','$pays','4',
				 //'2','2','$dateffet','$datech','0','0','0','$pnga_global','$pngb_global','0','$prime_nette','$prime_total','0','0','$cod_sous','','','$com')");
				 //$rqtinsert->execute();
				 //echo "1";
		 	}
    		}	
			if($formul==5)//formule groupe
			{
			//echo "opt =>   $opt";
			$rabg = 1;
			$rqtaffich = $bdd->prepare("select count(*) as nbassurer from souscripteurw where cod_par = '$cod_sous' ");
    		$rqtaffich->execute();
			
			while ($row = $rqtaffich->fetch()) {
				$nb_assur = $row['nbassurer'];
			}
			if($opt<30){
				if($nb_assur >= 10 && $nb_assur <= 20){ $rabg=0.95;}
				if($nb_assur >= 21 && $nb_assur <= 50){ $rabg=0.90;}
				if($nb_assur >= 51 && $nb_assur <= 100){ $rabg=0.85;}
				if($nb_assur >= 101){ $rabg=0.75;}
			}else{
				$rabg=1;
			}
			//echo "rabg ==> $rabg";
				$rqt = $bdd->prepare("select * from pays where cod_pays = '$pays' ");
				$rqt-> execute();
				while($row = $rqt->fetch())
				{
					$code_zone = $row['cod_zone'];
				}
			if ($opt=='30')//
			{
				$cod_per='18';
			}
			elseif ($opt=='31')
			{
				$cod_per='15';
			}else {
			$reqper = $bdd->prepare("SELECT * FROM `periode` WHERE `max_jour`>= '$new_duree'AND `trt_per`>=1 LIMIT 1 ");
			$reqper->execute();
			$cod_per = 0;
			while ($rowper = $reqper->fetch()) 
			{
				$cod_per = $rowper['cod_per'];
			}
		}
		$pnga_global=0;
		$pngb_global=0;
		$prime_nette=0;
		$prime_total=0;
		$primei=0;
		$age=0;
		$cod_tar=0;
		$rqtaffich = $bdd->prepare("select * from souscripteurw where cod_par = '$cod_sous' ");
		$rqtaffich-> execute();
		$cpt=0;
		while($row = $rqtaffich->fetch()) 
		{
			$age = $row['age'];
			$primei = 0;

			// pour chaque assur� on calcule la prime qui lui conevient
			$rqtselect = $bdd->prepare("SELECT a.* FROM `tarif` as a , pays as b WHERE a.cod_prod='1' and a.cod_formul='1' and a.cod_opt= '$opt'
		   and a.agemin <= '$age' and a.agemax >= '$age' and a.cod_per= '$cod_per'  and b.cod_pays= '$cod_pays'
		   and b.cod_zone = a.cod_zone and a.cod_cpl ='2' ");
			$rqtselect->execute();
			while ($row1 = $rqtselect->fetch()) 
			{
				$cod_tar = $row1['cod_tar'];
				$pngb_global = $pngb_global + ( $row1['pe'] + (($row1['maj_pe'] * $row1['pe']) / 100) - (($row1['rab_pe'] * $row1['pe']) / 100));
				$pt1 = $row1['pa'] + (($row1['maj_pa'] * $row1['pa']) / 100) - (($row1['rab_pa'] * $row1['pa']) / 100);
				$pnga_global = $pnga_global + $pt1;
				$cpt++;
			}
		}
		   
			$pnga_global=$pnga_global*$rabg;
			$prime_nette=$pnga_global+$pngb_global;
			$prime_total=$prime_nette+40+250;
    }	
		// recuperer la prime nette des avenant de la pilce $code
		$reqt=$bdd->prepare("select sum(pn) as pnav,sum(p1) as p1av,sum(p2) as p2av from avenantw where cod_pol='$code'");
		$reqt->execute();
		$pnav=0;$p1av=0;$p1av=0;
		while ($row1=$reqt->fetch()) 
		{
			$pnav = $row1['pnav'];
			$p1av = $row1['p1av'];
			$p2av = $row1['p2av'];
		}
		$p1n=$pnga_global-($p1+$p1av);
		$p2n=$pngb_global-($p2+$p2av);
		$diffprime=$prime_nette-($pn+$pnav);
		$primtotal=$diffprime+140;
		$pnn=$diffprime;
		$ptn=$primtotal;
		//fin corps
		$rqtiav=$bdd->prepare("INSERT INTO `avenantw`(`cod_av`, `dat_val`, `cod_tar`, `cod_prod`, `cod_per`, `cod_opt`, `cod_zone`, `cod_pays`, `cod_formul`, `cod_dt`, `cod_cpl`, `dat_eff`, `dat_ech`, `cap1`, `cap2`, `cap3`, `p1`, `p2`, `p3`, `pn`, `pt`, `ndat_eff`, `ndat_ech`, `mode`, `dat_op`, `lib_mpay`, `sequence`, `etat`, `cod_pol`, `cod_mot`,`com`)  VALUES ('', '$datesys', '$tar', '$prod', '$per', '$opt', '$zone','$cod_pays', '$formul', '2', '6', '$date1', '$date2','$cap1', '$cap2', '$cap3', '$p1n', '$p2n', '0', '$pnn', '$ptn', '$date1','$date2', '$modeav','$datopav','$av','$seq', '0', '$code','','$com')");
		$rqtiav->execute();
		$rqt=$bdd->prepare("update policew set ndat_eff='$date1',ndat_ech='$date2' where cod_pol='$code'");
		$rqt->execute(); 
		$rqtc = $bdd->prepare("UPDATE `produit` SET `sequence2`= '$seq' WHERE `cod_prod`='$prod'");
		$rqtc->execute();
	}
}

}
?>