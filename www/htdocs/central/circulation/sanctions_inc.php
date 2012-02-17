<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      sanctions_inc.php
 * @desc:      Calculate suspenctions and fines
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
require_once("fecha_de_devolucion.php");

function Sanciones($fecha_d,$atraso,$cod_usuario,$inventario,$politica){
global $Wxis,$xWxis,$db_path,$locales,$arrHttp,$msgstr;
	$p=explode('|',$politica);
	$multa=trim($p[7]);
	$multa_reserva=trim($p[8]);
	$dias=trim($p[9]);
	$dias_reserva=trim($p[10]);
	$sancion="";
	$ValorCapturado="";
	if ($multa!=0 and $multa!="") $sancion="M";
	if ($dias!=0 and $dias!="") $sancion="S";
	if ($sancion=="") return;
	switch ($sancion){
		case "M":
			$tipor="M";                                     		//v1
			$status="0";	                                  		//v10
			//cod_usuario                                     		//v20
  			$concepto=$msgstr["fine"]." (".$inventario.")";    		//v40
     		$fecha=date("Ymd");              						//v30
       		$monto=$atraso*$p[7]*$locales["fine"];                		//v50
       		$ValorCapturado="0001$tipor\n0010$status\n0020$cod_usuario\n0030$fecha\n0040$concepto\n0050$monto\n";
			break;
		case "S":
			$tipor="S";                      						//v1
			$status="0";	                 						//v10
			//cod_usuario                    						//v20
  			$concepto="Suspensión por atraso (".$inventario.")";    //v40
     		$fecha=date("Ymd");              						//v30
     		$lapso=$atraso*$p[9];
     		$fecha_v=FechaDevolucion($lapso,"D","");
     		$fecha_v=substr($fecha_v,0,8);
     		$ValorCapturado="0001$tipor\n0010$status\n0020$cod_usuario\n0030$fecha\n0040$concepto\n0060$fecha_v\n";
			break;
		default:
			return;
			break;
	}
//	print "<xmp>$ValorCapturado</xmp>";
	if ($ValorCapturado!=""){
		$ValorCapturado=urlencode($ValorCapturado);
		$IsisScript=$xWxis."actualizar.xis";
   		$query = "&base=suspml&cipar=$db_path"."par/suspml.par&login=".$_SESSION["login"]."&Mfn=New&Opcion=crear&ValorCapturado=".$ValorCapturado;
        include("../common/wxis_llamar.php");
//        foreach ($contenido as $value) echo "$value<br>";
	}
}
?>