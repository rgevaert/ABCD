<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      sanctions_read.php
 * @desc:      Determine whether the suspension has expired
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
function PrepararFecha($FechaP){
global $locales,$config_date_format;;
//Se convierte la fecha al formato de fecha local
	$df=explode('/',$config_date_format);
	switch ($df[0]){
		case "DD":
			$dia=substr($FechaP,6,2);
			break;
		case "MM":
			$mes=substr($FechaP,6,2);
			break;
	}
	switch ($df[1]){
		case "DD":
			$dia=substr($FechaP,4,2);
			break;
		case "MM":
			$mes=substr($FechaP,4,2);
			break;
	}
	$year=substr($FechaP,0,4);
	return $dia."-".$mes."-".$year;}

function CalculaVencimiento ($FechaP){
global $locales;
//Se convierte la fecha a formato ISO (yyyymmaa) utilizando el formato de fecha local
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

// se leen las suspensiones
	$sanctions_output="" ;
	$formato_obj="v1'|',v10'|',v20'|',v30'|',v40'|',v50'|',v60'|',v100'|',f(mfn,1,0)/";
   	$query = "&Expresion=TR_S_".$arrHttp["usuario"]."&base=suspml&cipar=$db_path"."par/suspml.par&Pft=".$formato_obj;
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$susp=array();
	foreach ($contenido as $linea){
		$p=explode('|',$linea);
		if ($p[0]==0 or trim($p[0])==""){
			$dif= CalculaVencimiento ($p[6]);   // se verifica si la suspensión está vigente
			if ($dif>=0)
				$susp[]=$linea;
		}
	}
	$nsusp=0;
	if (count($susp)>0) {
		$sanctions_output.= "<br><strong>".$msgstr["suspensions"]."</strong>
		<table width=95% bgcolor=#cccccc> ";
		if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"])  or isset($_SESSION["permiso"]["CIRC_DELSUS"]))
			$sanctions_output.= "<th>&nbsp;</th>";
		$sanctions_output.= "<th>".$msgstr["date"]."</th><th>".$msgstr["cause"]."</th><th>".$msgstr["expire"]."</th><th>".$msgstr["comments"]."</th>";
		foreach ($susp as $linea) {
			$p=explode("|",$linea);
			$nsusp=$nsusp+1;
			$fecha1=PrepararFecha($p[3]);
			$fecha2=PrepararFecha($p[6]);
			$sanctions_output.=  "<tr>";
			if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"])  or isset($_SESSION["permiso"]["CIRC_DELSUS"])){				$sanctions_output.= "<td bgcolor=white><input type=checkbox name=susp value=".$p[8]."></td>";			}
			$sanctions_output.= "<td bgcolor=white nowrap align=center>".$fecha1."</td><td bgcolor=white nowrap align=center>".$p[4]."</td><td bgcolor=white nowrap align=center>".$fecha2."<td bgcolor=white>".$p[7]."</td>";

		}
		$sanctions_output.=  "</table>";
		if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"])  or isset($_SESSION["permiso"]["CIRC_DELSUS"])){			$sanctions_output.="<a href=javascript:DeleteSuspentions()>".$msgstr["update"].'</a><br>';		}

		$sanctions_output.="</dd>";
		$sanctions_output.= "\n<script>nSusp=".$nsusp."</script>";

	}

// se leen las Multas
	$formato_obj="v1'|',v10'|',v20'|',v30'|',v40'|',v50'|',v60'|',v100'|',f(mfn,1,0)/";
   	$query = "&Expresion=TR_M_".$arrHttp["usuario"]."&base=suspml&cipar=$db_path"."par/suspml.par&Pft=".$formato_obj;
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$multa=array();
	foreach ($contenido as $linea){
		$multa[]=$linea;
	}
	$nmulta=0;
	if (count($multa)>0) {
		$sanctions_output.=  "<br><strong>".$msgstr["fine"]."</strong><table width=95% bgcolor=#cccccc>";
		if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"])  or isset($_SESSION["permiso"]["CIRC_DELFINE"]))
			$sanctions_output.="<th>".$msgstr["payed"]."</th>";
		$sanctions_output.="<th>".$msgstr["date"]."</th><th>".$msgstr["concept"]."</th><th>".$msgstr["amount"]."</th><th>".$msgstr["comments"]."</th>";
		foreach ($multa as $linea) {
			$p=explode("|",$linea);
			if ($p[1]==0 or trim($p[1])==""){
				$nmulta=$nmulta+1;
				$fecha1=PrepararFecha($p[3]);
				$sanctions_output.= "<tr>";
				if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"])  or isset($_SESSION["permiso"]["CIRC_DELFINE"]))
					$sanctions_output.="<td bgcolor=white><input type=checkbox name=pay value=".$p[8]."></td>";
				$sanctions_output.="<td bgcolor=white nowrap align=center>".$fecha1."</td><td bgcolor=white nowrap align=center>".$p[4]."</td><td bgcolor=white nowrap align=center>".$p[5]."<td bgcolor=white>".$p[7]."</td>";
            }
		}
		$sanctions_output.= "</table>";
		if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"])  or isset($_SESSION["permiso"]["CIRC_DELFINE"]))
        	$sanctions_output.="<a href=javascript:PagarMultas()>".$msgstr["update"]."</a><p>";
    	$sanctions_output.= "\n<script>nMultas=".$nmulta."</script>";
	}
	if ($sanctions_output!="" and isset($arrHttp["inventory"])) $ec_output.="<font color=red><strong>".$msgstr["pending_sanctions"]."</strong></font>";
?>