<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      translate_update.php
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
if (!isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]) and !isset($_SESSION["permiso"]["CENTRAL_ALL"])){
	header("Location: ../common/error_page.php") ;
}
include("../config.php");

include("../lang/dbadmin.php");

include("../lang/admin.php");
include("../common/get_post.php");

//foreach ($arrHttp as $var => $value) 	echo "$var = $value<br>";
$encabezado="";
if (isset($arrHttp["encabezado"])) $encabezado="&encabezado=s";
include("../common/header.php");
$rotulo="";
switch ($arrHttp["componente"]){
	case "soporte.php":
		$rotulo=$msgstr["maintenance"];
		break;
	case "dbadmin.php":
		$rotulo=$msgstr["dbadmin"];
		break;
	case "admin.php":
		$rotulo=$msgstr["catalogacion"];
		break;
}
echo "<body>\n";
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
?>
<div class="sectionInfo">

			<div class="breadcrumb">
				<?php echo "<h5>".$msgstr["traducir"].": ".$rotulo."</h5>"?>
			</div>

			<div class="actions">
 				<a href="menu_traducir.php?encabezado=s" class="defaultButton backButton">
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
echo "<font size=1> &nbsp; &nbsp; Script: translate_update.php</font><br>";
//error_reporting (0);
$componente=$arrHttp["componente"];
$lang=$_SESSION["lang"];

$componente=$arrHttp["componente"];
echo "<h2>$lang/$componente</h2><p>";
$mensajes="";

foreach ($arrHttp as $var=>$value) {
	if (substr($var,0,4)=="msg_"){
		$var=substr($var,4);
		$mensajes.=$var."=".$value."\n";
	}
}
//echo "<xmp>$mensajes</xmp>";
$fp=fopen($db_path."lang/$lang/$componente","w");
if (!$fp) {
	echo $msgstr["nopudoseractualizado"];
	die;
}
$res=fwrite($fp,stripslashes($mensajes)."\n");
fclose($fp);
echo "<h3>".$msgstr["actualizados"]."</h3> ";
echo "<p></div></div>";
include("../common/footer.php");
echo "
</body>
</html>";
?>
