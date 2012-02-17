<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      fdt_leer.php
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
$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");
include("../common/get_post.php");

if (!isset($arrHttp["Opcion"])) $arrHttp["Opcion"]="";
?>
<html>
<link rel="STYLESHEET" type="text/css" href="../css/styles.css">
<style>
	td{
		font-family:arial;
		font-size:10px;
	}
</style>
<?php echo "<font size=1 face=arial color=white>Script: fdt_leer.php</font><br>"?>
 <b><font face=arial size=1 color=white><?php echo $msgstr["fdt"].". ".$msgstr["database"]?>: <?php echo $arrHttp["base"]?>
 <font color=black>
		<table bgcolor=#EEEEEE width=100%>
			<td><?php echo $msgstr["tag"]?></td><td><?php echo $msgstr["fn"]?></td><td><?php echo $msgstr["subfields"]?></td>
<?
if ($arrHttp["Opcion"]!="new"){
	$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".fdt";
	if (file_exists($archivo)){
		$fp=file($archivo);
	}else{
		$archivo=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$arrHttp["base"].".fdt";
		if (file_exists($archivo)){
			$fp=file($archivo);
		}else{
			echo $msgstr["misfile"].": ".$arrHttp["base"]."/".$arrHttp["base"].".fdt";
	    	die;
	  	}
	}
}else{
	$fp=explode("\n",$_SESSION["FDT"]);
}
foreach ($fp as $value){
	$t=explode('|',$value);
	echo "<tr><td bgcolor=white>$t[1]</td><td bgcolor=white>".$t[2]."</td><td bgcolor=white>".$t[5]."</td>";
}
?>
</table>
