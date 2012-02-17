<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      sortkey_update.php
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
include("../config.php");
include("../lang/dbadmin.php");

include("../lang/soporte.php");
include("../common/header.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value)  echo "$var=$value<br>";
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
	$encabezado="&encabezado=s";
}else{
	$encabezado="";
}
?>
<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["sortkey"].$arrHttp["base"] ?>
	</div>

	<div class="actions">
<?php
	if ($encabezado!="") echo "<a href=javascript:self.close() class=\"defaultButton backButton\">";
?>
<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
<span><strong><?php echo $msgstr["back"]?></strong></span>
</a>
			</div>
			<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/sortkey.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/sortkey.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script:sortkey_update.php </font>";
?>
	</div>
<div class="middle form">
			<div class="formContent">
<?php
$fp=fopen($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/sort.tab","w");
$accents=explode("\n",$arrHttp["ValorCapturado"]);
echo "\n<script>
sort=new Array()
window.opener.document.forma1.sort.length=0
";
$ix=0;
foreach ($accents as $val){
	$val=trim($val);
	$ix=$ix+1;
	if($val!=""){		$a=explode('|',$val);
		echo "window.opener.document.forma1.sort.options[$ix]= new Option('".$a[0]."','".$a[1]."')\n";
		fwrite($fp,$a[0]."|".$a[1]."\n");	}}
fclose($fp);
echo "</script>\n";
echo "sort.tab ".$msgstr["updated"]."<p>";
?>
</div>
</div>
<?php include("../common/footer.php")?>
</body>
</html>

