<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      prestamo_devorlver.php
 * @desc:      Loan return
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
include("../config.php");
include("../common/get_post.php");

//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
//die;
//función para calcular la diferencia de tiempo entre dos fecha
function dateDiff($dformat, $endDate, $beginDate)
{

	$date_parts1=explode($dformat, $beginDate);
	$date_parts2=explode($dformat, $endDate);
	$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
	$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
	return $end_date - $start_date;
}

//Se ubica el ejemplar prestado en la base de datos de transacciones
$inventario="TR_P_".trim($arrHttp["searchExpr"]);
if (!isset($arrHttp["base"])) $arrHttp["base"]="trans";
$Formato="v10'|'v20'|'v30'|'v35'|'v40'|'v45'|'v70'|'v80'|'v100/";
$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["base"].".par&count=1&Expresion=".$inventario."&Pft=$Formato";
$contenido="";
$IsisScript=$xWxis."buscar_ingreso.xis";
include("../common/wxis_llamar.php");
$Total=0;
foreach ($contenido as $linea){	$linea=trim($linea);
	if ($linea!="") {
		$l=explode('|',$linea);
		if (substr($linea,0,6)=="[MFN:]"){			$Mfn=trim(substr($linea,6));		}else{			if (substr($linea,0,8)=="[TOTAL:]"){				$Total=trim(substr($linea,8));			}else{				$prestamo=$linea;			}
		}
	}
}
$error="";
if ($Total==0){
	$error="&error=Ejemplar no está prestado";
/*	echo "<html><script>
			alert('".$arrHttp["searchExpr"]." Ejemplar no está prestado')
		  	self.location.href=\"devolver.php?encabezado=s\"
		  </script></html>
	";
	die; */
}
// se extrae la información del ejemplar a devolver
if ($Total>0){
	$p=explode('|',$prestamo);
	$cod_usuario=$p[1];
	$inventario=$p[0];
	$fecha_p=$p[2];
	$hora_p=$p[3];
	$fecha_d=$p[4];
	$hora_d=$p[5];
	$tipo_usuario=$p[6];
	$tipo_objeto=$p[7];
	$referencia=$p[8];

	// se lee la política de préstamos
	include("loanobjects_read.php");
	// se lee el calendario
	include("calendario_read.php");
	// se lee la configuración local
	include("locales_read.php");

	// se incluye la rutina para calcular la fecha de devolucion


	//se determina la política a aplicar
	$politica=$politica[$tipo_objeto][$tipo_usuario];
	$p=explode('|',$politica);
	$lapso=$p[3];
	$unidad=$p[5];
//	echo "<p>Fecha de devolución programada: ".substr($fecha_d,6,2)."/".substr($fecha_d,4,2)."/".substr($fecha_d,0,4)." ".$hora_d."<br>";
	//Se calcula la fecha de devolución real en base a la política
	$df=explode('/',$config_date_format);
	$newdate = date($df[0]."/".$df[1]."/".$df[2]." h:i:s A");

//	echo "<p>Fecha de devolucion real= $newdate<p>$lapso";

	// se calcula la diferencia entre la fecha programada y la fecha real de devolución
	$newdate=date("m/d/Y h:i:s A");
	//echo "----$newdate---";
	$unidad="D";
	switch ($unidad){		case "H":
			$date1 = time();
			$tt=explode(' ',$hora_d);
			$date2 = mktime(0,0,0, substr($fecha_d,4,2),substr($fecha_d,6,2),substr($fecha_d,0,4));
			$newdate=date("m/d/Y h:i:s A");
	//	    $fecha_d= ."/".."/".." ".$hora_p;
	//		$atraso=dateDiff("/", $newdate, $fecha_d);
			break;
		case "D":
			$newdate=date("m/d/Y");
			$fecha_d=  substr($fecha_d,4,2)."/".substr($fecha_d,6,2)."/".substr($fecha_d,0,4);
			$atraso=dateDiff("/", $newdate, $fecha_d);
			break;	}

//	echo "<p>Atraso= $atraso $unidad<br>";
//	echo "<p>".$Mfn;

	$ValorCapturado="0001X\n";
	//echo "<xmp>$Mfn
	//".$ValorCapturado."</xmp>";

	$ValorCapturado=urlencode($ValorCapturado);
	$IsisScript=$xWxis."actualizar_registro.xis";
	$Formato="";
	$query = "&base=trans&cipar=$db_path"."par/trans.par&login=".$_SESSION["login"]."&Mfn=".$Mfn."&ValorCapturado=".$ValorCapturado;
	include("../common/wxis_llamar.php");
}
$cu="";
if (isset($arrHttp["usuario"]) and !isset($cod_usuario))
	$cu="&usuario=".$arrHttp["usuario"];
else
	$cu="&usuario=$cod_usuario";
if (isset($arrHttp["vienede"])){	header("Location: usuario_prestamos_presentar.php?encabezado=s$error$cu");}else{
	header("Location: devolver.php?encabezado=s$error$cu");
}
die;



?>