<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      fst_leer.php
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
$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");
include("../common/get_post.php");

?>
<link rel="STYLESHEET" type="text/css" href="../css/styles.css">

<script>
function AbrirVentana(){
	msgwin=window.open("","Fdt","width=400,height=400,resizable,scrollbars=yes")
	msgwin.focus()
}
</script>
<body>
<?php echo "<font face=arial size=1 color=white>&nbsp; &nbsp;Script: fst_leer.php<p>";
?>
 <b><?php echo $msgstr["fst"]?> &nbsp; (<a href=fdt_leer.php?base=<?php echo $arrHttp["base"]?> target=Fdt onclick=AbrirVentana() style="color:#cddef6"><?php echo $msgstr["displayfdt"]?></a>)</font><br>
		<table bgcolor=#EEEEEE>
			<td>ID</td><td><?php echo $msgstr["fstit"]?></td><td><?php echo $msgstr["formate"]?></td>
<?php
$fst=$db_path.$arrHttp["base"]."/data/".$arrHttp["base"].".fst";
if (file_exists($fst)){
	$fp=file($fst);
	foreach ($fp as $value){
		if (!empty($value)) {
			$ix=strpos($value," ");
			$id=trim(substr($value,0,$ix));
			$value=trim(substr($value,$ix));
			$ix=strpos($value," ");
			$ti=trim(substr($value,0,$ix));
			$format=stripslashes(trim(substr($value,$ix+1)));
			echo "<tr><td bgcolor=white valign=top>$id</td><td bgcolor=white valign=top>$ti</td><td bgcolor=white><font face=\"courier new\">$format</td>";
		}
	}
}else{
	echo "<tr><td bgcolor=white valign=top>&nbsp</td><td bgcolor=white valign=top>&nbsp</td><td bgcolor=white>&nbsp;</td>";

}
?>
</table>

<p>
</body>
</html>
