<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      default_create.php
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
echo "<html><title>Test FDT</title>
<script language=javascript src=js/popcalendar.js></script>\n";

include("../common/get_post.php");
//foreach($arrHttp["ValorCapturado"] as $var=>$value) echo "$var=$value<br>";

$base=$arrHttp["base"];
$arrHttp["cipar"]="$base.par";
$fp=file($db_path.$arrHttp["base"]."/".$arrHttp["base"].".fdt");
global $vars;
$ix=-1;
foreach ($fp as $value){

	$ix=$ix+1;
//	$fdt[$t[1]]=$value;
	$vars[$ix]=$value;
}
$default_values="S";
if (isset($_SESSION["valdef"])){	$default=$_SESSION["valdef"];
	$fp=explode("\n",$default);
	foreach ($fp as $linea){
		$linea=rtrim($linea);
		$tag=trim(substr($linea,0,3))*1;
		$valortag[$tag]=substr($linea,3);
	}
}
include("dibujarhojaentrada.php");
include("ingresoadministrador.php");




?>