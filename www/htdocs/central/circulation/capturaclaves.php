<?
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      captura_claves.php
 * @desc:      Get a keyword from an authority list
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
include ("../config.php");
$lang=$_SESSION["lang"];
include("../lang/admin.php");
// ==================================================================================================
// INICIO DEL PROGRAMA
// ==================================================================================================
include("../common/get_post.php");



//foreach ($arrHttp as $var => $value )	echo "$var = $value<br>";

	if (!isset($arrHttp["subc"])) $arrHttp["subc"]="";
	if (!isset($arrHttp["pref"])) $arrHttp["pref"]=$arrHttp["prefijo"];
	if (!isset($arrHttp["tagfst"])) $arrHttp["tagfst"]="";
	if (!isset($arrHttp["delimitador"])) $arrHttp["delimitador"]="";
	if (!isset($arrHttp["separa"])) $arrHttp["separa"]="";
	if (!isset($arrHttp["postings"])) $arrHttp["postings"]="ALL";
	if (!isset($arrHttp["Tag"]))$arrHttp["Tag"]="";
	if (!isset($arrHttp["Repetible"]))$arrHttp["Repetible"]="";
	$arrHttp["Formato"]=stripslashes($arrHttp["Formato"]);
	if (substr($arrHttp["Formato"],0,1)=="@"){		$Formato=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/".substr($arrHttp["Formato"],1);
		if (!file_exists($Formato)) $Formato=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/".substr($arrHttp["Formato"],1);
		$Formato="@".$Formato;	}else{		$Formato=$arrHttp["Formato"];	}
  	$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["base"].".par&Opcion=autoridades"."&tagfst=".substr($arrHttp["tagfst"],3)."&prefijo=".strtoupper($arrHttp["prefijo"])."&pref=".strtoupper($arrHttp["pref"])."&postings=".$arrHttp["postings"]."&formato_e=".$Formato;
  $IsisScript=$xWxis."ifp.xis";
   include("../common/wxis_llamar.php");
   //foreach ($contenido as $value) echo "$value<br>";
	$contenido = array_unique ($contenido);
	echo "<HTML>
	<head>
		<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">
		<META HTTP-EQUIV=\"Cache-Control\" CONTENT=\"no-cache\">
		<title>".$msgstr["listterm"]."</title>
		<script languaje=Javascript>\n";
  	echo "subC=\"".$arrHttp["subc"]."\"\n";
	echo "Repetible=\"".$arrHttp["Repetible"]."\"\n";
	echo "Tag=\"".$arrHttp["Tag"]."\"\n";
	echo "Delimitador=\"".$arrHttp["delimitador"]."\"\n";
	echo "Prefijo=\"".$arrHttp["prefijo"]."\"\n";
	echo "Separa=\"".$arrHttp["separa"]."\"\n";
	if (isset($arrHttp["indice"]))
		echo "Indice=\"".$arrHttp["indice"]."\"\n";
	else
		echo "Indice='N'\n";
?>

	function ObtenerTerminos(){
		Seleccion=""
		icuenta=0
		term=document.Lista.autoridades.options[document.Lista.autoridades.selectedIndex].value
        window.opener.Ctrl_activo.value=term
        window.opener.Ctrl_activo.focus()
        self.close()
	}

function Continuar(){
	i=document.Lista.autoridades.length-1
	a=document.Lista.autoridades[i].text
	AbrirIndice(a)
}

function IrA(ixj){
	a=document.Lista.ira.value
    AbrirIndice(a)
}

<?
echo "function AbrirIndice(Termino){
    	db='".$arrHttp["base"]."'
    	cipar='".$arrHttp["cipar"]."'
    	Separa='".$arrHttp["separa"]."'
		Tag='".$arrHttp["Tag"]."'
		Prefijo='".$arrHttp["pref"]."'+Termino
		Pref='".$arrHttp["pref"]."'
		Subc='". $arrHttp["subc"]."'
		lang='".$arrHttp["lang"]."'
		Postings='".$arrHttp["postings"]."'
		repetible='".$arrHttp["Repetible"]."'
		Formato='".$arrHttp["Formato"]."'
		url='capturaclaves.php?Opcion=autoridades&base='+db+'&cipar='+cipar+'&Tag='+Tag+'&pref='+Pref+'&prefijo='+Prefijo+'&separa='+Separa+'&subc='+Subc+'&postings='+Postings+'&lang='+lang+'&repetible='+repetible+'&Formato='+Formato
		self.location.href=url
	}

</script>\n";
?>
	</head>
	<body LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0">

	<font color=black size=1 face=arial><?php echo $msgstr["ayudaterminos"]?>
	<center>
	<table cellpadding=0 cellspacing=0 border=1 width=100%>
			<form method=post name=Lista onSubmit="javascript:return false">
	<td  bgcolor=#cccccc align=center><font size=1 face="arial"><?php for ($i=65;$i<91;$i++ ) echo "<a href=javascript:AbrirIndice('".chr($i)."')>".chr($i)."</a>&nbsp; "?></td>
	<tr>
	<td width=95% valign=top>
	<Select name=autoridades multiple size=20 style="width:460px" onchange=javascript:ObtenerTerminos()>
<?php

	foreach ($contenido as $linea){

		$f=explode('$$$',$linea);
		if (!isset($f[1])) $f[1]=$f[0];
		echo "<option value=\"";
		echo $f[1];
		echo "\">";
        if (substr($f[0],0,1)=="^") $f[0]=substr($f[0],2);
        echo $f[0];

	}

?>
	</select></td>

	</table>
	<table cellpadding=0 cellspacing=0 border=0 width=100% bgcolor=#cccccc>
		<td><a href=Javascript:Continuar() class=boton><font size=1><?php echo $msgstr["continuar"]?></a></td>
		<td class=menusec2><font size=1><?php echo $msgstr["avanzara"]?>: <input type=text name=ira size=15 value=""><a href=Javascript:IrA() onKeyPress="codes(event)" ><img src=../dataentry/img/ira.gif border=0 align=ABSBOTTOM></a></td>
	</table>
	</form></body></html>
