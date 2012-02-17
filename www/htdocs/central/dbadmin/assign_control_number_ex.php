<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      assign_control_number_ex.php
 * @desc:      ASSIGN CONTROL NUMBERS TO A DATABASE
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
include("../config.php");
$lang=$_SESSION["lang"];
include ("../lang/admin.php");
include ("../lang/dbadmin.php");
include ("../lang/acquisitions.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";
//die;

$base =$arrHttp["base"];
$cipar =$arrHttp["base"].".par";
//GET THE MAX MFN
$IsisScript=$xWxis."administrar.xis";
$query = "&base=".$arrHttp["base"] . "&cipar=$db_path"."par/".$arrHttp["base"].".par&Opcion=status";
include("../common/wxis_llamar.php");
$ix=-1;
foreach($contenido as $linea) {
	$ix=$ix+1;
	if ($ix>0) {
		if (trim($linea)!=""){
	   		$a=explode(":",$linea);
	   		$tag[$a[0]]=$a[1];
	  	}
	}
}

if (isset($arrHttp["encabezado"]))
	$encabezado="&encabezado=S";
else
	$encabezado="";
LeerFdt($arrHttp["base"]);
$db_addto=$arrHttp["base"];
include("../common/header.php");
echo "<body>\n";
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
	$encabezado="&encabezado=s";
}
echo "<div class=\"sectionInfo\">
	<div class=\"breadcrumb\">
";
echo $msgstr["assigncn"].": ".$arrHttp["base"];
echo "	</div>
	<div class=\"actions\">
";

$ayuda="control_number.html";
if (isset($arrHttp["encabezado"])){
	if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_RESETLCN"])){
		echo "<a href=\"assign_control_number.php?base=".$arrHttp["base"]."$encabezado\" class=\"defaultButton backButton\">
		<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
	<span><strong>".$msgstr["back"]."</strong></span></a>
		";
	}else{
		echo "<a href=\"../common/inicio.php?reinicio=s&base=".$arrHttp["base"]."$encabezado\" class=\"defaultButton backButton\">
		<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
	<span><strong>".$msgstr["back"]."</strong></span></a>
		";
	}
}

?>
</div>

<div class="spacer">&#160;</div>
</div>
<div class="middle form">
	<div class="formContent">
<?
//se lee la FDT de la base de datos para determinar el campo donde se almacena el número de control

$Formato=$tag_ctl;
if ($tag_ctl==""){
	echo "<h4>".$msgstr["missingctl"]."</h4>";}else{
	echo $msgstr["cn"]." ".$msgstr["tag"].": $tag_ctl<br>";
	echo "<table>";
	echo "<th>Mfn</th><th>".$msgstr["cn"]."</th>";
	for ($Mfn=$arrHttp["Mfn"];$Mfn<=$arrHttp["to"];$Mfn++){		$control=ProximoNumero($arrHttp["base"]);
		$tag_ctl=trim($tag_ctl);
		if (strlen($tag_ctl)==1) $tag_ctl="000".$tag_ctl;
		if (strlen($tag_ctl)==2) $tag_ctl="00".$tag_ctl;
		if (strlen($tag_ctl)==3) $tag_ctl="0".$tag_ctl;
		$ValorCapturado=$tag_ctl.$control."\n";
		$IsisScript=$xWxis."actualizar.xis";
		$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par&login=".$_SESSION["login"]."&Mfn=$Mfn&Opcion=actualizar&ValorCapturado=".$ValorCapturado;
		include("../common/wxis_llamar.php");
		echo "<tr><td>$Mfn</td><td>$control</td>" ;
		echo "<td>";
		//foreach ($contenido as $value) {		//	if (!empty($value)) 
		//		echo "---$value<br>";
		//}
		echo "</td>";	}

	echo "<form name=forma1 action=assign_control_number.php method=post>
	<input type=hidden name=base value=".$arrHttp["base"].">
	<input type=hidden name=to value=".$Mfn.">
	<input type=hidden name=encabezado value=s>";
	if ($Mfn<$tag["MAXMFN"])
		echo "<input type=submit name=go value=".$msgstr["continuar"].">";
    echo "
	</form>
	";
}

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
			echo "missing file ".$base."/".$base.".fdt";
		    die;
		 }
	}
	$tag_ctl="";
	$pref_ctl="";
	foreach ($fp as $linea){
		$l=explode('|',$linea);
		if ($l[0]=="AI"){
			$tag_ctl=$l[1];
			if (empty($l[12]))  $l[12]="NC_";  //if the prefix in not defined NC_ is assumed
			$pref_ctl=$l[12];
		}
	}
	// Si no se ha definido el tag para el número de control en la fdt, se produce un error
}


function ProximoNumero($base){
global $db_path;
	$archivo=$db_path.$base."/data/control_number.cn";
	if (!file_exists($archivo)){
		$fp=fopen($archivo,"w");
		$res=fwrite($fp,"");
		fclose($fp);
	}
	$perms=fileperms($archivo);
	if (is_writable($archivo)){
	//se protege el archivo con el número secuencial
		chmod($archivo,0555);
	// se lee el último número asignado y se le agrega 1
		$fp=file($archivo);
		$cn=implode("",$fp);
		$cn++;
	// se remueve el archivo .bak y se renombre el archivo .cn a .bak
		if (file_exists($db_path.$base."/data/control_number.bak"))
			unlink($db_path.$base."/data/control_number.bak");
		$res=rename($archivo,$db_path.$base."/data/control_number.bak");
		chmod($db_path.$base."/data/control_number.bak",0666);
		$fp=fopen($archivo,"w");
	    fwrite($fp,$cn);
	    fclose($fp);
	    chmod($archivo,0666);
	    return $cn;
	}
}