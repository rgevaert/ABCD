<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      recval_save.php
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
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";

$pft=explode("\n",$arrHttp["ValorCapturado"]);
if (isset($arrHttp["encabezado"]))
	$encabezado="&encabezado=S";
else
	$encabezado="";
$lang=$_SESSION["lang"];
$fp=fopen($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["fn"],"w");
if (!$fp){
	echo $arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["fn"].": ";	echo $msgstr["nopudoseractualizado"];
	die;}
foreach ($pft as $value){	$tag=substr($value,0,4);
	$value=trim(substr($value,4));
	fwrite($fp,ltrim($tag, "0").":".urldecode($value)."\n###\n");
}
fclose($fp);
include("../common/header.php");
echo "<body>";
if (isset($arrHttp["encabezado"])){
    	include("../common/institutional_info.php");
	$encabezado="&encabezado=s";
}else{
	$encabezado="";
}
echo "
	<div class=\"sectionInfo\">
	<div class=\"breadcrumb\">".$msgstr["recval"].": ".$arrHttp["base"]."</div>
	<div class=\"actions\">\n";
echo "<a href=typeofrecs.php?base=". $arrHttp["base"].$encabezado." class=\"defaultButton backButton\">
	<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
		<span><strong>". $msgstr["back"]."</strong></span>
		</a>
		</div>
			<div class=\"spacer\">&#160;</div>
		</div>";
echo "<div class=\"middle form\">
			<div class=\"formContent\">";
echo "<font size=1 face=arial> &nbsp; &nbsp; Script: recval_save.php</font>";
echo "<center><h4>".$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["fn"].": ".$msgstr["updated"];
echo "</h4></center></div></div>";
include("../common/footer.php");
echo "</body>
</html>";
?>