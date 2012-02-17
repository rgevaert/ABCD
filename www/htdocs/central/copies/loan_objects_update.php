<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      loan_objects_update.php
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
include("../lang/acquisitions.php");

include("../lang/admin.php");
include("../common/get_post.php");

//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
$xtl="";
$xnr="";
$arrHttp["base"]="loanobjects";
include("../dataentry/plantilladeingreso.php");
include("../dataentry/actualizarregistro.php");

//GET THE RECORD FROM LOANOBJECTS USING THE CONTROL NUMBER TO CHECK IF ALREADY LOADED (IN CASE OF REFRESH)
$Formato=$db_path."loanobjects/pfts/".$_SESSION["lang"]."/loanobjects_add.pft" ;
if (!file_exists($Formato)) $Formato=$db_path."loanobjects/pfts/".$lang_db."/loanobjects_add.pft"  ;
$Expresion="CN_".$arrHttp["db"]."_".$arrHttp["cn"];
$query = "&base=loanobjects&cipar=$db_path"."par/loanobjects.par"."&from=1&Formato=@$Formato&Opcion=buscar&Expresion=".urlencode($Expresion);
$IsisScript=$xWxis."imprime.xis";
include("../common/wxis_llamar.php");
//foreach ($contenido as $value) echo "$value<br>";
$c=implode("",$contenido);
$dup=array();
$cipar="loanobjects.par";
$base="loanobjects";
$variables["tag959"]="";
if (empty($c)) {
	$arrHttp["Mfn"]="New";
	$arrHttp["Opcion"]="crear";
	$variables["tag1"]=$arrHttp["cn"];
	$variables["tag10"]=$arrHttp["db"];
}else{
	$arrHttp["Opcion"]="addocc";
	$ixc=0;
	foreach ($contenido as $value){
		$value=trim($value);
		if ($ixc==0){
			$ixc=1;
			$v=explode('$$$',$value);
			$arrHttp["Mfn"]=$v[0];
			$value=$v[1];
		}
		$inv=explode('|',$value);
		$dup[$inv[2]]="Y";
	}
}
$vc=explode("\n",$arrHttp["ValorCapturado"]);
$arrHttp["ValorCapturado"]="";

foreach ($vc as $value){
	$value=trim($value);
	if ($value!=""){
		$ix=strpos($value,'^i');
		$ix=$ix+2;
		$ix1=strpos($value,'^',$ix);
		$inv=substr($value,$ix,$ix1-$ix);
		if (!isset($dup[$inv])) {
			if ($variables["tag959"]=="")
				$variables["tag959"]=$value;
			else
				$variables["tag959"].="\n".$value;
		};
	}
} Print_page();

if ($variables["tag959"]!="") ActualizarRegistro();

//GET THE RECORD FROM LOANOBJECTS USING THE CONTROL NUMBER
$Formato=$db_path."loanobjects/pfts/".$_SESSION["lang"]."/loanobjects_add.pft" ;
if (!file_exists($Formato)) $Formato=$db_path."loanobjects/pfts/".$lang_db."/loanobjects_add.pft"  ;
$Expresion="CN_".$arrHttp["db"]."_".$arrHttp["cn"];
$query = "&base=loanobjects&cipar=$db_path"."par/loanobjects.par"."&from=1&Formato=@$Formato&Opcion=buscar&Expresion=".urlencode($Expresion);
$IsisScript=$xWxis."imprime.xis";
include("../common/wxis_llamar.php");
$old_c=$contenido;
$ix=0;
foreach ($old_c as $value){
    if ($ix==0){
    	$t=explode('$$$',$value);
    	$value=$t[1];
    	$t=explode('|',$value);
    	$ix=1;
    	$cn=$t[0];
    	$db=$t[1];
    	break;
    }
}


echo "<strong><a href=../dataentry/show.php?base=$db&Expresion=CN_".$arrHttp["cn"]." target=blank>".$msgstr["cn"].": $cn ($db)</a></strong><br>";
echo "<table class=\"statTable\" cellspacing=5 cellpadding=5 width=100%>
		<tr>";
$archivo=$db_path."copies/pfts/".$_SESSION["lang"]."/copies_add.tit";
if (!file_exists($archivo))
	$archivo=$db_path."copies/pfts/".$lang_db."/copies_add.tit";
$fp=file($archivo);
foreach ($fp as $linea) {
	$l=explode('|',$linea);
	foreach ($l as $lx)
		if (!empty($lx)) echo "<th>$lx</th>";
	break;
}
foreach ($old_c as $value){
	$t=explode("|",$value);
	echo "<tr><td align=center>".$t[2]."</td><td>".$t[3]."</td><td>".$t[4]."</td><td align=center>".$t[5]."</td><td align=center>".$t[6]."</td>";
	echo "<td align=center>".$t[7]."</td>";
}
echo "</table>
</div>
</div>
";
include("../common/footer.php");
//=====================================

function Print_page(){
Global $arrHttp,$msgstr,$cn,$db;
	$encabezado="";
	include("../common/header.php");
	echo "<body>\n";
	if (isset($arrHttp["encabezado"])){
		include("../common/institutional_info.php");
		$encabezado="&encabezado=s";
	}
?>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["loanobjects"]?>
	</div>
	<div class="actions">
		<a href=copies_edit_browse.php?Expresion=CN_<?php echo $arrHttp["db"]."_". $arrHttp["cn"]?> class="defaultButton backButton">
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<span><strong><?php echo $msgstr["back"]?></strong></span>
        </a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/copies/loan_objects_update.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/copies/loan_objects.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: loan_objects_update.php</font>\n";
echo "
	</div>
		<div class=\"middle list\">
";
}
?>
