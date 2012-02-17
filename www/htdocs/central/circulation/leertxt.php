<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      leertxt.php
 * @desc:      BEGINNING OF THE PROGRAM
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

include ("../config.php");
include("../lang/admin.php");
include("../lang/dbadmin.php");

// ==================================================================================================
// INICIO DEL PROGRAMA
// ==================================================================================================

//


include("../common/get_post.php");
echo "<font face=arial size=1>Script: leertxt.php";

//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";
$fp="";
if (isset($arrHttp["archivo"]) and $arrHttp["archivo"]!="*.pft" ) {

	if (strpos($arrHttp["archivo"],".pft")!=0 or strpos($arrHttp["archivo"],".eml")!=0) {
    	$fs="";
	}else{
		$fs=".pft";
	}
	$a=$arrHttp["archivo"].$fs;

	if (isset($arrHttp["Opcion"])and $arrHttp["Opcion"]=="guardar") {
		echo "<font face=verdana size=2>".$msgstr["displaypft"].": <b>$a</b></font><br>";
    	$fp=fopen($db_path.$arrHttp["base"]."/www/".$arrHttp["archivo"].$fs,"w",0);
		fputs($fp, stripslashes( $arrHttp["formato"])); #write all of $data to our opened file
  		fclose($fp); #close the file
		echo "<center>".$msgstr["saved"]."!</center>";
		$arrHttp["pft"]="S";
	}
	$file= $arrHttp["archivo"].$fs;
	$fp = fopen ($file, "r");
	if (!$fp){
  		echo $msgstr["misfile"]." ". $file;
		die;
	}
}else{

}
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<title>ABCD. <?php echo $msgstr["editar"]." ".$msgstr["displaypft"]?></title>
<meta http-equiv="content-language" content="en">
<meta name="robots" content="all">
<meta name="robots" content="index">
<meta name="robots" content="follow">
<meta http-equiv="expires" content="Mon, 01 Jan 1990 00:00:00 GMT">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta name="revisit-after" content="1 day">
<meta http-equiv="content-script-type" content="text/javascript">
<script languaje=javascript>
function Enviar(){
	window.opener.document.forma1.pftedit.value=document.forma1.formato.value
	window.opener.toggleLayer('pftedit')
	self.close()
}
</script>

<body><font color=black>

<form name=forma1 action=leertxt.php method=post>
<?php echo "<font face=verdana size=1>".$msgstr["displaypft"].": <b>$a</b></font>
<p>".$msgstr["edit"]." ".$msgstr["pft"]?>
<?php if (isset($arrHttp["pft"])) {
//    echo   " Al terminar de clic sobre </font> <b>enviar</b> <font color=black> para almacenar los cambios</span>  <br>";
  }else{
  	echo "<br><span><font color=red>".$msgstr["pftwd1"]." <a href=javascript:Enviar()><img src=../dataentry/img/copy_to_folder.gif border=0></a> ".$msgstr["pftwd2"]."</font></span>"
  }
?>

<input type=hidden name=Opcion>
<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
<?php echo "
		&nbsp;<textarea name=formato rows=20 cols=100% nowrap>";

  	$fp = file($a);
	foreach ($fp as $linea){
  		echo $linea;
	}

?>
</textarea>
<br>
<a href=javascript:Enviar()><img src=../dataentry/img/copy_to_folder.gif  xheight=20 border=0 align=absmiddle alt='send'><?php echo $msgstr["send"]?></a>
</form>
<br>
<?php echo $msgstr["fdt"]?>&nbsp;<a href=fdt_leer.php?base=<?php echo $arrHttp["base"]?> target=_blank><img src=../dataentry/img/preview.gif alt="Full window" border=0 align=top><?php echo $msgstr["openwindow"]?></a><br>
<iframe height=60% width=60%  scrolling=yes src=fdt_leer.php?base=<?php echo $arrHttp["base"]?>></iframe>
</body>
</html>