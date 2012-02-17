<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      typeofitems_update.php
 * @desc:      Update the type of items
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
$lang=$_SESSION["lang"];
include("../lang/prestamo.php");

include("../common/get_post.php");
//foreach ($arrHttp as $var => $value) 	echo "$var = $value<br>";
$arrHttp["ValorCapturado"]= htmlspecialchars_decode ($arrHttp["ValorCapturado"]);
$arrHttp["ValorCapturado"]= stripslashes ($arrHttp["ValorCapturado"]);
$t=explode("\n",$arrHttp["ValorCapturado"]);
$fp=fopen($db_path."circulation/def/".$_SESSION["lang"]."/items.tab","w");
foreach ($t as $value){	fwrite($fp,stripslashes($value)."\n");
}
include("../common/header.php");

echo "<body>";
include("../common/institutional_info.php");
?>
<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["typeofitems"]?>
	</div>

	<div class="actions">
		<a href="configure_menu.php" class="defaultButton backButton">
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<span><strong><?php echo $msgstr["back"]?></strong></span>
		</a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<?php if ($_SESSION["permiso"]=="admloan") echo "<font color=white>&nbsp; &nbsp; Script: typeofitems_update.php" ?></font>
	</div>
<div class="middle form">
			<div class="formContent">
<center><h4>
<?php echo $msgstr["typeofitems"]." ".$msgstr["saved"]?>!!!!</h4>

		</td>
</table>
</center>
</div></div>
<?php include("../common/footer.php");?>
</body>
</html>
