<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      carga_txt_guardar.php
 * @desc:     
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
$lang= $_SESSION["lang"];


include("../lang/admin.php");
include("../lang/soporte.php");


include("../common/get_post.php");

//foreach ($arrHttp as $var=>$value) 	echo "$var = $value<br>";
echo "<html>
<body>";
$file=$db_path.$arrHttp["base"]."/cnv/".$arrHttp["fn"].".cnv";

$fp = fopen($file,"w");
if (!$fp){
	echo "<center><br><br><h1><b><font color=red>admin/php/$file</font></b> ".$msgstr["revisarpermisos"]."</h1>";
	die;
}
$value=explode('!!',$arrHttp["tablacnv"]);
if (isset($arrHttp["delimited"]) and $arrHttp["delimited"]=="on")
	fwrite($fp,"[TABS]\n");
else
	fwrite($fp,$arrHttp["separador"]."\n");
foreach ($value as $tab){
	$tab=stripslashes($tab);
	$tab=str_replace("'","`",$tab);
	fwrite($fp,$tab."\n");
}
fclose($fp);
echo "<center><br><br><h3>$file ".$msgstr["okactualizado"]."</h3>";
echo "
<p><a href=carga_txt_cnv.php?base=".$arrHttp["base"]."&Opcion=cnv&tipo=txt&accion=".$arrHttp["accion"]." class=boton>&nbsp; &nbsp; ".$msgstr["continuar"]."&nbsp; &nbsp; </a>
</form>
</body>
</Html>"
?>
