<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      picklist_save.php
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
include("../common/header.php");
if (isset($arrHttp["encabezado"])){
	$encabezado="&encabezado=s";
	include("../common/institutional_info.php");
}else{
	$encabezado="";
}
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
//die;


echo "
	<div class=\"sectionInfo\">
			<div class=\"breadcrumb\">".
				$msgstr["picklist"]. ": " . $arrHttp["base"]." - ".$arrHttp["picklist"]."
			</div>
			<div class=\"actions\">

	";
if (isset($arrHttp["desde"])){
	echo "<a href=\"fixed_marc.php?base=". $arrHttp["base"]."$encabezado\" class=\"defaultButton backButton\">";
}else{
	echo "<a href=\"picklist.php?base=". $arrHttp["base"]."&row=".$arrHttp["row"]."&picklist=".$arrHttp["picklist"]."\" class=\"defaultButton backButton\">";
}
echo "
					<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
					<span><strong>". $msgstr["back"]."</strong></span>
				</a>";
echo "			</div>
			<div class=\"spacer\">&#160;</div>
	</div>

<div class=\"helper\">
<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/picklist_tab.html target=_blank>".$msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/picklist_tab.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: picklist_save.php" ;

echo "</font>
	</div>
 <div class=\"middle form\">
			<div class=\"formContent\">";
if (strpos($arrHttp["picklist"],"%path_database%")===false){
	$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["picklist"];
}else{
	$archivo=str_replace("%path_database%",$db_path,$arrHttp["picklist"]);
}
$fp=false;
$fp=fopen($archivo,"w");
if (!$fp){
	echo $archivo.": ".$msgstr["nopudoseractualizado"];
	die;
}
fwrite($fp,$arrHttp["ValorCapturado"]);
fclose($fp);
echo "<h3>".$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["picklist"]." ".$msgstr["updated"]."</h3>";
if (!isset($arrHttp["desde"])){
?>
<script>
	row=<?php echo $arrHttp["row"]."\n"?>
	name="<?php echo $arrHttp["picklist"]?>"
	window.opener.valor=name
	window.opener.Asignar()
</script>
<?php
}
include("../common/footer.php");?>
