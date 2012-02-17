<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      databases_configure.php
 * @desc:      Ask for the pfts which configure an bibliographic database
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
include("../lang/prestamo.php");

include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";

$prefix_in="";
$prefix_cn="";
$pft_totalitems="";
$pft_in="";
$pft_cn="";
$pft_dispobj="";
$pft_storobj="";
$pft_disploan="";
$pft_typeofr="";

function LeerPft($pft_name){
global $arrHttp,$db_path,$lang_db;
	$pft="";
	$archivo=$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/$pft_name";
	if (!file_exists($archivo)) $archivo=$db_path.$arrHttp["base"]."/loans/".$lang_db."/$pft_name";	$fp=file_exists($archivo);
	if ($fp){
		$fp=file($archivo);
		foreach ($fp as $value){
			$pft.=$value;
		}

	}
    return $pft;}


$archivo=$db_path.$arrHttp["base"]."/loans/".$_SESSION["lang"]."/loans_conf.tab";
if (!file_exists($archivo)) $archivo=$db_path.$arrHttp["base"]."/loans/".$lang_db."/loans_conf.tab";
$fp=file_exists($archivo);
if ($fp){	$fp=file($archivo);
	foreach ($fp as $value){		$ix=strpos($value," ");
		$tag=trim(substr($value,0,$ix));
		switch($tag){			case "IN": $prefix_in=substr($value,$ix);
				break;
			case "NC":
				$prefix_nc=trim(substr($value,$ix));
				break;		}	}}
$pft_totalitems=LeerPft("loans_totalitems.pft");
$pft_in=LeerPft("loans_inventorynumber.pft");
$pft_nc=LeerPft("loans_cn.pft");
$pft_dispobj=LeerPft("loans_display.pft");
$pft_storobj=LeerPft("loans_store.pft");
$pft_loandisp=LeerPft("loans_show.pft");
$pft_typeofr=LeerPft("loans_typeofobject.pft");

include("../common/header.php");
?>
<script>
function Guardar(){
	ix=document.forma1.base.selectedIndex
	if (ix<1){		alert("<?php echo $msgstr["seldb"]?>")
		return	}
	document.forma1.action="databases_configure_update.php"
	document.forma1.target="_self";
    document.forma1.submit()}

function Test(){
	if (document.forma1.Mfn.value==""){		alert("<?php echo $msgstr["test_mfn_err"]?>")
		return	}
    msgwin_t=window.open("","TestPft","")
    msgwin_t.focus()	document.forma1.action="databases_configure_test.php"
	document.forma1.target="TestPft";
	document.forma1.submit()}
</script>
<?php
$encabezado="";
include("../common/institutional_info.php");
echo "
		<div class=\"sectionInfo\">
			<div class=\"breadcrumb\">".
				$msgstr["sourcedb"].". ".$msgstr["loan"].". ".$msgstr["configure"]."
			</div>
			<div class=\"actions\">\n";

				echo "<a href=\"databases.php?encabezado=s\" class=\"defaultButton backButton\">
					<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
					<span><strong>". $msgstr["back"]."</strong></span>
				</a>
				<a href=javascript:Guardar() class=\"defaultButton saveButton\">
					<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
					<span><strong>".$msgstr["update"]."</strong></span>
				</a>
			</div>
			<div class=\"spacer\">&#160;</div>
		</div>
		<div class=\"helper\">
	<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/circulation/loans_databases_configure.html target=_blank>".$msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/circulation/loans_databases_configure.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: databases_configure.php </font>";

echo"</div>
	<div class=\"middle form\">
		<div class=\"formContent\">";
echo "<h5>".$msgstr["database"].": ".$arrHttp["base"]." &nbsp; &nbsp;[<a href=../dbadmin/fst_leer.php?base=".$arrHttp["base"]." target=_blank>Open FST</a>]"."  &nbsp; &nbsp;[<a href=../dbadmin/fdt_leer.php?base=".$arrHttp["base"]." target=_blank>Open FDT</a>]"."</h5>";
echo "<form name=forma1 action=databases_configure_save.php method=post>\n";
echo "<input type=hidden name=base value=".$arrHttp["base"].">\n";
if (file_exists($db_path."loans.dat")){
	echo "
	<table>
	<tr><td valign=top>1. ".$msgstr["pft_obj"]."<br>(loans_display.pft)</td><td><textarea rows=5 cols=80 name=bibref>".$pft_dispobj."</textarea></td>
	<tr><td valign=top>2. ".$msgstr["pft_store"]."<br>(loans_store.pft)</td><td><textarea rows=5 cols=80 name=bibstore>".$pft_storobj."</textarea></td>
	<tr><td valign=top>3. ".$msgstr["pft_loandisp"]."<br>(loans_show.pft)</td><td><textarea rows=5 cols=80 name=loandisp>".$pft_loandisp."</textarea></td>
	<tr><td valign=top>4. ".$msgstr["pft_ninv"]."<br>(loans_inventorynumber.pft)</td><td><textarea rows=2 cols=80 name=num_i>".$pft_in."</textarea></td>
	<tr><td valign=top>5. ".$msgstr["invkey"]."<br>(loans_conf.tab)</td><td valign=top><input type=text name=invkey value='".$prefix_in."'></td>
	<tr><td valign=top>6. ".$msgstr["nckey"]."<br>(loans_conf.tab)</td><td valign=top><input type=text name=nckey value='".$prefix_nc."'></td>
	<tr><td valign=top>7. ".$msgstr["pft_nclas"]."<br>(loans_cn.pft)</td><td><textarea rows=2 cols=80 name=num_c>".$pft_nc."</textarea></td>
	<tr><td valign=top>8. ".$msgstr["pft_nejem"]."<br>(loans_totalitems.pft)</td><td valign=top><textarea rows=2 cols=80 name=totalej>".$pft_totalitems."</textarea></td>
	<tr><td valign=top>9. ".$msgstr["pft_typeofr"]."<br>(loans_typeofobject.pft)</td><td><textarea rows=5 cols=80 name=tm>".$pft_typeofr."</textarea></td>
	</table>
	";
}else{	echo "
	<table>
	<tr><td valign=top>1. ".$msgstr["pft_obj"]."</td><td><textarea rows=5 cols=80 name=bibref>".$pft_dispobj."</textarea></td>
	<tr><td valign=top>2. ".$msgstr["pft_store"]."</td><td><textarea rows=5 cols=80 name=bibstore>".$pft_storobj."</textarea></td>
	<tr><td valign=top>3. ".$msgstr["pft_loandisp"]."</td><td><textarea rows=5 cols=80 name=loandisp>".$pft_loandisp."</textarea></td>
	</table>
	";}
echo "&nbsp; &nbsp;<a href=javascript:Test()>".$msgstr["test"]."</a>
Mfn: <input type=text name=Mfn size=10>&nbsp; &nbsp;
</form></div></div>";
include("../common/footer.php");
echo "</body></html>" ;

?>