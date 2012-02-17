<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      editpar_update.php
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
$arrHttp=array();

global $arrHttp;
include("../config.php");


include("../lang/dbadmin.php");

include("../common/get_post.php");
//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";
include("../common/header.php");

echo "<body>\n";
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
?>
<div class="sectionInfo">

			<div class="breadcrumb">
				<?php echo $msgstr["dbnpar"]." " .$msgstr["database"].": ".$arrHttp["base"]?>
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
<?php }
echo "
<div class=\"middle form\">
			<div class=\"formContent\">
";


echo "<font face=arial>";
echo "<xmp>".$arrHttp["par"]."</xmp>";
$fp=fopen($db_path."par/".$arrHttp["base"].".par","w");
fwrite($fp,$arrHttp["par"]);
fclose($fp);

echo "<p><h4>".$arrHttp["base"].".par ".$msgstr["updated"]."</H4>";
if (!isset($arrHttp["encabezado"]))echo "<a href=menu_modificardb.php?base=".$arrHttp["base"].">Menu</a><p>";
echo "
</div>
</div>
";
include("../common/footer.php");
echo "</body></html>\n";