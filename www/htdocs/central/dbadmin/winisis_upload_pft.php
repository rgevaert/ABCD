<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      winisis_upload_pft.php
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


function CrearPft($Pft_w){
global $arrHttp,$msgstr;
	$ix=0;
	echo "<p><dd><span class=title>".$msgstr["subir"]." ".$arrHttp["base"].".pft"."</span>";
	$salida="";
	echo "<dd><table bgcolor=#FFFFFF>
		<td>";
	echo "<xmp>$Pft_w</xmp>";
	echo "</dd></table>";
	echo "<dd>" .$arrHttp["base"].".pft ".$msgstr["transferido"]."!</dd>";
	return $Pft_w;
}

include("../common/get_post.php");
include("../common/header.php");
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
echo "<a href=winisis_upload_fst.php?base=".$arrHttp["base"]."&nombre=".$arrHttp["base"]."&desc=".urlencode($arrHttp["desc"]).$encabezado." class=\"defaultButton backButton\">";

echo "
					<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
					<span><strong>". $msgstr["cancel"]."</strong></span>
				</a>
			</div>
			<div class=\"spacer\">&#160;</div>
	</div>";

echo "
	<div class=\"helper\">
	<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/winisis_upload_pft.html target=_blank>".$msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/winisis_upload_pft.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: winisis_upload_pft.php</font></div>";

echo "
<div class=\"middle form\">
			<div class=\"formContent\">";

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
       	$Pft="";
        foreach($fp as $linea) $Pft.=$linea;
        $Pft_conv=CrearPft($Pft);
}

$_SESSION["PFT"]=$Pft_conv;
echo "<P><dd><a href=menu_creardb.php?base=".$arrHttp["base"].">".$msgstr["cancel"]."</a> &nbsp; &nbsp;
	<a href=winisis_upload_fst.php?base=".$arrHttp["base"]."&nombre=".$arrHttp["base"]."&desc=".urlencode($arrHttp["desc"])."$encabezado>".$msgstr["back"]."</a> &nbsp; &nbsp; &nbsp; &nbsp;
	<a href=crearbd_new_create.php?base=".$arrHttp["base"]."$encabezado>".$msgstr["createdb"]."</a> &nbsp; &nbsp; &nbsp; &nbsp;
	";
echo "</div></div>\n";
include("../common/footer.php");
?>
