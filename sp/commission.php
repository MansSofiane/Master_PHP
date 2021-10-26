<?php session_start();
require_once("../../../data/conn5.php");

if ($_SESSION['loginAGA']){
}
else {
    header("Location:index.html");
}

$id_user = $_SESSION['id_userAGA'];
$rqt = $bdd->prepare("SELECT distinct agence FROM `utilisateurs`   ORDER BY `agence`");
$rqt->execute();


?>

<div id="content-header">
    <div id="breadcrumb"> <a class="tip-bottom"><i class="icon-signal"></i> Commission</a><a class="current">Facture-Commission</a></div>
</div>



<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">

            <div class="widget-content nopadding">
                <form class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label">Agence *:</label>
                        <div class="controls">
                            <select id="user">
                                <option value="0">Tout</option>
                                <?php while ($row_res=$rqt->fetch()){  ?>
                                    <option value="<?php  echo $row_res['agence']; ?>"><?php  echo $row_res['agence']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <div data-date-format="dd/mm/yyyy">
                                <input type="text" class="date-pick dp-applied"  id="date1" placeholder="Du 01/01/2000 (*)"/>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="text" class="date-pick dp-applied"  id="date2" placeholder="Au 01/01/2000 (*)"/>
                            </div>
                        </div>

                </form>
            </div>
        </div>
    </div>

    <div class="widget-box">

        <ul class="quick-actions">
            <li class="bg_lh"> <a onClick="prod('1')"> <i class="icon-bar-chart"></i>Facture de commission</a> </li>
        </ul>
    </div>
    <script language="JavaScript">initdate();</script>
    <script language="JavaScript">

        function verifdatestat(dd)
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

        function dfrtoenstat(date1)
        {
            var split_date=date1.split('/');
            var new_d=new Date(split_date[2], split_date[1]*1 - 1, split_date[0]*1);
            var new_day = new_d.getDate();
            new_day = ((new_day < 10) ? '0' : '') + new_day; // ajoute un zéro devant pour la forme
            var new_month = new_d.getMonth() + 1;
            new_month = ((new_month < 10) ? '0' : '') + new_month; // ajoute un zéro devant pour la forme
            var new_year = new_d.getYear();
            new_year = ((new_year < 200) ? 1900 : 0) + new_year; // necessaire car IE et FF retourne pas la meme chose
            var new_date_text = new_year + '-' + new_month + '-' + new_day;
            return new_date_text;
        }
        function prod(code)
        {


            var date1 = document.getElementById("date1");
            var date2 = document.getElementById("date2");
            var agence= document.getElementById("user").value;
            if(agence=="0")
            {
                alert("Veuillez selectionner une agence!");
            }
            else {
//RewriteRule ^facture/([-a-z0-9A-Z]+)/([-a-z0-9A-Z]+)/([-a-z0-9A-Z]+)$ pcommission.php?d1=$1&d2=$2&ag=$3
                if (verifdatestat(date1) && verifdatestat(date2)) {
                    var d1 = dfrtoenstat(date1.value);
                    var d2 = dfrtoenstat(date2.value);

                    window.open('sortie/facture/' + d1 + '/' + d2 + '/' + agence, 'commission', 'height=600, width=800, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no');

                }
            }
        }

    </script>