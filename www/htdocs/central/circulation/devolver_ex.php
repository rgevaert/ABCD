<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      devolver_ex.php
 * @desc:      Returns a loan
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
$lang=$_SESSION["lang"];
include("../lang/admin.php");
include("../lang/prestamo.php");
include("../common/get_post.php");
date_default_timezone_set('UTC');
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
//die;
$archivo="";
$pr_loan="";
$pr_return="";
$pr_fine="";
$pr_statment="";
$pr_solvency="";
if (file_exists($db_path."trans/pfts/".$_SESSION["lang"]."/receipts.lst")){
	$archivo=$db_path."trans/pfts/".$_SESSION["lang"]."/receipts.lst";
}else{
	if (file_exists($db_path."trans/pfts/".$lang_db."/receipts.lst"))
		$archivo=$db_path."trans/pfts/".$lang_db."/receipts.lst";
}
if ($archivo!=""){
	$fp=file($archivo);
	foreach ($fp as $value){
		$value=trim($value);
		$v=explode('|',$value);
		switch($v[0]){
			case "pr_loan":
				$pr_loan=$v[1];
				break;
			case "pr_return":
				$pr_return=$v[1];
				break;
			case "pr_fine":
				$pr_fine=$v[1];
				break;
			case "pr_statment":
				$pr_statment=$v[1];
				break;
			case "pr_solvency":
				$pr_solvency=$v[1];
				break;
		}
	}
}

//función para calcular la diferencia de tiempo entre dos fecha
function compareDate ($FechaP){	$dia=substr($FechaP,6,2);
	$mes=substr($FechaP,4,2);
	$year=substr($FechaP,0,4);
	$exp_date=$year."-".$mes."-".$dia;
	$todays_date = date("Y-m-d");
	$today = strtotime($todays_date);
	$expiration_date = strtotime($exp_date);
	$diff=$expiration_date-$today;
	$diff=$diff/(60 * 60 * 24);
    return $diff;
}//end Compare Date

//Calculo de la sanción por atraso
include("sanctions_inc.php");

//Se ubica el ejemplar prestado en la base de datos de transacciones
$inventario="TR_P_".trim($arrHttp["searchExpr"]);
if (!isset($arrHttp["base"])) $arrHttp["base"]="trans";
$Formato="v10'|$'v20'|$'v30'|$'v35'|$'v40'|$'v45'|$'v70'|$'v80'|$'v100,'|$',v40,'|$'v400,'|$'v500/";
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
if ($Total==0){
	$error="&error=".$msgstr["item"]." ".$msgstr["noloan"];
}
// se extrae la información del ejemplar a devolver
if ($Total>0){
	$p=explode('|$',$prestamo);
	$cod_usuario=$p[1];
	$arrHttp["usuario"]=$cod_usuario;
	$inventario=$p[0];
	$fecha_p=$p[2];
	$hora_p=$p[3];
	$fecha_d=$p[9];   //fecha de devolución en formato ISO
	$hora_d=$p[5];
	$tipo_usuario=$p[6];
	$tipo_objeto=$p[7];
	$referencia=$p[8];
	$ppres=$p[10];
	// se lee la política de préstamos
	include("loanobjects_read.php");
	// se lee el calendario
	include("calendario_read.php");
	// se lee la configuración local
	include("locales_read.php");

	//se determina la política a aplicar
	if ($ppres==""){		$ppres=$politica[$tipo_objeto][$tipo_usuario];   //read the policy
		if (empty($ppres)) {
			$ppres=$politica[strtoupper($tipo_objeto)][trim(strtoupper($tipo_usuario))];
		}
		if (trim($ppres)==""){
			$ppres=$politica["0"]["0"];
		}
	}
	$p=explode('|',$ppres);
	$lapso=$p[3];
	$unidad=$p[5];
	$u_multa= $p[7];      //unidades de multa
	$u_multa_r= $p[8];    //unidades de multa si el libro está reservado
	$u_suspension=$p[9];  //unidades de suspensión
	$u_suspension=$p[10];  //unidades de suspensión si el libro está reservado
    $devolucion=date("Ymd");
	$ValorCapturado="0001X\n0500$devolucion\n";
	$ValorCapturado.="0130^a".$_SESSION["login"]."^b".date("Ymd H:i:s");
	$ValorCapturado=urlencode($ValorCapturado);
	$IsisScript=$xWxis."actualizar_registro.xis";
	$Formato="";
	$recibo="";
	if ($pr_return!=""){
		if (file_exists($db_path."trans/pfts/".$_SESSION["lang"]."/".$pr_return.".pft")){
			$Formato=$db_path."trans/pfts/".$_SESSION["lang"]."/".$pr_return;
		}else{
			if (file_exists($db_path."trans/pfts/".$lang_db."/".$pr_return.".pft")){
				$Formato=$db_path."trans/pfts/".$lang_db."/".$pr_return;
			}
		}
		if ($Formato!="") $Formato="&Formato=$Formato";
	}
	$query = "&base=trans&cipar=$db_path"."par/trans.par&login=".$_SESSION["login"]."&Mfn=".$Mfn."&ValorCapturado=".$ValorCapturado.$Formato;
	include("../common/wxis_llamar.php");
	$recibo=implode(" ",$contenido);
}

// si está atrasado se procesan las multas y suspensiones
$atraso=compareDate ($fecha_d);
if ($politica==""){	$error="&error=".$msgstr["nopolicy"]." $tipo_usuario / $tipo_objeto";}else{
	$error="";
	if ($atraso<0){
		$atraso=abs($atraso);
		Sanciones($fecha_d,$atraso,$arrHttp["usuario"],$inventario,$ppres);
	}
}
$cu="";
if (isset($arrHttp["usuario"]))
	$cu="&usuario=".$arrHttp["usuario"];
else
	$cu="&usuario=$cod_usuario";
if (isset($arrHttp["vienede"])){	header("Location: usuario_prestamos_presentar.php?encabezado=s$error$cu"."&recibo=$recibo");}else{
	header("Location: devolver.php?encabezado=s$error$cu"."&recibo=$recibo");
}
die;



?>