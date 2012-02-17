<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      default_edit.php
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
include("../config.php");

include("../lang/admin.php");

require_once ('plantilladeingreso.php');

include("../common/get_post.php");

include("../common/header.php");

if (isset($arrHttp["encabezado"])){
		echo "<body>";
		include("../common/institutional_info.php");
		if ($arrHttp["base"]=="users") $retorno="loans";
		if ($arrHttp["base"]=="acces") $retorno="usersadm";
		echo "
		<div class=\"sectionInfo\">
				<div class=\"breadcrumb\">
					";
				if ($arrHttp["Mfn"]=="New") echo "<h3>". $msgstr["newoper"]."</h3>\n";
				echo "</div>
				<div class=\"actions\">
					<a href=\"../$retorno/browse.php?encabezado=s\" class=\"defaultButton backButton\">
						<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
						<span><strong>".$msgstr["back"]."</strong></span>
					</a>
				</div>
				<div class=\"spacer\">&#160;</div>
			</div>
		</div>
	 	<div class=\"middle form\">
			<div class=\"formContent\">
			";
	}else{
		echo "
	<div class=\"middle form\">
				<div class=\"formContent\">
			";
	}

if (isset($arrHttp["wks"])){
	$wk=explode('|',$arrHttp["wks"])  ;
	$arrHttp["wks"]=$wk[0];
	if (isset($wk[1]))
		$arrHttp["wk_tipom_1"]=$wk[1];
	else
		$arrHttp["wk_tipom_1"]="";
	if (isset($wk[2]))
		$arrHttp["wk_tipom_2"]=$wk[2];
	else
		$arrHttp["wk_tipom_2"]="";

}else{	$arrHttp["wks"]="";}
$base=$arrHttp["base"];
$arrHttp["cipar"]="$base.par";
if (file_exists($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".fdt"))
	$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".fdt";
else
	$archivo=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$arrHttp["base"].".fdt";
$fp=file($archivo);
global $vars;
$ix=-1;
foreach ($fp as $value){

	$ix=$ix+1;
//	$fdt[$t[1]]=$value;
	$vars[$ix]=$value;
}
$default_values="S";
if (isset($_SESSION["valdef"])){
	$default=$_SESSION["valdef"];
	$fp=explode("\n",$default);
	foreach ($fp as $linea){
		$linea=rtrim($linea);
		$tag=trim(substr($linea,0,4))*1;
		$valortag[$tag]=substr($linea,4);
	}
}

PlantillaDeIngreso();
include("dibujarhojaentrada.php");
include("ingresoadministrador.php");

echo "</div></div>\n";
		include("../common/footer.php");
		die;


?>