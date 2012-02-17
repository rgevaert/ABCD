<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      winisis_upload_fst.php
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
include("../lang/dbadmin.php");
include("../lang/soporte.php");
$lang=$_SESSION["lang"];
include("../common/get_post.php");


function CrearFst($Fst_w){
global $arrHttp,$msgstr;
$fdt=array();
	$ix=0;
	echo "<p><dd><span class=title>".$msgstr["subir"]." ".$arrHttp["base"].".fst"."</span>";
	$salida="";
	$fst=explode("\n",$Fst_w);
	echo "<dd><table bgcolor=#cccccc>
		<td width=80 bgcolor=white align=center>".$msgstr["id"]."</td><td nowrap width=50 bgcolor=white align=center>".$msgstr["fstit"]."</td><td width=90% bgcolor=white>".$msgstr["formate"]."</td>";
	foreach ($fst as $value){
		if (!empty($value)) {
			$ix=strpos($value," ");
			$id=substr($value,0,$ix);
			$ix1=strpos($value," ",$ix+1);
			$it=substr($value,$ix,$ix1-$ix);
			$pft=substr($value,$ix1+1);
			echo "<tr><td bgcolor=white valign=top align=center>$id</td><td bgcolor=white valign=top align=center>$it</td><td bgcolor=white valign=top>$pft</td>";
		}
	}
	echo "</table>";
	echo "<dd><h3>" .$arrHttp["base"].".fst ".$msgstr["transferido"]."!</h3></dd>";
}




//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";

include("../common/header.php");
echo "<script  src=\"../dataentry/js/lr_trim.js\"></script>\n";
echo "<script languaje=javascript>
function EnviarForma(){
	if (Trim(document.winisis.userfile.value)==''){
		alert('".$msgstr["missing"]." ".$msgstr["pft"]."')
		return
	}
	ext=document.winisis.userfile.value
	e=ext.split(\".\")
	if (e.length==1 || e[1].toUpperCase()!=\"PFT\"){
		alert('".$msgstr["missing"]." ".$msgstr["pft"]."')
		return
	}
	document.winisis.submit()
}
</script>";
if (isset($arrHttp["encabezado"]))
	include("../common/institutional_info.php");
echo "
	<div class=\"sectionInfo\">

			<div class=\"breadcrumb\"><h5>".
				$msgstr["winisisdb"].": " . $arrHttp["base"]."</h5>
			</div>

			<div class=\"actions\">
	";
if (isset($arrHttp["encabezado"]))
		$encabezado="&encabezado=s";
	else
		$encabezado="";
echo "<a href=winisis_upload_fdt.php?base=".$arrHttp["base"]."&nombre=".$arrHttp["base"]."&desc=".urlencode($arrHttp["desc"]).$encabezado." class=\"defaultButton backButton\">";
echo "
					<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
					<span><strong>". $msgstr["back"]."</strong></span>
				</a>
			</div>
			<div class=\"spacer\">&#160;</div>
	</div>";

echo "
	<div class=\"helper\">
	<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/winisis_upload_fst.html target=_blank>".$msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/winisis_upload_fst.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: winisis_upload_fst.php</font></div>";

echo "
<div class=\"middle form\">
			<div class=\"formContent\">";
if (!isset($_SESSION["FST"])){
	$files = $_FILES;
	if ($files['userfile']['size']) {
      // clean up file name
		$name = preg_replace("/[^a-z0-9._]/", "",
  			str_replace(" ", "_",
  			str_replace("%20", "_", strtolower($name)
		)
 			)
   		);
 		$fp=file($files['userfile']['tmp_name']);
  		$Fst="";
   		foreach($fp as $linea) $Fst.=$linea;
		CrearFst($Fst);
		$_SESSION["FST"]=$Fst;
	}
}else{	CrearFst($_SESSION["FST"]);}
echo "
<form name=winisis action=winisis_upload_pft.php method=POST enctype=multipart/form-data onsubmit='javascript:EnviarForma();return false'>
<input type=hidden name=Opcion value=PFT>
<input type=hidden name=base value=".$arrHttp["base"].">
<input type=hidden name=desc value=\"".$arrHttp["desc"]."\"> ";
if (isset($arrHttp["encabezado"])) echo "<input type=hidden name=encabezado value=s>\n";
echo "<dd><table bgcolor=#eeeeee>
<tr>
<tr><td class=title>".$msgstr["subir"]." ".$arrHttp["base"]. ".pft</td>

<tr><td><input name=userfile type=file size=50></td><td></td>
<tr><td>  <input type=submit value='".$msgstr["subir"]."'></td>
</table></dd>

</form>";
echo "<br><dd><a href=menu_creardb.php?base=".$arrHttp["base"]."$encabezado>".$msgstr["cancel"]."</a> &nbsp; &nbsp;
	<a href=winisis_upload_fdt.php?base=".$arrHttp["base"]."&nombre=".$arrHttp["base"]."&desc=".urlencode($arrHttp["desc"]);
if (isset($arrHttp["encabezado"])) echo "&encabezado=s";
echo ">".$msgstr["back"]."</a> &nbsp; &nbsp; &nbsp; &nbsp;
	</p></dd>";
echo "</div></div>\n";
include("../common/footer.php");
?>
