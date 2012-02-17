<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      actualizararchivotxt.php
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

if (!isset($arrHttp["archivo"])) die;
$arrHttp["txt"]=stripslashes($arrHttp["txt"]);
$arrHttp["txt"]=str_replace("\"",'"',$arrHttp["txt"]);
$archivo=$arrHttp["archivo"];
$fp=fopen($db_path.$archivo,"w");
fputs($fp,$arrHttp["txt"]);
fclose($fp);
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
<dd><table border=0>
	<tr>
		<TD>
			<p><h4>
<?php $ar=explode('/',$arrHttp["archivo"]);
$ix=count($ar)-1;
$file=$ar[$ix];
echo $file." ".$msgstr["updated"]?></h4>
<?php if (!isset($arrHttp["encabezado"]))
		echo "
			<script>if (top.frames.length<1)
			        document.writeln(\"<a href=javascript:self.close()>".$msgstr['close']."</a>\")
			</script>
         ";
?>
		</TD>
</table>
</div></div>
<?php include("../common/footer.php")?>

</body>
</html>
