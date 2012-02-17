<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      sendto.php
 * @desc:      GENERATES THE STATISTICAL TABLES
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
include ("../lang/statistics.php");

//VALORES QUE VIENEN DE LA PÁGINA
include("../common/get_post.php");
//foreach ($arrHttp as $key => $value) echo "$key = $value <br>";
switch ($arrHttp["Opcion"]){
		case "D":
	    	$filename=$arrHttp["base"].".doc";
			header('Content-Type: application/msword; charset=windows-1252');
			header("Content-Disposition: attachment; filename=\"$filename\"");
    		header("Expires: 0");
    		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
    		header("Pragma: public");
			echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
			echo '   xmlns:w="urn:schemas-microsoft-com:office:word"' . "\n";
			echo '   xmlns="http://www.w3.org/TR/REC-html40">' . "\n";
			break;
		case "W":
			$filename=$arrHttp["base"].".xls";
			header('Content-Type: application/excel; charset=windows-1252');
			header("Content-Disposition: attachment; filename=\"$filename\"");
    		header("Expires: 0");
    		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
    		header("Pragma: public");
			echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
			echo '   xmlns:w="urn:schemas-microsoft-com:office:word"' . "\n";
			echo '   xmlns="http://www.w3.org/TR/REC-html40">' . "\n";
			break;
		default:
			echo "<html>
			<font face=arial size=2>";
	}
    echo $arrHttp["html"];
    echo "</html>";

	die;

?>
