<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      copies_edit_read.php
 * @desc:      
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
include("../config.php");
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
$lang=$_SESSION["lang"];


include("../lang/admin.php");
include("../lang/acquisitions.php");
include("../dataentry/leerregistroisis.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";

include("../common/header.php");
include("../acquisitions/javascript.php");

//Se lee el registro que va a ser modificado
$arrHttp["lock"] ="S";
$arrHttp["opcion"]="leer";
$maxmfn=0;

$res=LeerRegistro($arrHttp["base"],$arrHttp["base"].".par",$arrHttp["Mfn"],$maxmfn,"editar",$_SESSION["login"],"","");
if ($res=="LOCKREJECTED") {
	echo "<script>
	alert('".$arrHttp["Mfn"].": ".$msgstr["reclocked"]."')
	</script>";
	die;
	break;
}
?>
<script language=javascript>
top.toolbarEnabled="N"
function Validar(){ 
}
function EnviarForma(){
	ret=Validar()
	if (ret=="N") return
	document.forma1.submit()
}
</script>
<body>
<?php
if (isset($arrHttp["encabezado"]) and $arrHttp["encabezado"]=="s"){
	include("../common/institutional_info.php");
}
echo "
	<div class=\"sectionInfo\">
		<div class=\"breadcrumb\">".
			 $msgstr["m_editcopy"]."
		</div>
		<div class=\"actions\">\n";
?>
			<a href=<?php echo $arrHttp["retorno"]?> class="defaultButton cancelButton">
				<img src=../images/defaultButton_iconBorder.gif alt="" title="" />
				<span><strong><?php echo $msgstr["cancelar"]?></strong></span>
			</a>
			<a href=javascript:EnviarForma() class="defaultButton saveButton">
				<img src=../images/defaultButton_iconBorder.gif alt="" title="" />
				<span><strong><?php echo $msgstr["actualizar"]?></strong></span>
			</a>
<?php
echo "	</div>
		<div class=\"spacer\">&#160;</div>
	</div>";
?>
<div class="helper">
	<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/acquisitions/copies_add.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
 	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/acquisitions/copies_add.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: copies_edit_read.php";
?>
</font>
	</div>

<form method=post name=forma1 action=copies_update.php onSubmit="javascript:return false">
<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
<input type=hidden name=cipar value=<?php echo $arrHttp["base"].".par"?>>
<input type=hidden name=ValorCapturado value="">
<input type=hidden name=check_select value="">
<input type=hidden name=Indice value="">
<input type=hidden name=Mfn value="<?php echo $arrHttp["Mfn"]?>">
<input type=hidden name=valor value="">
<input type=hidden name=retorno value=<?php echo $arrHttp["retorno"]?>>

<div class="middle form">
<?php
$fmt_test="S";
$arrHttp["wks"]="new.fmt";
$wks_avail["new"]=1;
include("../dataentry/plantilladeingreso.php");
ConstruyeWorksheetFmt();
include("../dataentry/dibujarhojaentrada.php");
PrepararFormato();

?>
</form>
	</div>
</div>
<?php include("../common/footer.php"); ?>
</body>
</html>

<?php
// ==================================================================================

function LeerFdt($base){
global $tag_ctl,$pref_ctl,$arrHttp,$db_path,$msgstr;
// se lee la FDT para conseguir la etiqueta del campo donde se coloca la numeración automática y el prefijo con el cual se indiza el número de control

	$archivo=$db_path.$base."/def/".$_SESSION["lang"]."/".$base.".fdt";
	if (file_exists($archivo)){
		$fp=file($archivo);
	}else{
		$archivo=$db_path.$base."/def/".$lang_db."/".$base.".fdt";
		if (file_exists($archivo)){
			$fp=file($archivo);
		}else{
			echo $msgstr["notfound"].": ".$archivo;
		    die;
		 }
	}
	$tag_ctl="";
	$pref_ctl="";
	foreach ($fp as $linea){
		$l=explode('|',$linea);
		if ($l[0]=="AI"){
			$tag_ctl=$l[1];
			$pref_ctl=$l[12];
		}
	}
	// Si no se ha definido el tag para el número de control en la fdt, se produce un error
	if ($tag_ctl=="" or $pref_ctl==""){
		echo "<h2>".$msgstr["missingctl"]."</h2>";
		die;
	}
}

?>