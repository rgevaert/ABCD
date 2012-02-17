<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      renovar_ex.php
 * @desc:      Renew EX
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
if (!isset($arrHttp["vienede"]) or $arrHttp["vienede"]!="ecta_web"){
	if (!isset($_SESSION["permiso"])){
		header("Location: ../common/error_page.php") ;
	}
}
include("../config.php");
include("../common/get_post.php");
//date_default_timezone_set('UTC');
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
//die;

$lang=$_SESSION["lang"];
include("../lang/admin.php");
include("../lang/prestamo.php");

include("fecha_de_devolucion.php");
include("sanctions_read.php");

function compareDate ($FechaP){
global $locales;

	$dia=substr($FechaP,6,2);
	$mes=substr($FechaP,4,2);
	$year=substr($FechaP,0,4);
	$exp_date=$year."-".$mes."-".$dia;
	$todays_date = date("Y-m-d");
	$today = strtotime($todays_date);
	$expiration_date = strtotime($exp_date);
	$diff=$expiration_date-$today;
	return $diff;

}//end Compare Date




//Se ubica el ejemplar prestado en la base de datos de transacciones
$inventario="TR_P_".trim($arrHttp["searchExpr"]);
if (!isset($arrHttp["base"])) $arrHttp["base"]="trans";
$Formato="v10'|$'v20'|$'v30'|$'v35'|$'v40'|$'v45'|$'v70'|$'v80'|$'v100,'|$'f(nocc(v200),1,0)'|$'v400/";
$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["base"].".par&count=1&Expresion=".$inventario."&Pft=$Formato";
$contenido="";
$IsisScript=$xWxis."buscar_ingreso.xis";
include("../common/wxis_llamar.php");
$Total=0;
foreach ($contenido as $linea){
	$linea=trim($linea);
	if ($linea!="") {
		$l=explode('|$',$linea);
		if (substr($linea,0,6)=="[MFN:]"){			$Mfn=trim(substr($linea,6));		}else{			if (substr($linea,0,8)=="[TOTAL:]"){				$Total=trim(substr($linea,8));			}else{				$prestamo=$linea;			}
		}
	}
}

$error="";
//echo "Mfn=$Mfn<p>" ;
if ($Total==0){
	$error="&error=".$msgstr["item"]." ".$msgstr["noloan"];
	Regresar($error);
	die;
}

// se extrae la información del ejemplar a devolver
$p=explode('|$',$prestamo);
$cod_usuario=$p[1];
$inventario=$p[0];
$fecha_p=$p[2];
$hora_p=$p[3];
$fecha_d=$p[4];
$hora_d=$p[5];
$tipo_usuario=$p[6];
$tipo_objeto=$p[7];
$referencia=$p[8];
$no_renova=$p[9];         // Número de renovaciones procesadas
$ppres=$p[10];    //Loan policy

// se lee la política de préstamos
include("loanobjects_read.php");
// se lee el calendario
include("calendario_read.php");
// se lee la configuración local
include("locales_read.php");

//se determina la política a aplicar
if ($ppres==""){
	$ppres=$politica[$tipo_objeto][$tipo_usuario];   //read the policy
	if (trim($ppres)==""){
		$ppres=$politica[strtoupper($tipo_objeto)][trim(strtoupper($tipo_usuario))];
	}
	if (trim($ppres)==""){
		$ppres=$politica["0"]["0"];
	}
}
$p=explode('|',$ppres);
$lapso=$p[3];
$unidad=$p[5];
//se verifica si el objeto admite más renovaciones
if ($no_renova>=$p[6]){	$error="&error=".$msgstr["nomorenew"];
	Regresar($error);}

//se verifica la fecha límite del usuario
if (trim($p[15])!=""){
	if (compareDate ($p[15])>0){		$error="&error=".$msgstr["limituserdata"]." ".$msgstr["noitrenew"];
		Regresar($error);
	}
}
// se verifica la fecha límite del objeto
if (trim($p[16])!=""){
	if (compareDate ($p[16])>0){
		$error="&error=".$msgstr["limitobjectdata"]." ".$msgstr["noitrenew"];
		Regresar($error);
	}
}

// Se calcula si hay atraso en la fecha de devolución
$atraso=compareDate($fecha_d);

if ($atraso<0){
	if ($p[13]!="Y"){  // se verifica si la política permite renovar cuando está atrasado		$error="&error=".$msgstr["loanoverdued"];
		Regresar($error);
	}
//	Sanciones($fecha_d,$atraso,$cod_usuario,$inventario,$politica);
}



// se verifica si tiene reservas


// Se pasa la fecha de préstamo y devolución anteriores al campo 200
$f_ant="^a".$fecha_p."^b".$hora_p."^c".$fecha_d."^d".$hora_p."^e".$_SESSION["login"];
//se calcula la nueva fecha de devolución
$fecha_dev=FechaDevolucion($lapso,$unidad,"");
$fecha_pres=date("Ymd h:i:s A");
$ixp=strpos($fecha_dev," ");
$fecha_d=trim(substr($fecha_dev,0,$ixp));
$hora_d=trim(substr($fecha_dev,$ixp));
$ixp=strpos($fecha_pres," ");
$fecha_p=trim(substr($fecha_pres,0,$ixp));
$hora_d=trim(substr($fecha_pres,$ixp));

$ValorCapturado="d30\nd35\nd40\nd45\n";
$ValorCapturado.="a30~".$fecha_p."~\n";
//$ValorCapturado.="a35~".$hora_p."~\n";
$ValorCapturado.="a40~".$fecha_d."~\n";
//$ValorCapturado.="a45~".$hora_d."~\n";
$ValorCapturado.="a200~".$f_ant."~";
$ValorCapturado=urlencode($ValorCapturado);
$IsisScript=$xWxis."updaterec.xis";
$Formato="";
$query = "&base=trans&cipar=$db_path"."par/trans.par&login=".$_SESSION["login"]."&Mfn=".$Mfn."&ValorCapturado=".$ValorCapturado;
include("../common/wxis_llamar.php");
Regresar("");
die;

function Regresar($error){global $arrHttp,$cod_usuario;
    if (isset($arrHttp["vienede"]) and $arrHttp["vienede"]=="ecta_web"){    	header("Location: opac_statment_ex.php?usuario=$cod_usuario$error&vienede=ecta_web");
    	die;    }	$cu="";
	if (isset($arrHttp["usuario"]) and !isset($cod_usuario)){
		$cu="&usuario=".$arrHttp["usuario"];
	}else{
		$cu="&usuario=$cod_usuario";
		header("Location: usuario_prestamos_presentar.php?encabezado=s$error$cu");
	}
	die;}





?>