<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      fst_update.php
 * @desc:      Update an fst file
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

include("../lang/dbadmin.php");
include("../common/get_post.php");

if (isset($arrHttp["encabezado"]))
	$encabezado="&encabezado=s";
else
	$encabezado="";
$arrHttp["ValorCapturado"]= htmlspecialchars_decode ($arrHttp["ValorCapturado"]);
$arrHttp["ValorCapturado"]= stripslashes ($arrHttp["ValorCapturado"]);
if ($arrHttp["Opcion"]=="new"){
	$_SESSION["FST"]=$arrHttp["ValorCapturado"];
	header("Location:pft.php?Opcion=new&base=".$arrHttp["base"].$encabezado);
	die;
}
$t=explode("\n",$arrHttp["ValorCapturado"]);
$fp=fopen($db_path.$arrHttp["base"]."/data/".$arrHttp["base"].".fst","w");

foreach ($t as $value){
	fwrite($fp,stripslashes($value)."\n");
	//echo "$value<br>";
}

include("../common/header.php");
echo "<body>";
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
	$encabezado="&encabezado=s";
}
?>
<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["fst"].": ".$arrHttp["base"]?>
	</div>

	<div class="actions">
<?php if ($arrHttp["Opcion"]=="new"){
	echo "<a href=\"../common/inicio.php?reinicio=s\" class=\"defaultButton cancelButton\">";
}else{
	echo "<a href=\"menu_modificardb.php?base=".$arrHttp["base"]."$encabezado\" class=\"defaultButton backButton\">";
}
?>
<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
<span><strong><?php echo $msgstr["back"]?></strong></span>
</a>
			</div>
			<div class="spacer">&#160;</div>
</div>
<div class="helper">
<?php echo "<font color=white>&nbsp; &nbsp; Script: fst_update.php" ?></font>
	</div>
<div class="middle form">
			<div class="formContent">
<center><h4>
<?php echo $msgstr["fstupdated"]?></h4>

		</TD>
</table>
</center>
</div></div>
<?php include("../common/footer.php");?>
</body>
</html>
