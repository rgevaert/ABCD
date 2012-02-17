<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      busqueda_guardar.php
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
include("../config.php");
include("../common/get_post.php");
include("../lang/admin.php");
include("../lang/dbadmin.php");
//foreach($arrHttp as $var=>$value) echo "$var=$value<br>";
$arrHttp["Expresion"]=str_replace('\"','',$arrHttp["Expresion"]);
$archivo=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/search_expr.tab";
$fp=fopen($archivo,"a");
$res=fwrite($fp,trim($arrHttp["Descripcion"])."|".trim($arrHttp["Expresion"])."\n\n");
fclose($fp);
echo "<html><body>
<title>".$msgstr["savesearch"]."</title>
<font face=verdana size=2><font color=darkred><h4>".$msgstr["saved"]."</h4>

<a href=javascript:self.close()>".$msgstr["cerrar"]."</a>

</body></html>";

?>
