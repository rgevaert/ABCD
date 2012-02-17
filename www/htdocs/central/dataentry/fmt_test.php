<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      fmt_test.php
 * @desc:      FMT test
 * @author:    Guilda Ascencio
 * @since:     20091203
 * @version:   1.0
 *
 * == BEGIN LICENSE ==
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Lesser General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Lesser General Public License for more details.
 *   
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *   
 * == END LICENSE ==
*/
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../config.php");
include("../common/header.php");

$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");

include("../lang/admin.php");
include("../common/get_post.php");

$base=$arrHttp["base"];
$arrHttp["cipar"]="$base.par";

//Se lee el archivo .fdt de la base de datos para construir la hoja de ingreso

$fpTm = file($db_path.$base."/def/".$_SESSION["lang"]."/$base.fdt");
if (!$fpTm) {
	$fpTm = file($db_path.$base."/def/".$lang_db."/$base.fdt");
}
if (!$fpTm) {
   	echo $base."/$base.fdt".": ".$msgstr["ne"];
	die;
}
$base_fdt=array();
foreach ($fpTm as $linea){
	if (trim($linea)!="") {
		$base_fdt[]=$linea;
	}
}

$t=explode("\n",urldecode($arrHttp["fmt"]));

$ix=-1;
global $vars;
$fdt=array();

//Se construye el formato de entrada tomando los valores de la FDT
foreach ($t as $value){
	if (trim($value)!=""){		$tx=explode('|',$value) ;
		if (trim($tx[1])=="") {			$ix=$ix+1;
//			$fdt[$ix]=$value;
			$vars[$ix]=$value;		}else{
			$primeravez="S";			foreach ($base_fdt as $lin){
				$vx=array();				$vx=explode('|',$lin);
				if ($vx[1]==$tx[1] or $primeravez=="N"){
					if ($primeravez=="S" and trim($vx[1])!=""){
						$ix=$ix+1;
//						$fdt[$ix]=$lin;
						$vars[$ix]=$lin;						$primeravez="N";					}else{						if (trim($vx[1])!="" or trim($vx[0])=="H"){       //Si la columna de tag no tiene un blanco se termina la lista de los subcampos							break;						}else{							$ix=$ix+1;
//							$fdt[$ix]=$lin;
							$vars[$ix]=$lin;						}					}				}			}		}
	}
}
$fmt_test="S";
include("../dataentry/dibujarhojaentrada.php");
include("../dataentry/leerarchivoini.php");

include("../dataentry/ingresoadministrador.php");



?>

