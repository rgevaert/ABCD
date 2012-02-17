<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      devolver.php
 * @desc:      Ask for the item to be returned
 * @author:    Guilda Ascencio
 * @since:     20091203
 * @version:   1.0
 *
 * == BEGIN LICENSE ==
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Lesser General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * == END LICENSE ==
*/
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
$lang=$_SESSION["lang"];

include("../lang/prestamo.php");
include("../common/get_post.php");
$arrHttp["base"]="users";
//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";
include("../common/header.php");

function ImprimirRecibo($Recibo){
?>
<script>
	msgwin=window.open("","recibo","width=400, height=300, scrollbars, resizable")
	msgwin.document.write("<?php echo $Recibo?>")
	msgwin.focus()
	msgwin.print()
	msgwin.close()
</script>
<?php
}
?>
<script src=../dataentry/js/lr_trim.js></script>
<script>
document.onkeypress =
  function (evt) {
    var c = document.layers ? evt.which
            : document.all ? event.keyCode
            : evt.keyCode;
	if (c==13)
		EnviarForma()

    return true;
  };

function EnviarForma(){	if (Trim(document.inventorysearch.searchExpr.value)==""){		alert("<?php echo $msgstr["missinventory"]?>")
		return	}
	document.inventorysearch.submit()
}

function AbrirIndiceAlfabetico(){	db="trans"
	cipar="trans.par"
	postings=1
	tag="10"
	Prefijo="TR_P_"
	Ctrl_activo=document.inventorysearch.searchExpr
	lang="<?php echo $_SESSION["lang"]?>"
	Separa=""
	Repetible=""
	Formato="v10"
	Prefijo=Separa+"&tagfst=&prefijo="+Prefijo
	ancho=200
	url_indice="capturaclaves.php?opcion=autoridades&base="+db+"&cipar="+cipar+"&Tag="+tag+Prefijo+"&postings="+postings+"&lang="+lang+"&repetible="+Repetible+"&Formato="+Formato
	msgwin=window.open(url_indice,"Indice","width=480, height=425,scrollbars")
	msgwin.focus()
}


</script>
<?php
echo "<body onload='javascript:document.inventorysearch.searchExpr.focus()'>\n";
include("../common/institutional_info.php");
?>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["return"]?>
	</div>
	<div class="actions">
		<?php include("submenu_prestamo.php");?>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/devolver.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php echo "
<a href=../documentacion/ayuda.php?help=". $_SESSION["lang"]."/circulation/loan.html target=_blank>". $msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/circulation/loan.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: devolver.php </font>
	</div>";
// prestar, reservar o renovar
?>
<div class="middle list">

	<div class="searchBox">
	<form name=inventorysearch action=devolver_ex.php method=post onsubmit="javascript:return false">
	<table width=100%>
		<td width=100>
		<label for="searchExpr">
			<strong><?php echo $msgstr["inventory"]?></strong>
		</label>
		</td><td>
		<input type="text" name="searchExpr" id="searchExpr" value="" class="textEntry" onfocus="this.className = 'textEntry';"  onblur="this.className = 'textEntry';" />
        <input type=hidden name=base value=trans>
		<input type="button" name="list" value="<?php echo $msgstr["list"]?>" class="submit" onclick="javascript:AbrirIndiceAlfabetico();return false"/>
		<input type="submit" name="reservar" value="<?php echo $msgstr["return"]?>" xclass="submitAdvanced" onclick="javascript:EnviarForma()"/>
		</td></table>
		<?php echo $msgstr["clic_en"]." <i>[".$msgstr["return"]."]</i> ".$msgstr["para_c"]?>
	</form>
	</div>
<?php
if (isset($arrHttp["usuario"])){    include("ec_include.php");
    echo "<div class=formContent>$ec_output</div>";
}
?>

</div>
<?php include("../common/footer.php");
echo "</body></html>" ;
if (isset($arrHttp["recibo"])) {
	ImprimirRecibo($arrHttp["recibo"]);
}
if (isset($arrHttp["error"])){	echo "<script>
			alert('".$arrHttp["error"]."')
			</script>
	";}

?>