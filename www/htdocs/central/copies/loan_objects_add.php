<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      loan_objects_add.php
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
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include ("../config.php");
include("../lang/acquisitions.php");

include("../lang/admin.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";

// GET THE CONTROL NUMBER FOR THE BIBLIOGRAPHIC RECORD
if (!isset($arrHttp["cn"])){
	LeerFst($arrHttp["base"]);
	if ($tag_ctl!=""){
		$Formato="v".$tag_ctl;

//Se lee el registro bibliográfico para capturar el número del objeto

		$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par"."&from=".$arrHttp["Mfn"]."&to=".$arrHttp["Mfn"]."&Formato=$Formato&Opcion=rango";
		$IsisScript=$xWxis."imprime.xis";
		include("../common/wxis_llamar.php");
	//	foreach ($contenido as $value)  echo "$value<br>";
		$valortag[1]=implode("",$contenido);
		if ($valortag[1]==""){     //CHECK IF THE RECORD HAS CONTROL NUMBER
			$err_copies="Y";
		}else{
			$err_copies="N";
			$arrHttp["cn"]="CN_".$arrHttp["base"]."_".$valortag[1];
		}
	}else{
		Print_page();
		die;
	}
}

//GET THE RECORD FROM LOANOBJECTS USING THE CONTROL NUMBER
$Formato=$db_path."loanobjects/pfts/".$_SESSION["lang"]."/loanobjects_add.pft" ;
if (!file_exists($Formato)) $Formato=$db_path."loanobjects/pfts/".$lang_db."/loanobjects_add.pft"  ;
$Expresion=$arrHttp["cn"];
$query = "&base=loanobjects&cipar=$db_path"."par/loanobjects.par"."&from=1&Formato=@$Formato&Opcion=buscar&Expresion=".urlencode($Expresion);
$IsisScript=$xWxis."imprime.xis";
include("../common/wxis_llamar.php");
$old_c=$contenido;
//CREATE LIST OF INVENTORY NUMBERS FOR CHECKING DUPLICATES IN THE RECORD
$ixc=0;
foreach ($contenido as $value){
    if ($ixc==0){
    	$ixc=1;
    	$v=explode('$$$',$value);
    	$value=$v[1];
    }
	$inv=explode('|',$value);
	$dup[$inv[2]]="Y";
}
//GET THE COPIES TO BE ADDED TO THE LOANOBJECTS DATASBASE
$Formato=$db_path."copies/pfts/".$_SESSION["lang"]."/copies_add.pft" ;
if (!file_exists($Formato)) $Formato=$db_path."copies/pfts/".$lang_db."/copies_add.pft"  ;
$Expresion=$arrHttp["cn"]."* STATUS_1";
$query = "&base=copies&cipar=$db_path"."par/copies.par"."&from=1&Formato=@$Formato&Opcion=buscar&Expresion=".urlencode($Expresion);
$IsisScript=$xWxis."imprime.xis";
include("../common/wxis_llamar.php");
$c=implode("",$contenido);
//foreach ($contenido as $value) echo "$value<br>";
//GET THE TYPE OF OBJECT IN RESPECT TO THE LOAN POLICY
$file=$db_path."circulation/def/".$_SESSION["lang"]."/items.tab";
if (!file_exists($file)) $file=$db_path."circulation/def/".$lang_db."/items.tab";
$fp_items=file($file);


Print_page();
if (empty($c)) {
	echo "<p><br><br><dd><strong>".$msgstr["nocopiestoadd"]."</strong></dd></div></div>";
    	include("../common/footer.php");
    	die;
}
//foreach ($contenido as $value) echo "$value<br>";
$ix=0;
$subc="ilbotv";
echo "<script>
		copies=new Array()
		";
$ix=-1;
$cn="";
$db="";
foreach ($contenido as $value){
	$t=explode('|',$value);
	$ix=$ix+1;
	if ($ix==0){
		$cn=$t[0];
		$db=$t[1];
	}
	$vc="^".$subc[0].$t[3];
	if (!empty($t[4])) $vc.="^".$subc[1].$t[4];
	if (!empty($t[5])) $vc.="^".$subc[2].$t[5];
	if (!empty($t[6])) $vc.="^".$subc[4].$t[6];
	if (!empty($t[7])) $vc.="^".$subc[5].$t[7];
	echo "copies[$ix]=\"".$vc."\"\n";
}
echo "</script>\n";

$archivo=$db_path."copies/pfts/".$_SESSION["lang"]."/copies_add.tit";
if (!file_exists($archivo))
	$archivo=$db_path."copies/pfts/".$lang_db."/copies_add.tit";
$fp=file($archivo);
$key=explode('_',$arrHttp["cn"]);
$key="CN_".$key[2];
echo "<strong><a href=javascript:Show('$key')>".$msgstr["cn"].": $cn ($db)</a></strong><br>";
echo $msgstr["loanobjadd"]."

	<table class=\"statTable\" cellspacing=5 cellpadding=5 width=100%>
		<tr>";
foreach ($fp as $linea) {
	$l=explode('|',$linea);
	foreach ($l as $lx)
		if (!empty($lx)) echo "<th>$lx</th>";
	break;
}
echo "<tr><td colspan=6 bgcolor=#eeeeee><strong>".$msgstr["newitems"]."</strong></td>
<tr>";
reset($contenido);
foreach ($contenido as $value){
	$t=explode("|",$value);
	$duplicate="";
	if (isset($dup[$t[3]])) {
		$duplicate="<strong><font color=red>".$msgstr["exitems"]."</font></strong>";
		echo "<input type=hidden name=duplicated value=Y>\n";
	}else{
		echo "<input type=hidden name=duplicated>\n";
	}

	echo "<tr><td align=center>".$t[3]." $duplicate</td><td>".$t[4]."</td><td>".$t[5]."</td><td align=center>".$t[6]."</td><td align=center>".$t[7]."</td>";
	echo "<td align=center><Select name=status>";

	$vc="";
	foreach ($fp_items as $value){
		$v=explode('|',$value);
		echo "<option value=".$v[0].">".$v[1]."</option>\n";
	}
	echo "</select></td>";
}
echo "</table>
<input type=hidden name=status>
<input type=hidden name=duplicated>
<input type=hidden name=ValorCapturado>
<input type=hidden name=db value=$db>
<input type=hidden name=cn value=$cn>
</div>
<div>
<div>
</form>";
include("../common/footer.php");
//=====================================

function Print_page(){
Global $arrHttp,$msgstr,$error;
	$encabezado="";
	include("../common/header.php");
?>
<script>
function Show(CN){
	msgwin=window.open("../dataentry/show.php?base=<?php echo $arrHttp["base"]?>&Expresion="+CN,"show","width=600, height=600, resizable, scrollbars")
	msgwin.focus()
}

function Send(){
	ValorCapturado=""
	for (i=0;i<document.forma1.status.length-1;i++){
		if (document.forma1.duplicated[i].value==""){
			ind=document.forma1.status[i].selectedIndex
			tobj=document.forma1.status[i].options[ind].value
			copies[i]+="^o"+tobj
			ValorCapturado+=copies[i]+"\n"
		}

	}
	if (ValorCapturado==""){
		alert("<?php echo $msgstr["exitems"]?>")
		return
	}
	document.forma1.ValorCapturado.value=ValorCapturado
	document.forma1.submit()
}
</script>
<?
	echo "<body>
	<form name=forma1 action=loan_objects_update.php onsubmit='return false'>\n";
	if (isset($arrHttp["encabezado"])){
		include("../common/institutional_info.php");
		$encabezado="&encabezado=s";
	}
?>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["loanobjects"]?>
	</div>
	<div class="actions">
		<?php
		if (!isset($arrHttp["return"])){
			$ret="../common/inicio.php?reinicio=s$encabezado";
		}else{
			$ret=str_replace("|","?",$arrHttp["return"])."&encabezado=".$arrHttp["encabezado"];
		}

		?>
		<a href='javascript:top.toolbarEnabled="";top.Menu("same")' class="defaultButton backButton">
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" /><?php echo $msgstr["back"]?></a>
		<?php if ($error==""){
		?>
		<a href=javascript:Send() class="defaultButton copiesdbaddButton">
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<?php echo $msgstr["update"]?>
		</a>
		<?php
		}
		?>

	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/copies/loan_objects.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/copies/loan_objects.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: loan_objects_add.php</font>";
echo "
</div>
<div class=\"middle form\">
		<div class=\"formContent\">

";
if ($error!=""){
	echo "<h4>".$msgstr[$error]."</h4>";
}
}

// ==================================================================================

function LeerFst($base){
global $tag_ctl,$pref_ctl,$arrHttp,$db_path,$AI,$lang_db,$msgstr,$error;
// GET THE FST TO FIND THE CONTROL NUMBER OF A BIBLIOGRAPHIC DATABASE
	$archivo=$db_path.$base."/data/".$base.".fst";
	if (!file_exists($archivo)){
		echo $msgstr["notfound"].": ".$base."/data/".$base.".fst";
		die;
	}
	$fp=file($archivo);
	$tag_ctl="";
	$pref_ctl="CN_";
	foreach ($fp as $linea){
		$linea=trim($linea);
		$ix=strpos($linea,"\"CN_\"");
		if ($ix===false){
			$ix=strpos($linea,'|CN_|');
		}
		if ($ix===false){
		}else{
			$ix=strpos($linea," ");
			$tag_ctl=trim(substr($linea,0,$ix));
			break;
		}
	}
	// Si no se ha definido el tag para el número de control en la fdt, se produce un error
	if ($tag_ctl==""){
		$error="missingctl";
	}
}


function LeerFdt($base){
global $tag_ctl,$pref_ctl,$arrHttp,$db_path,$lang_db,$msgstr;
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