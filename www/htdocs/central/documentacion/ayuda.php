<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      ayuda.php
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
if (!isset($_SESSION["permiso"])) die;
include("../config.php");
include("../common/get_post.php");
//echo $arrHttp["help"];
$t=explode('/',$arrHttp["help"]);
$a=$db_path."documentacion/".$arrHttp["help"];
unset($fp);
$texto="";
if (file_exists($a)){
	$fp = file($a);
}else{
	$a=$db_path."documentacion/en/".$t[1];
	if (file_exists($a)) $fp=file($a);

}
if (isset($fp))
foreach ($fp as $value) {	$value=str_replace('php',$app_path,$value);
	echo "$value\n";}
?>