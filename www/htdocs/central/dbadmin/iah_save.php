<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      iah_save.php
 * @desc:      Save the changes made
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

include("../lang/iah_conf.php");
include("../lang/dbadmin.php");
include("../common/get_post.php");

if (strpos($arrHttp["base"],"|")===false){

}   else{
		$ix=strpos($arrHttp["base"],'^b');
		$arrHttp["base"]=substr($arrHttp["base"],2,$ix-2);
}
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
include("../common/header.php");

unset($fp);

?>
</head>
<body>
<A NAME=INICIO>
<?php
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
	$encabezado="&encabezado=s";
}else{
	$encabezado="";
}
?>
<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["iah-conf"].": ".$arrHttp["base"].".def"?>
	</div>
	<div class="actions">
<?php
if (isset($arrHttp["encabezado"]))
	echo "<a href=\"menu_modificardb.php?base=".$arrHttp["base"]."$encabezado\" class=\"defaultButton backButton\">";
?>
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<span><strong><?php echo $msgstr["back"]?></strong></span></a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
	<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/iah_edit_db.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
 	echo "\<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/iah_edit_db.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "&nbsp; &nbsp;<font color=white>&nbsp; &nbsp; Script: iah_save.php";
?>
</font>
	</div>
<div class="middle form">
	<div class="formContent">


<?php
$arrHttp["ValorCapturado"]= stripslashes($arrHttp["ValorCapturado"]);
echo "<xmp>".$arrHttp["ValorCapturado"]."</xmp>";
$fp=fopen($db_path."par/".strtoupper($arrHttp["base"]).".def","w");
$res=fwrite($fp,$arrHttp["ValorCapturado"]);
fclose($fp);
echo "<h2>".$msgstr["saved"]."</h2>";
?>
</form>
</div></div>
<?php include("../common/footer.php");?>
</body>
</html>
