<?php session_start();
require_once("../../../../../data/conn5.php");
if ($_SESSION['loginAGA']){
$id_user=$_SESSION['id_userAGA'];
}
else {
header("Location:login.php");
}
$tokiacc3 = generer_token('devind3');
if ( isset($_REQUEST['tok']) ) {
    $token = $_REQUEST['tok'];
}
if ( isset($_REQUEST['sous']) &&  isset($_REQUEST['classe'])  &&  isset($_REQUEST['periode']) && isset($_REQUEST['cap1']) && isset($_REQUEST['cap2']) &&  isset($_REQUEST['bool'])){
    $codsous= $_REQUEST['sous'];
    $classe= $_REQUEST['classe'];
	$periode= $_REQUEST['periode'];
	$cap1= $_REQUEST['cap1'];
	$cap2= $_REQUEST['cap2'];
	$bool= $_REQUEST['bool'];

$cpt=0;$rp=0;$codsous1=0;$prime=0;$codtar=0;$per=0;$libper="";$jour=0;$tar=0;
//on recupere la periode
$rqtper=$bdd->prepare("SELECT * FROM `periode`  WHERE cod_per='$periode'");
$rqtper->execute();
while ($rowper=$rqtper->fetch()){
$per=$rowper['cod_per'];
$libper=$rowper['lib_per'];
$jour=$rowper['max_jour'];
}
//on verifie l'assure
$rqtv=$bdd->prepare("SELECT `rp_sous` FROM `souscripteurw`  WHERE `cod_sous`='$codsous'");
$rqtv->execute();

while ($row=$rqtv->fetch()){
$rp=$row['rp_sous'];
}
$dt=40;
$cp=250;
  
//Requete pour le tarif sur souscripteur qui est l'assure
if($rp==1){	
//d?but du tarif le souscripteur est l'assure ******************************************
// Tarif de la Garantie DC	
$rqt1=$bdd->prepare("SELECT t.`cod_tar`,t.`pe`,t.`pa`,d.`mtt_dt`,c.`mtt_cpl` FROM `tarif` as t, dtimbre as d, cpolice as c, `souscripteurw` as s WHERE t.`cod_prod`='2' AND t.`cod_seg`='1' AND t.`cod_cls`='$classe' AND t.`cod_zone`='1' AND t.`cod_formul`='1'  AND t.`cod_per`='$periode'  AND t.`cod_dt`=d.`cod_dt` AND t.`cod_cpl`=c.`cod_cpl` AND t.`cod_cpl`='2' AND t.`agemin`<=s.`age` AND t.`agemax`>=s.`age` AND s.`cod_sous`='$codsous'");
$rqt1->execute();
$pe1=0;$pa1=0;$dt=0;$cp=0;$pt1=0;
while ($row_res=$rqt1->fetch()){
$cpt++;
$pe1=$row_res['pe'];$pa1=$row_res['pa'];
$dt=$row_res['mtt_dt'];$cp=$row_res['mtt_cpl'];
$tar=$row_res['cod_tar'];
}
$pt1=($cap1*$pe1*$pa1)/100;
// Tarif de la Garantie IPP	
$rqt2=$bdd->prepare("SELECT t.`pe`,t.`pa`,d.`mtt_dt`,c.`mtt_cpl` FROM `tarif` as t, dtimbre as d, cpolice as c, `souscripteurw` as s WHERE t.`cod_prod`='2' AND t.`cod_seg`='1' AND t.`cod_cls`='$classe' AND t.`cod_zone`='1' AND t.`cod_formul`='2'  AND t.`cod_per`='$periode'  AND t.`cod_dt`=d.`cod_dt` AND t.`cod_cpl`=c.`cod_cpl` AND t.`cod_cpl`='2' AND t.`agemin`<=s.`age` AND t.`agemax`>=s.`age` AND s.`cod_sous`='$codsous'");
$rqt2->execute();
$pe2=0;$pa2=0;$pt2=0;
while ($row_res2=$rqt2->fetch()){
$cpt++;
$pe2=$row_res2['pe'];$pa2=$row_res2['pa'];
}
$pt2=($cap1*$pe2*$pa2)/100;
// Tarif de la Garantie FMP
$rqt3=$bdd->prepare("SELECT t.`pe`,t.`pa`,d.`mtt_dt`,c.`mtt_cpl` FROM `tarif` as t, dtimbre as d, cpolice as c, `souscripteurw` as s WHERE t.`cod_prod`='2' AND t.`cod_seg`='1' AND t.`cod_cls`='$classe' AND t.`cod_zone`='1' AND t.`cod_formul`='3'  AND t.`cod_per`='$periode'  AND t.`cod_dt`=d.`cod_dt` AND t.`cod_cpl`=c.`cod_cpl` AND t.`cod_cpl`='2' AND t.`agemin`<=s.`age` AND t.`agemax`>=s.`age` AND s.`cod_sous`='$codsous'");
$rqt3->execute();
$pe3=0;$pa3=0;$pt3=0;
while ($row_res3=$rqt3->fetch()){
$cpt++;
$pe3=$row_res3['pe'];$pa3=$row_res3['pa'];
}
$pt3=($cap2*$pe3*$pa3);
$prime=$pt1+$pt2+$pt3+$dt+$cp;


// fin du tarif souscripteur est l'assure************************************************	
}





//Requete pour le tarif sur souscripteur qui n'est pas l'assure
if($rp==2){	
// on recupere le code de l'assure
$rqtv1=$bdd->prepare("SELECT `cod_sous` FROM `souscripteurw`  WHERE `cod_par`='$codsous'");
$rqtv1->execute();
$rp=0;
while ($row=$rqtv1->fetch()){
$codsous1=$row['cod_sous'];
}
//**************
// Tarif de la Garantie DC	
$rqt1=$bdd->prepare("SELECT t.`cod_tar`,t.`pe`,t.`pa`,d.`mtt_dt`,c.`mtt_cpl` FROM `tarif` as t, dtimbre as d, cpolice as c, `souscripteurw` as s WHERE t.`cod_prod`='2' AND t.`cod_seg`='1' AND t.`cod_cls`='$classe' AND t.`cod_zone`='1' AND t.`cod_formul`='1'  AND t.`cod_per`='$periode'  AND t.`cod_dt`=d.`cod_dt` AND t.`cod_cpl`=c.`cod_cpl` AND t.`cod_cpl`='2' AND t.`agemin`<=s.`age` AND t.`agemax`>=s.`age` AND s.`cod_sous`='$codsous1'");
$rqt1->execute();
$pe1=0;$pa1=0;$dt=0;$cp=0;$pt1=0;
while ($row_res=$rqt1->fetch()){
$cpt++;
$pe1=$row_res['pe'];$pa1=$row_res['pa'];
$dt=$row_res['mtt_dt'];$cp=$row_res['mtt_cpl'];
$tar=$row_res['cod_tar'];
}
$pt1=($cap1*$pe1*$pa1)/100;
// Tarif de la Garantie IPP	
$rqt2=$bdd->prepare("SELECT t.`pe`,t.`pa`,d.`mtt_dt`,c.`mtt_cpl` FROM `tarif` as t, dtimbre as d, cpolice as c, `souscripteurw` as s WHERE t.`cod_prod`='2' AND t.`cod_seg`='1' AND t.`cod_cls`='$classe' AND t.`cod_zone`='1' AND t.`cod_formul`='2'  AND t.`cod_per`='$periode'  AND t.`cod_dt`=d.`cod_dt` AND t.`cod_cpl`=c.`cod_cpl` AND t.`cod_cpl`='2' AND t.`agemin`<=s.`age` AND t.`agemax`>=s.`age` AND s.`cod_sous`='$codsous1'");
$rqt2->execute();
$pe2=0;$pa2=0;$pt2=0;
while ($row_res2=$rqt2->fetch()){
$cpt++;
$pe2=$row_res2['pe'];$pa2=$row_res2['pa'];
}
$pt2=($cap1*$pe2*$pa2)/100;
// Tarif de la Garantie FMP
$rqt3=$bdd->prepare("SELECT t.`pe`,t.`pa`,d.`mtt_dt`,c.`mtt_cpl` FROM `tarif` as t, dtimbre as d, cpolice as c, `souscripteurw` as s WHERE t.`cod_prod`='2' AND t.`cod_seg`='1' AND t.`cod_cls`='$classe' AND t.`cod_zone`='1' AND t.`cod_formul`='3'  AND t.`cod_per`='$periode'  AND t.`cod_dt`=d.`cod_dt` AND t.`cod_cpl`=c.`cod_cpl` AND t.`cod_cpl`='2' AND t.`agemin`<=s.`age` AND t.`agemax`>=s.`age` AND s.`cod_sous`='$codsous1'");
$rqt3->execute();
$pe3=0;$pa3=0;$pt3=0;
while ($row_res3=$rqt3->fetch()){
$cpt++;
$pe3=$row_res3['pe'];$pa3=$row_res3['pa'];
}
$pt3=($cap2*$pe3*$pa3);
$prime=$pt1+$pt2+$pt3+$dt+$cp;	
}	
	
}
if($cpt>0){
?>
<div id="content-header">
      <div id="breadcrumb"> <a class="tip-bottom"><i class="icon-home"></i> Produit</a><a>Individuelle-Accident</a><a>Formule-Individuelle</a><a class="current">Nouveau-Devis</a> </div>
  </div>
  <div class="row-fluid">  
    <div class="span12">
      <div class="widget-box">
      <div id="breadcrumb"> <a><i></i>Souscripteur</a><a>Assure</a><a>Capital</a><a class="current">Validation</a></div>
        <div class="widget-content nopadding">
          <form class="form-horizontal">
		      
			 <div class="control-group">
			 <label class="control-label">Prime DC: </label>
              <div class="controls">
                <input type="text" id="p1" class="span5" value="<?php echo number_format($pt1, 2, ',', ' '); ?> DZD" disabled="disabled"/>
              </div>
            </div>
			 <div class="control-group">
			 <label class="control-label">Prime IPP: </label>
              <div class="controls">
                <input type="text" id="p2" class="span5" value="<?php echo number_format($pt2, 2, ',', ' '); ?> DZD" disabled="disabled"/>
              </div>
            </div> 
			<div class="control-group">
			 <label class="control-label">Prime FMP: </label>
              <div class="controls">
                <input type="text" id="p3" class="span5" value="<?php echo number_format($pt3, 2, ',', ' '); ?> DZD" disabled="disabled"/>
              </div>
            </div>
			<div class="control-group">
			 <label class="control-label">Prime a payer: </label>
              <div class="controls">
                <input type="text" id="pt" class="span5" value="<?php echo number_format($prime, 2, ',', ' '); ?> DZD" disabled="disabled"/>
              </div>
            </div>
			<div class="control-group">
			 <label class="control-label">Duree: </label>
              <div class="controls">
                <input type="text" id="per" class="span5" value="<?php echo $libper; ?>" disabled="disabled" />
              </div>
            </div>
		  
			  <div class="control-group"> 
              <label class="control-label">Date-Effet:</label>
				 <div class="controls" data-date-format="dd/mm/yyyy">
				<input type="text" class="date-pick dp-applied"  id="deffiacc" placeholder="Format date JJ/MM/AAAA"/> 
              </div>
			  </div>	
            <div class="form-actions" align="right">
			  <input  type="button" class="btn btn-success" onClick="instdeviacc('<?php echo $codsous; ?>','<?php echo $pt1; ?>','<?php echo $pt2; ?>','<?php echo $pt3; ?>','<?php echo $prime; ?>','<?php echo $periode; ?>','<?php echo $cpt; ?>','<?php echo $jour; ?>','<?php echo $tar; ?>','<?php echo $cap1; ?>','<?php echo $cap2; ?>','<?php echo $bool; ?>','<?php echo $tokiacc3; ?>')" value="Enregistrer" />
			  <input  type="button" class="btn btn-danger"  onClick="Menu1('prod','assiaccind.php')" value="Annuler" />
            </div>
          </form>
        </div>
      </div>
	 </div>
</div>
<?php }else{ ?>
<div id="content-header">
<div id="breadcrumb"> <a class="tip-bottom"><i class="icon-home"></i> Produit</a> <a>Individuelle-Accident</a> <a class="current">Nouveau-Devis</a> </div>
</div>
<div id="breadcrumb"> <a><i></i><a class="current">Cas Non Assure !!</a></div>
<div class="form-actions" align="right">
<input  type="button" class="btn btn-danger"  onClick="Menu1('prod','assiaccind.php')" value="Retour" />
</div>
<?php } ?>
<script language="JavaScript">initdate();</script>
<script language="JavaScript">
    function verifdate_ind5(dd)
    {
        v1=true;
        var regex = /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/;
        var test = regex.test(dd.value);
        if(!test){
            v1=false;
            alert("Format date incorrect! jj/mm/aaaa");dd.value="";

        }
        return v1;
    }

    function dfrtoen_ind5(date1)
    {
        var split_date=date1.split('/');
        var new_d=new Date(split_date[2], split_date[1]*1 - 1, split_date[0]*1);
        var new_day = new_d.getDate();
        new_day = ((new_day < 10) ? '0' : '') + new_day; // ajoute un z?ro devant pour la forme
        var new_month = new_d.getMonth() + 1;
        new_month = ((new_month < 10) ? '0' : '') + new_month; // ajoute un z?ro devant pour la forme
        var new_year = new_d.getYear();
        new_year = ((new_year < 200) ? 1900 : 0) + new_year; // necessaire car IE et FF retourne pas la meme chose
        var new_date_text = new_year + '-' + new_month + '-' + new_day;
        return new_date_text;
    }
    function calage_ind5(dd)
    {
        var bb1=document.getElementById("datsys");
        var aa=new Date(dfrtoen_ind5(dd.value));
        var bb=new Date(bb1.value);
        var sec1=bb.getTime();
        var sec2=aa.getTime();
        var sec=(sec1-sec2)/(365.24*24*3600*1000);
        age=Math.floor(sec);
        return age;

    }

    function compdat_ind5(dd)
    {
        var rcomp=false;
        var bb1=document.getElementById("datsys");
        var aa=new Date(dfrtoen_ind5(dd.value));
        var bb=new Date(bb1.value);
        var sec1=bb.getTime();
        var sec2=aa.getTime();
        if(sec2>=sec1){rcomp=true;}
        return rcomp;

    }

    function addDays_ind5(dd,xx) {
        // Date plus plus quelques jours
        var split_date = dd.split('/');
        var new_date = new Date(split_date[2], split_date[1]*1 - 1, split_date[0]*1 + parseInt(xx)-1);
        var dd= new Date(split_date[2], split_date[1]*1 - 1, split_date[0]*1);
        var new_day = new_date.getDate();
        new_day = ((new_day < 10) ? '0' : '') + new_day; // ajoute un z?ro devant pour la forme
        var new_month = new_date.getMonth() + 1;
        new_month = ((new_month < 10) ? '0' : '') + new_month; // ajoute un z?ro devant pour la forme
        var new_year = new_date.getYear();
        new_year = ((new_year < 200) ? 1900 : 0) + new_year; // necessaire car IE et FF retourne pas la meme chose
        var new_date_text = new_day + '/' + new_month + '/' + new_year;
        return new_date_text;
    }
function instdeviacc(codsous,p1,p2,p3,pt,per,cpt,jour,tar,cap1,cap2,bool,tok){
var dateff=document.getElementById("deffiacc");
var datech=null;

	   if (window.XMLHttpRequest) { 
        xhr = new XMLHttpRequest();
     }
     else if (window.ActiveXObject) 
     {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
     }
	 
	if(cpt==0){alert("Cas Non-Assure !");}else{
	  if(verifdate_ind5(dateff)){
	 
          	if(compdat_ind5(dateff)){
			   datech=dfrtoen_ind5(addDays_ind5(dateff.value,jour));
			   dateff=dfrtoen_ind5(dateff.value)
			   xhr.open("GET", "produit/iaccid/phy/ndev.php?sous="+codsous+"&p1="+p1+"&p2="+p2+"&p3="+p3+"&pt="+pt+"&d1="+dateff+"&d2="+datech+"&per="+per+"&tar="+tar+"&cap1="+cap1+"&cap2="+cap2+"&bool="+bool+"&tok="+tok, false);
                xhr.send(null);
				alert("Devis Enregistre !");
				Menu1('prod','assiaccind.php');
				
            }else{
	         alert("la date d'effet doit pas etre anterieure !");
	             }
				 }else{alert("Format Date incorrect JJ/MM/AAAA !");}
				 
	
	}
	
	}	
			
</script>	