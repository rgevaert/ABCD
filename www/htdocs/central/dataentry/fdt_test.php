<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      fdt_test.php
 * @desc:      FDT test
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
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../config.php");
$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");

include("../lang/admin.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value)  echo "$var=$value<br>";
$arrHttp["ValorCapturado"]=stripslashes($arrHttp["ValorCapturado"]) ;
$base=$arrHttp["base"];
$arrHttp["cipar"]="$base.par";
$t=explode("\n",$arrHttp["ValorCapturado"]);
$ix=-1;
global $vars;
foreach ($t as $value){

	$ix=$ix+1;
	$fdt[$t[1]]=$value;
	$vars[$ix]=$value;
	//echo "$value<br>";
}
$fmt_test="S";
$fondocelda="white";

include("../common/header.php");
echo "<script>
base='".$arrHttp["base"]."'
</script>
";
//include("../common/institutional_info.php");

echo "


<div class=\"helper\">
<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/fdt_test.html target=_blank>".$msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"])) echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/fdt_test.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: fdt_test.php";
echo "</font>
	</div>

<div class=\"middle form\">
			<div class=\"formContent\">";
include("../dataentry/dibujarhojaentrada.php");
include("../dataentry/ingresoadministrador.php");




?>