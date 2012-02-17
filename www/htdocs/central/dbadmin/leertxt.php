<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      leertxt.php
 * @desc:      Read a plain text file and allows you to modify
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
include ("../lang/admin.php");
include ("../lang/dbadmin.php");


// ==================================================================================================
// INICIO DEL PROGRAMA
// ==================================================================================================

//

include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value)  echo "$var=$value<br>";
echo "<font face=arial size=1>Script: leertxt.php";

$fp="";
if (isset($arrHttp["archivo"])) {
	$folder="pfts";
	$arch=explode("|",$arrHttp["archivo"]);  //[0]=NAME [1]=TYPE OF OUTPUT: CT=COLS.TABLE  CD=COLS.DELIMITED
	$arrHttp["archivo"]=$arch[0];
	echo "<script>type='".$arch[1]."'</script>\n";
	$type="";
	if (isset($arrHttp["tipof"])) $type="|".$arrHttp["tipof"];
	if (isset($arrHttp["Opcion"])and $arrHttp["Opcion"]=="guardar") {
		echo "<font face=verdana size=2>".$msgstr["displaypft"].": <b>$a</b></font><br>";
    	$fp=fopen($db_path.$arrHttp["base"]."/www/".$arrHttp["archivo"].".pft","w",0);
		fputs($fp, stripslashes( $arrHttp["formato"])); #write all of $data to our opened file
  		fclose($fp); #close the file
		echo " ".$msgstr["saved"]."!</center>";
		$arrHttp["pft"]="S";
	}
	unset($fp);
	$file=$db_path.$arrHttp["base"]."/$folder/".$_SESSION["lang"]."/" .$arrHttp["archivo"].".pft";
	if (file_exists($file)){
        $fp=file($file);
	}else{
		$file=$db_path.$arrHttp["base"]."/$folder/".$lang_db."/" .$arrHttp["archivo"].".pft";
		$fp=file($file);
	}

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
	window.opener.document.forma1.pft.value=document.forma1.formato.value
	window.opener.document.forma1.headings.value=document.forma1.headings.value
	window.opener.EsconderVentana('pftedit')
	window.opener.toggleLayer('pftedit')
//CHECK THE OPENER FORM WITH THE TYPE OF OUTPUT
	switch (type){
		case "CT":
			window.opener.document.forma1.tipof[2].checked =true
			break
		case "CD":
			window.opener.document.forma1.tipof[3].checked =true
			break
	}
	self.close()
}
</script>

<body><font color=black>
<form name=forma1 action=leertxt.php method=post>
<p><?php echo $msgstr["edit"]." ".$msgstr["pft"].": ".$file?>
<?php if (isset($arrHttp["pft"])) {
//    echo   " Al terminar de clic sobre </font> <b>enviar</b> <font color=black> para almacenar los cambios</span>  <br>";
  }else{
  	echo "<br><font color=red>".$msgstr["pftwd1"]." <a href=javascript:Enviar()><strong>".$msgstr["send"]."</strong></a> ".$msgstr["pftwd2"]."</span>";
  }
?>

<input type=hidden name=Opcion>
<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
<?php echo "
		&nbsp;<textarea name=formato rows=15 cols=100% style=\"width:100%\" nowrap>";

	foreach ($fp as $linea){
  		echo $linea;
	}

?>
</textarea>

<?php
// READ HEADINGS (IF ANY)
reset($fp);
$file=str_replace($arrHttp["archivo"].".pft",$arrHttp["archivo"]."_h.txt",$file);
$head="";
if (file_exists($file)){
	$fp=file($file);
	foreach ($fp as $lin){
		if (!empty($lin)) $head.=$lin;
	}
}
echo $msgstr["r_heading"].":<br> <textarea name=headings rows=10 cols=30  nowrap>$head</textarea>";
?>
<br>
<a href=javascript:Enviar()><h2><?php echo $msgstr["send"]?></h2></a>
</form>
<br>
<?php echo $msgstr["fdt"]?>&nbsp;<a href=fdt_leer.php?base=<?php echo $arrHttp["base"]?> target=_blank><?php echo $msgstr["openwindow"]?></a><br>
<iframe height=60% width=60%  scrolling=yes src=fdt_leer.php?base=<?php echo $arrHttp["base"]?>></iframe>
</body>
</html>
