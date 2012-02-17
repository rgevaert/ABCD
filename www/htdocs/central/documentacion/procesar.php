<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      procesar.php
 * @desc:      PROCESS HELP FILE FOR THE FIELDS OF THE DATABASE IN THE DEFINITION OF THE DATABASE MODULE
 *             Write in the help file after finish editing.
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
include("../config.php");
include("../common/get_post.php");
include("../lang/dbadmin.php");
$salida=$arrHttp["FCK"];
$salida=stripslashes($salida);
$archivo=$db_path."documentacion/".$arrHttp["archivo"];
$fp = fopen($archivo, "w", 0); #open for writing
  fputs($fp, $salida); #write all of $data to our opened file
  fclose($fp); #close the file

echo "<html><body>

	<a href=edit.php?archivo=" . $arrHttp["archivo"].">".$msgstr["edit"]."</a><br>";
	echo $salida;

?>
<p>
<input type="submit" value="<?php echo $msgstr["close"]?>" onclick=javascript:self.close()>
