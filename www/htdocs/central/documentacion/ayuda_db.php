<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      ayuda_db.php
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
include ("../config.php");
$lang=$_SESSION["lang"];

include("../lang/soporte.php");

include("../lang/lang.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";

if (!isset($_SESSION["permiso"])) {	echo "session expired";
	die;}
unset($fp);
$archivo_s="";
$archivo=$db_path.$arrHttp["base"]."/ayudas/".$_SESSION["lang"]."/".$arrHttp["campo"];

if (file_exists($archivo)){	$fp=file($archivo);
	$archivo_s=$arrHttp["base"]."/ayudas/".$_SESSION["lang"]."/".$arrHttp["campo"];
}else{	$archivo=$db_path.$arrHttp["base"]."/ayudas/".$lang_db."/".$arrHttp["campo"];

	if (file_exists($archivo)){		$archivo_s=$arrHttp["base"]."/ayudas/".$lang_db."/".$arrHttp["campo"];
		$fp=file($archivo);
	}
}
echo "<h4>$archivo_s</h4>";
if (isset($fp)){	foreach ($fp as $value) {
		$value=str_replace('/php',$app_path.'/',$value);
		echo "$value\n";
	}}
?>
</body>
</html>