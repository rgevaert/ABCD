<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      eliminar_registro.php
 * @desc:      Delete record
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
require_once("../config.php");
$lang=$_SESSION["lang"];

require_once ("../lang/admin.php");
require_once ("../lang/msgusr.php");


include("../common/get_post.php");
//foreach ($arrHttp as $var => $value) 	echo "$var = $value<br>";
//die;
//SE VERIFICA SI HAY FORMATO DE VALIDACION DE LA ELIMINACION
$archivo=$db_path.$arrHttp["base"]."/pfts/recdel_val";
$verify="";
if (file_exists($archivo.".pft")){
	$verify="Y";
}else{
    $archivo=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/recdel_val";
    if (file_exists($archivo.".pft")){
		$verify="Y";
	}else{
		$archivo=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/recdel_val";
       	if (file_exists($archivo.".pft")){
			$verify="Y";
		}
	}
}
$err_del="";
if ($verify=="Y") $res=VerificarEliminacion($archivo);
if ($res==""){
	$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par&login=".$_SESSION["login"]."&Mfn=" . $arrHttp["Mfn"]."&Opcion=eliminar";
	$IsisScript=$xWxis."eliminarregistro.xis";
	include("../common/wxis_llamar.php");
}else{	$err_del="&error="."$res";}
$encabezado="";
if (isset($arrHttp["encabezado"]))
	$encabezado="?encabezado=s";
else
	$encabezado="?" ;
if ($arrHttp["base"]=="users") $retorno="loans";
if ($arrHttp["base"]=="acces") $retorno="usersadm";
if (isset($arrHttp["retorno"])){
	if (isset($arrHttp["Expresion"])) $arrHttp["retorno"].="&Expresion=".$arrHttp["Expresion"];
	if (isset($arrHttp["from"])) $arrHttp["retorno"].="&from=".$arrHttp["from"].$err_del;
	header("Location:".$arrHttp["retorno"]);
	die;
}else{
	$retorno="../dataentry/browse.php";
	header("Location:$retorno$encabezado"."&base=".$arrHttp["base"]."&return=".$arrHttp["return"]."&from=".$arrHttp["from"].$err_del);
}

include ("verificar_eliminacion");
?>