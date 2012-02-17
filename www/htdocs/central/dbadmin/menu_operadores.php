<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      menu_operadores.php
 * @desc:      Update the list of operators
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

global $arrHttp;
include("../common/get_post.php");

if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
$lang=$_SESSION["lang"];


include("../lang/admin.php");
include("../lang/soporte.php");
?>
<html>
<head>


<script>
	function LeerOperador(){
		Mfn=document.menu.oper.options[document.menu.oper.selectedIndex].value
		if (Mfn=="") return

		msgwin=window.open("../dataentry/fmt.php?Opcion=leer&base=acces&cipar=acces.par&Mfn="+Mfn+"&ver=N&ventana=S","acces","width=750,height=600,menubar=0,scrollbars=1")
		msgwin.focus()
	}
	function NuevoOperador(){

		msgwin=window.open("../dataentry/fmt.php?Opcion=leer&base=acces&cipar=acces.par&Mfn=New&ver=N&ventana=S","acces","width=750,height=600,menubar=0,scrollbars=1")
		msgwin.focus()
	}
</script>
<body>
<?
	echo "
	<form name=menu>
	";
	$query = "&base=acces&cipar=$db_path"."par/acces.par&from=1&Opcion=leertodo";
	$IsisScript=$xWxis."auditoria.xis";
	$xWxis."auditoria.xis "
	$ic=-1;
	foreach($contenido as $linea){
	echo "$linea<br>";
		$linea=trim($linea);
		if ($linea!=""){
			if (trim(substr($linea,0,4))=="mfn="){
				$ic++;
				$ix=strpos($linea,'<');
				if ($ix===false){
				}else{
					$linea=substr($linea,0,$ix);
				}
				$valortag[$ic][0]=substr($linea,4);

  			}else{
	    		$pos=strpos($linea, " ");
    			if (is_integer($pos)) {
     				$tag=trim(substr($linea,0,$pos));
////El formato ALL envía un <br> al final de cada línea y hay que quitarselo
//
    				$linea=rtrim(substr($linea, $pos+2,strlen($linea)-($pos+2)-5));
					if ($tag==1002){
	 					$maxmfn=$linea;
					}else{
     					if (!isset($valortag[$ic][$tag])){
      						$valortag[$ic][$tag]=$linea;
     					}else{
     	 					$valortag[$ic][$tag]=$valortag[$ic][$tag]."\n".$linea;
     					}
    				}
   				}
  			}
 		}
	}
	echo "<center><p><br><br><font face=verdana><h4>".$msgstr["actualizar"]." ".$msgstr["dboper"]."</h4><Table border=1 cellpadding=5>
		<tr><td class=menusec2>
	".$msgstr["actualizar"]." ".$msgstr["dboper"].": <select name=oper onChange=LeerOperador()><option value=''></option>";
	foreach ($valortag as $value) echo "<option value=".$value[0].">".$value[10]."\n";
	echo "</select></td>
	<tr><td class=menusec2><a href=javascript:NuevoOperador()>".$msgstr["nueva"]." ".$msgstr["dboper"]."</a></td>
	</table>";

echo "</form>";

?>
