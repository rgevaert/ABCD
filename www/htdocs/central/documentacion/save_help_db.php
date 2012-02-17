<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      save_help_db.php
 * @desc:      SAVE HELP FILE FOR THE FIELDS OF THE DATABASE IN THE DEFINITION OF THE DATABASE MODULE
 *             After finish editing, save the help file in the help directory of the database.
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

$arrHttp=Array();
include("../config.php");
include("../lang/dbadmin.php");
foreach ($HTTP_GET_VARS as $var => $value) {
	if (!empty($value))  $arrHttp[$var]=$value;
	}
if (count($arrHttp)==0){
	foreach ($HTTP_POST_VARS as $var => $value) {
		$arrHttp[$var]=$value;
	}
}
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
$salida=$arrHttp["FCK"];
$salida=stripslashes($salida);
$archivo=$arrHttp["archivo"];
$ix=strripos($archivo,'/');
$ar=substr($archivo,0,$ix);
if (!file_exists($ar)) {
	echo "<h4>"."$ar:  ".$msgstr["folderne"]."</h4>";
	die;
}
$fp = fopen($archivo, "w", 0); #open for writing
  fputs($fp, $salida); #write all of $data to our opened file
  fclose($fp); #close the file

	echo $salida;

?>