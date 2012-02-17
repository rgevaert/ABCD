<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      diccionario.php
 * @desc:      terms dictionary, manage dictionary of terms
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
if (!isset($_SESSION['permiso'])) die;
include("../config.php");

include("../lang/admin.php");



// Para presentar el diccionario de términos

function PresentarDiccionario(){
global $arrHttp,$terBd,$xWxis,$db_path,$Wxis;


	if ($arrHttp["Opcion"]=="ir_a"){
		$arrHttp["LastKey"]=$arrHttp["prefijo"].$arrHttp["IrA"];
	}
	$arrHttp["Opcion"]="diccionario";
	$Prefijo=$arrHttp["prefijo"];
	$IsisScript= $xWxis."ifp.xis";
	$arrHttp["Formato"]="";
	$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["cipar"]."&Formato=".$arrHttp["Formato"]."&Opcion=".$arrHttp["Opcion"]."&prefijo=".$arrHttp["prefijo"]."&campo=".$arrHttp["campo"]."&Diccio=".$arrHttp["Diccio"]."&prologo=".$arrHttp["prologo"]."&LastKey=".$arrHttp["LastKey"];
	include("../common/wxis_llamar.php");
	foreach ($contenido as $linea){
		$pre=trim(substr($linea,0,strlen($arrHttp["prefijo"])));
		if ($pre==$arrHttp["prefijo"]){
			$ter=substr($linea,strlen($arrHttp["prefijo"]));
			$tt=explode('|',$ter);
			$ll=explode('|',$linea);
			echo "<option value='".$ll[0]."'>".$tt[0]." (".$tt[1].")"."\n";
			$mayorclave=$linea;
		}
	}

	$arrHttp["LastKey"]=$mayorclave;
	$arrHttp["Opcion"]="epilogo";

}




// ------------------------------------------------------
// INICIO DEL PROGRAMA
// ------------------------------------------------------

include("../common/get_post.php");

//foreach ($arrHttp as $var => $value) 	echo "$var = $value<br>";


include("ifpro.php");

switch ($arrHttp["Opcion"]){
	case "diccionario":
		$arrHttp["IsisScript"]="ifp.xis";
		PresentarDiccionario();
		break;
	case "mas_terminos":
		$arrHttp["IsisScript"]="ifp.xis";
		PresentarDiccionario();
		break;
	case "ir_a":
		PresentarDiccionario();
		break;
}

include("ifepil.php");

?>