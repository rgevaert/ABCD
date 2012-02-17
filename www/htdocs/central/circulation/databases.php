<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      databases.php
 * @desc:      Select the bibliographic database to be configured
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
include("../common/get_post.php");
$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");

include("../lang/prestamo.php");


//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";
include("../common/header.php");
?>
<script>
function Continuar(){
	ix=document.forma1.base.selectedIndex
	if (ix<1){		alert("<?php echo $msgstr["seldb"]?>")
		return	}    document.forma1.submit()}
</script>
<?php
$encabezado="";
include("../common/institutional_info.php");
echo "
		<div class=\"sectionInfo\">
			<div class=\"breadcrumb\">".
				$msgstr["sourcedb"]."
			</div>
			<div class=\"actions\">\n";

				echo "<a href=\"configure_menu.php?encabezado=s\" class=\"defaultButton backButton\">
					<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
					<span><strong>". $msgstr["back"]."</strong></span>
				</a>
			</div>
			<div class=\"spacer\">&#160;</div>
		</div>
		<div class=\"helper\">
	<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/circulation/loans_databases.html target=_blank>".$msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
 	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/circulation/loans_databases.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: databases.php </font>";

echo " </div>
		<div class=\"middle form\">
			<div class=\"formContent\">";
echo "<p><h4>".$msgstr["seldbdoc"]."</h4>";
echo "<p><form name=forma1 action=databases_configure.php >\n";
echo "<table><td valign=top>";
echo $msgstr["database"];
echo ": <select name=base>
<option value=''>\n";
$fp=file($db_path."bases.dat");
$bases_p=array();
foreach ($fp as $value){
	$value=trim($value);
	if ($value!=""){		$b=explode('|',$value);
		$archivo="";
		if (file_exists($db_path.$b[0]."/loans/".$_SESSION["lang"]."/loans_conf.tab"))			$archivo=$db_path.$b[0]."/loans/".$_SESSION["lang"]."/loans_conf.tab";
		else
		    if (file_exists($db_path.$b[0]."/loans/".$lang_db."/loans_conf.tab"))
		    	$archivo=$db_path.$b[0]."/loans/".$lang_db."/loans_conf.tab";
		if ($archivo!="") $bases_p[]=$b[1]." (".$b[0].")";
		echo "<option value=".$b[0].">".$b[1]."\n";
	}}
echo "</select></td>";
echo "<td valign=top>".$msgstr["alreadysel"].": ";
foreach ($bases_p as $value) echo $value."<br>";
echo "</td>";
echo "<tr><td valign=top align=right><h2><a href=javascript:Continuar()>".$msgstr["continue"]."</a></h2></td></table></form></div></div>";
include("../common/footer.php");
echo "</body></html>" ;

?>