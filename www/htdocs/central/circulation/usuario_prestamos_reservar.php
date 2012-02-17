<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      usuario_prestamos_reservar.php
 * @desc:      Renews items to an user
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

// localizar el item que se desea prestar y determinar si se presta o se reserva
// se llama desde prestar_procesar.php

if (!isset($_SESSION["login"])) die;
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
include("../config_loans.php");
$lang=$_SESSION["lang"];
include("../lang/prestamo.php");

include("fecha_de_devolucion.php");

function VerReservas($signatura,$usuario){
global $db_path,$xWxis,$Wxis,$msgstr;
   	$query = "&Expresion=TU_".$signatura."_".$usuario."&base=reserva&cipar=$db_path"."par/reserva.par&Pft=v20/";
	$contenido="";
	echo $query;
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$msg="";
	foreach ($contenido as $linea){
		$linea=trim($linea);
//		echo "--$signatura=$linea--<br>";
		if ($signatura==$linea){
			$msg=$msgstr["yareservado"];
			break;
		}
	}
	return $msg;
}

function Reservar($nv,$total_ej,$inventario,$signatura,$tipo_obj,$titulo,$usuario,$tipo_usuario){
global $db_path,$xWxis,$Wxis,$arrHttp,$msgstr;
	if ($nv>0){
		echo "<h3><font color=red>$nv ".$msgstr["vence"]." ".$msgstr["noreserve"]."</font></h3>";
		return;
	}
	echo $msgstr["reservas"].": ";
	$todays_date = date("Ymd");
	$ValorCapturado="0001R\n0010$usuario\n0020$signatura\n0030".$arrHttp["inventory"]."\n0040$todays_date\n0050$tipo_usuario\n0060$tipo_obj";
	$ValorCapturado=urlencode($ValorCapturado);
	$IsisScript=$xWxis."crear_registro.xis";
	$Pft="v1102";
	$query = "&base=reserva&cipar=$db_path"."par/reserva.par&login=".$_SESSION["login"]."&Pft=$Pft&Mfn=New&ValorCapturado=".$ValorCapturado;
	include("../common/wxis_llamar.php");
	$exit="";
	foreach ($contenido as $value) $exit.=$value;
	if ($exit==0)
		$reservado="S";
	else
		$reservado="N";
	echo "<table bgcolor=#cccccc><tr><td bgcolor=white valign=top>$signatura</td><td >$todays_date</td><td>$tipo_obj</td><td>$titulo</td></tr>";

	echo "</table>";
}
Function Iso2Fecha($fecha){
	$f=substr($fecha,6,2)."/".substr($fecha,4,2)."/".substr($fecha,0,4);
	return $f;
}

// se determina si el préstamo está vencido
function compareDate ($FechaP){
global $locales;

//Se convierte la fecha a formato ISO (yyyymmaa) utilizando el formato de fecha local
	switch ($locales["date1"]){
		case "d":
			$dia=substr($FechaP,0,2);
			break;
		case "m":
			$mes=substr($FechaP,0,2);
			break;
	}
	switch ($locales["date2"]){
		case "d":
			$dia=substr($FechaP,3,2);
			break;
		case "m":
			$mes=substr($FechaP,3,2);
			break;
	}
	$year=substr($FechaP,6,4);
	$exp_date=$year."-".$mes."-".$dia;
	$todays_date = date("Y-m-d");
	$today = strtotime($todays_date);
	$expiration_date = strtotime($exp_date);
	$diff=$expiration_date-$today;
	return $diff;

}//end Compare Date

///////////////////////////////////////////////////////////////////////////////////////////

include("leer_pft.php");
// se lee la configuración de la base de datos de objetos de préstamos
$arrHttp["base"]="biblo/loans/";
include("databases_configure_read.php");
// se lee la configuración local
include("calendario_read.php");
include("locales_read.php");
// se leen las politicas de préstamo
include("loanobjects_read.php");
// se lee la configuración de la base de datos de usuarios
include("borrowers_configure_read.php");


$valortag = Array();
include("../common/get_post.php");
//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";
if ($arrHttp["Opcion"]=="reservar")
	$msg_1=$msgstr["reserve"];
else
	if ($arrHttp["Opcion"]=="prestar") $msg_1=$msgstr["loan"];

include ('../dataentry/leerregistroisispft.php');




// ------------------------------------------------------

include("../common/header.php");

echo "<body>";
 include("../common/institutional_info.php");
$link_u="";
if (isset($arrHttp["usuario"])) $link_u="&usuario=".$arrHttp["usuario"];
?>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msg_1?>
	</div>
	<div class="actions">
		<?php include("submenu_prestamo.php");?>

	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/prestamo_procesar.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php if ($_SESSION["permiso"]=="loanadm"){
		echo "        		<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/prestamo_procesar.html target=_blank>".$msgstr["edhlp"]."</a>";
      	echo "<font color=white>&nbsp; &nbsp; Script: usuario_prestamos_reservar.php</font>\n";
      }
?>
	</div>
<div class="middle form">
	<div class="formContent">
<?php

include("ec_include.php");  //se incluye el procedimiento para leer el usuario y los préstamos pendientes

//Se obtiene el código, tipo y vigencia del usuario
$formato=$pft_uskey.'\'$$\''.$pft_ustype.'\'$$\''.$pft_usvig;
$formato=urlencode($formato);
$query = "&Expresion=".trim($uskey).$arrHttp["usuario"]."&base=users&cipar=$db_path"."par/users.par&Pft=$formato";
$contenido="";
$IsisScript=$xWxis."cipres_usuario.xis";
include("../common/wxis_llamar.php");
$user="";
foreach ($contenido as $linea){
	$linea=trim($linea);
	if ($linea!="")  $user.=$linea;
}
$userdata=explode('$$',$user);
if (empty($userdata[2])) 
	$vig=$msgstr["activo"];
else
	$vig=$userdata[2];

//Se analiza la vigencia del usuario
switch($userdata[2]){
	case "1":
		echo "<font color=red>".$msgstr["userinval"]."</font>";
		break;
	case "2":
	    echo "<font color=red>".$msgstr["usersusp"]."</font>";
		break;
}

//if (trim($userdata[2])!=""){
//	echo "<xmp>--------".$msgstr[$userdata[2]]."----------</xmp>";
//	die;
//}
//Se determina el total de objetos permitidos prestar al usuario
$tprestamos_p=0;
foreach ($politica as $key=>$ob){
	foreach($ob as $key1=>$obj1)
	if ($key1==$userdata[1]){
		$obj=explode('|',$obj1);
		$tprestamos_p+=$obj[2];
    }
}
echo "Total objetos a prestar: $tprestamos_p;  Total objetos prestados: $np;  Total objetos en mora: $nv<p>";
$ejemplar=urldecode($arrHttp["ejemplar"]);
// se extrae la información del ejemplar a prestar o reservar
$ej=explode('||',$ejemplar);
$total_ej=$ej[0];      //total de ejemplares
$signatura=$ej[2];     //signatura topográfica
$inventario=$ej[1];    //Número de inventario
$tipo_obj=$ej[3];      //Tipo de objeto


// se consulta la base de datos de reservas a ver si este item ya está reservado

$msg=VerReservas($signatura,$arrHttp["usuario"]);

if ($msg==""){
	Reservar($nv,$total_ej,$inventario,$signatura,$tipo_obj,urldecode($arrHttp["titulo"]),$userdata[0],$userdata[1]);
}else{
	echo "$msg";
}
?>