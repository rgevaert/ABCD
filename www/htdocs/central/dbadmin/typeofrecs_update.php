<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      typeofrecs_update.php
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
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";

include("../common/header.php");
if (isset($arrHttp["encabezado"])){
    include("../common/institutional_info.php");
	$encabezado="&encabezado=s";
}else{	$encabezado="";}
echo "
	<div class=\"sectionInfo\">
	<div class=\"breadcrumb\">".$msgstr["typeofrecords"].": ".$arrHttp["base"]."</div>
	<div class=\"actions\">\n";
echo "<a href=menu_modificardb.php?base=". $arrHttp["base"].$encabezado." class=\"defaultButton backButton\">
	<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
		<span><strong>". $msgstr["back"]."</strong></span>
		</a>
		</div>
			<div class=\"spacer\">&#160;</div>
		</div>";
echo "<div class=\"middle form\">
			<div class=\"formContent\">";
$cell=array();
foreach ($arrHttp as $var=>$value) {
	if (substr($var,0,4)=="cell"){		$c=explode("_",$var);
		if ($value=="_") $value="";
		if (isset($cell[$c[0]]))
			$cell[$c[0]].='|'.$value;
		else
			$cell[$c[0]]=$value;
	}else{	}}

$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/typeofrecord.tab";
$fp=fopen($archivo,"w");
if (!isset($arrHttp["nivelr"])) $arrHttp["nivelr"]="";
$res=fwrite($fp,$arrHttp["tipom"]." ".$arrHttp["nivelr"]."\n");
foreach ($cell as $value){
	$l=explode('|',$value);
	$linea=str_replace('|',"",$value);
	if (!empty($value)) $res=fwrite($fp,$value."\n");;
}
fclose($fp);
echo "<center><h4>".$arrHttp["base"]."/def/".$_SESSION["lang"]."/typeofrecord.tab"." ".$msgstr["updated"]."</h4>";
if (isset($arrHttp["encabezado"]))
	$encabezado="&encabezado=s";
else
	$encabezado="";
if (isset($arrHttp["Opcion"]) and $arrHttp["Opcion"]=="tipom"){	echo "<p><a href=typeofrecs_edit.php?base=".$arrHttp["base"]."$encabezado>".$msgstr["typeofrecords_create"]."</a>";}

echo "</center></div></div>";
include("../common/footer.php");
echo "</body></html>";
?>