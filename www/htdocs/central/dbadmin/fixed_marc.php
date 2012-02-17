<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      fixed_marc.php
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
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");


if (!isset($_SESSION["permiso"]["CENTRAL_ALL"]) and !isset($_SESSION["permiso"]["CENTRAL_MODIFYDEF"])){
	echo "<br><br><h2>".$msgstr["menu_noau"]."</h2>";
	die;
}
include("../common/get_post.php");
include("../common/header.php");
?>
<link rel="STYLESHEET" type="text/css" href="../styles/basic.css">
<script  src="../dataentry/js/lr_trim.js"></script>

<body>
<?php if (isset($arrHttp["encabezado"])){
    	include("../common/institutional_info.php");
}
echo "<div class=\"sectionInfo\"><div class=\"breadcrumb\">".$msgstr["typeofrecords"].": ". $arrHttp["base"]."</div><div class=\"actions\">\n";
if (isset($arrHttp["encabezado"]))
	$encabezado="&encabezado=s";
else
	$encabezado="";
echo "<a href=menu_modificardb.php?base=". $arrHttp["base"].$encabezado." class=\"defaultButton cancelButton\">
	<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
	<span><strong>". $msgstr["cancel"]."</strong></span>
	</a>
	</div>
	<div class=\"spacer\">&#160;</div>
	</div>";
?>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/typeofrecs_marc.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/typeofrecs_marc.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: fixed_marc.php";
?>
</font>
	</div>
<div class="middle form">
			<div class="formContent"> <xcenter>


<?php
//READ THE TYPE OF RECORDS USING THE PICKLIST ASSOCIATED TO THE FIELD 3006 OF THE LEADER
unset($ldr_06);
if (file_exists($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/ldr_06.tab"))
	$ldr_06=file($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/ldr_06.tab");
else
    if (file_exists($db_path.$arrHttp["base"]."/def/".$lang_db."/ldr_06.tab"))
		$ldr_06=file($db_path.$arrHttp["base"]."/def/".$lang_db."/ldr_06.tab");

if (!$ldr_06){
	echo "missing file ".$ldr_06;
	die;
}
echo "<div style=position:relative;margin-left:100px>";
echo "<strong>File: ldr_06.fdt (<a href=picklist_edit.php?base=".$arrHttp["base"]."&picklist=ldr_06.tab&desde=fixed_marc$encabezado>".$msgstr["edit"]."</a>)</strong><p>" ;
foreach ($ldr_06 as $value){
	$value=trim($value);
	$t=explode('|',$value);
	echo $t[0]." - ".$t[1].": <a href=\"fdt.php?base=". $arrHttp["base"]."$encabezado&Fixed_field=y&fdt_name=".$t[2]."\">".$t[2]."</a><br>";
}
echo "</div><p>";
echo "</div></div>";
include("../common/footer.php");?>
</body>
</html>
