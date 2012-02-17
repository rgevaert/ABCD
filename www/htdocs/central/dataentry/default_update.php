<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      default_update.php
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
<link rel=stylesheet href=../styles/basic.css type=text/css>\n";
echo "<font size=1 face=arial> &nbsp; &nbsp; Script: default_update.php<BR>";
global $ValorCapturado;
include("actualizarregistro.php");
require_once ('plantilladeingreso.php');

function VariablesDeAmbiente($var,$value){
global $arrHttp;


		if (substr($var,0,3)=="tag") {
			$ixpos=strpos($var,"_");
			if ($ixpos!=0) {
				$occ=explode("_",$var);
				if (trim($value)!=""){
					$value="^".trim($occ[2]).$value;
					$var=$occ[0]."_".$occ[1];
					if (is_array($value)) {
						$value = implode("\n", $value);
					}
					if (isset($arrHttp[$var])){
						$arrHttp[$var].=$value;
					}else{
						$arrHttp[$var]=$value;
					}
				}
			}else{
				if (is_array($value)) {
			   		$value = implode("\n", $value);
				}
				if (isset($arrHttp[$var])){
					$arrHttp[$var].="\n".$value;
				}else{
					$arrHttp[$var]=$value;
				}
			}
		}else{
			if (trim($value)!="") $arrHttp[$var]=$value;
		}
}

if (isset($_GET)){
	foreach ($_GET as $var => $value) {
		VariablesDeAmbiente($var,$value);
	}
}if (isset($_POST)){
	foreach ($_POST as $var => $value) {
		VariablesDeAmbiente($var,$value);
	}
}
include("../common/get_post.php");


foreach ($arrHttp as $var => $value) {
	if (substr($var,0,3)=="tag" ){
		$tag=explode("_",$var);
		if (substr($var,0,3)=="sta" ){
			$tag[0]="tag".substr($tag[0],3);
		}
		if (isset($variables[$tag[0]])){

			$variables[$tag[0]].="\n".$value;
			$valortag[substr($tag[0],3)].="\n".$value;
		}else{
			$variables[$tag[0]]=$value;
			$valortag[substr($tag[0],3)]=$value;
		}
   	}

}
//foreach ($valortag as $key => $value) echo "$key=$value<br>";
//foreach($arrHttp as $var=>$value) echo "$var=$value<br>";

//foreach ($variables as $tag=>$value)  echo "$var=$value<br>";
include("../config.php");
$base=$arrHttp["base"];
$arrHttp["cipar"]="$base.par";
$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".fdt";
if (!file_exists($archivo)) $archivo=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$arrHttp["base"].".fdt";
$fp=file($archivo);
global $vars;
$ix=-1;
foreach ($fp as $value){

	$ix=$ix+1;
	$vars[$ix]=$value;
}
$default_values="S";
ActualizarRegistro();
$_SESSION["valdef"]=$ValorCapturado;
echo "<xmp>".$_SESSION["valdef"]."</xmp>";
echo "<br><br><h4>".$msgstr["valdef"]." ".$msgstr["actualizados"]."</h4>";
//echo $ValorCapturado;




?>