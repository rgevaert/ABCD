<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      ec_include.php
 * @desc:      Display the user statment
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
// se determina si el pr�stamo est� vencido
function compareDate ($FechaP){
global $locales,$config_date_format;
//Se convierte la fecha a formato ISO (yyyymmaa) utilizando el formato de fecha local
	$f_date=explode('/',$config_date_format);
	switch ($f_date[0]){
		case "DD":
			$dia=substr($FechaP,0,2);
			break;
		case "MM":
			$mes=substr($FechaP,0,2);
			break;
	}
	switch ($f_date[1]){
		case "DD":
			$dia=substr($FechaP,3,2);
			break;
		case "MM":
			$mes=substr($FechaP,3,2);
			break;
	}
	$year=substr($FechaP,6,4);
	$exp_date=$year."-".$mes."-".$dia;

	$ixTime=strpos($FechaP," ");
	if ($ixTime>0) {		$exp_date.=substr($FechaP,$ixTime);
		$todays_date = date("Y-m-d h:i A");	}else{		$todays_date = date("Y-m-d");	}
	$today = strtotime($todays_date);
	$expiration_date = strtotime($exp_date);
	$diff=$expiration_date-$today;
	return $diff;

}//end Compare Date

// se presenta la  informaci�n del usuario
	$formato_us=$db_path."users/loans/".$_SESSION["lang"]."/loans_usdisp.pft";
    if (!file_exists($formato_us)) $formato_us=$db_path."users/loans/".$lang_db."/loans_usdisp.pft";
   	$query = "&Expresion=".trim($uskey).$arrHttp["usuario"]."&base=users&cipar=$db_path/par/users.par&Formato=".$formato_us;
	$contenido="";
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	foreach ($contenido as $linea){
		$ec_output.= $linea."\n";
	}
	$ec_output.="\n<script>nMultas=0;nSusp=0</script>\n" ;
	include("sanctions_read.php");
	$ec_output.=$sanctions_output;
//	foreach ($prestamo as $linea) echo "$linea<br>";
    $permitir_prestamo="S";
// se leen los prestamos pendientes
	$formato_obj=$db_path."trans/pfts/".$_SESSION["lang"]."/loans_display.pft";
    if (!file_exists($formato_obj)) $formato_obj=$db_path."trans/pfts/".$lang_db."/loans_display.pft";
   	$query = "&Expresion=TRU_P_".$arrHttp["usuario"]."&base=trans&cipar=$db_path"."par/trans.par&Formato=".$formato_obj;
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$prestamos=array();
	foreach ($contenido as $linea){
		$prestamos[]=$linea;
	}
	$nv=0;   //n�mero de pr�stamos vencidos
	$np=0;   //Total libros en poder del usuario
	if (count($prestamos)>0) {		$ec_output.= "<strong>".$msgstr["loans"]."</strong>
		<table width=100% bgcolor=#cccccc>
		<td> </td><th>".$msgstr["inventory"]."</th><th>".$msgstr["volume"]."</th><th>".$msgstr["tome"]."</th><th>".$msgstr["control_n"]."</th><th>".$msgstr["signature"]."</th><th>".$msgstr["reference"]."</th><th>".$msgstr["typeofitems"]."</th><th>".$msgstr["loandate"]."</th><th>".$msgstr["devdate"]."</th><th>".$msgstr["overdue"]."</th><th>".$msgstr["renewed"]."</th>\n";

		foreach ($prestamos as $linea) {			if (!empty($linea)) {
				$p=explode("^",$linea);
				//SI LA POLITICA SE GRAB� CON EL REGISTRO DE LA TRANSACCION, ENTONCE SE APLICA ESA
				// DE OTRA FORMA SE UBICA LA POL�TICA LE�DA DE LA TABLA
				if (isset($p[17]) and trim($p[17])!=""){					$politica_este=explode('|',$p[17]);
				}else{					$politica_este=explode('|',$politica[$p[3]][$p[6]]);				}
                $lapso_p=$politica_este[5];
                #if ($lapso_p=="") $lapso_p="D";
				$np=$np+1;
				$dif= compareDate ($p[5]);
				$fuente="";
				$mora="0";
				if ($dif<0) {
					if ($politica_este[12]!="Y") $nv=$nv+1;
					if ($lapso_p=="D"){						$mora=floor(abs($dif)/(60*60*24));    //cuenta de pr�stamos vencidos					}else{
						$fulldays=floor(abs($dif)/(60*60*24));
						$fullhours=floor((abs($dif)-($fulldays*60*60*24))/(60*60));
						$fullminutes=floor((abs($dif)-($fulldays*60*60*24)-($fullhours*60*60))/60);
						$mora=$fulldays*24+$fullhours;
						//echo "<br>** $fulldays, $fullhours , $fullminutes";
					}
				    $fuente="<font color=red>";
				}
				$ec_output.= "<tr><td  bgcolor=white valign=top>";
				$ec_output.="<input type=radio name=chkPr value=$mora  id='".$p[0]."'>";
				$ec_output.= "<input type=hidden name=politica value=".$politica[$p[3]][$p[6]]."> \n";
				$ec_output.="</td>

					<td bgcolor=white nowrap align=center valign=top>".$p[0]."</td>".
					"<td bgcolor=white nowrap align=center valign=top>".$p[14]."</td>".
					"<td bgcolor=white nowrap align=center valign=top>".$p[15]."</td>".
					"</td><td bgcolor=white nowrap align=center valign=top>".$p[12]."(".$p[13].")</td><td bgcolor=white nowrap align=center valign=top>".$p[1]."<td bgcolor=white valign=top>".$p[2]."</td><td bgcolor=white align=center valign=top>". $p[3]. "</td><td bgcolor=white nowrap align=center valign=top>".$p[4]."</td><td nowrap bgcolor=white align=center valign=top>$fuente".$p[5]."</td><td align=center bgcolor=white valign=top>".$mora." ".$lapso_p."</td><td align=center bgcolor=white valign=top>". $p[11]."</td></tr>\n";
        	}
		}
		$ec_output.= "</table></dd>";
        $ec_output.= "<script>
		np=$np
		nv=$nv
		</script>\n";
	}


?>