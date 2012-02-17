<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      editararchivotxt.php
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

include("../lang/dbadmin.php");;
include("../common/get_post.php");
$archivo=str_replace("\\","/",$arrHttp["archivo"]);
include("../common/header.php");
?>
<link rel=STYLESHEET type=text/css href=../css/styles.css>
<body>
<?php
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");

?>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo "<h5>"." " .$msgstr["database"].": ".$arrHttp["base"]."</h5>"?>
	</div>
	<div class="actions">
<?php echo "<a href=\"menu_modificardb.php?base=".$arrHttp["base"]."&encabezado=s\" class=\"defaultButton backButton\">";
?>
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<span><strong><?php echo $msgstr["back"]?></strong></span>
		</a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<?php }?>
<div class="middle form">
	<div class="formContent">
<br><br>
<?php
$ar=explode('/',$arrHttp["archivo"]);
$xi=count($ar)-1;
$file=$ar[$xi];
$fp=file($db_path.$archivo);
echo "
<form method=post action=actualizararchivotxt.php>
<input type=hidden name=archivo value='".$arrHttp["archivo"]."'>
<font face=arial><h4>".$file." &nbsp; <a href=javascript:self.close()>".$msgstr["close"]."</a>
</h4>
<textarea name=txt rows=20 cols=100 style=\"font-family:courier\";>";
foreach ($fp as $value) echo $value;
echo "</textarea>
<br><input type=submit value=".$msgstr["update"].">
</form>
</div></div>";
include("../common/footer.php");
echo "</body></html>";
?>