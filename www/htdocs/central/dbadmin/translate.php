<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      translate.php
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

$lang=$_SESSION["lang"];
include("../common/get_post.php");
//foreach ($arrHttp as $var => $value) 	echo "$var = $value<br>";
$encabezado="";
if (isset($arrHttp["encabezado"])) $encabezado="&encabezado=s";
include("../common/header.php");
$rotulo="";
switch ($arrHttp["componente"]){	case "soporte.tab":
		$rotulo=$msgstr["maintenance"];
		break;
	case "dbadmin.tab":
		$rotulo=$msgstr["dbadmin"];
		break;
	case "admin.tab":
		$rotulo=$msgstr["catalogacion"];
		break;
	case "statistics.tab":
		$rotulo=$msgstr["statistics"];
		break;
}
echo "
<script>
function Enviar(){	document.forma1.submit()}
</script>
<body>\n";
if (isset($arrHttp["encabezado"]))include("../common/institutional_info.php");

?>
<div class="sectionInfo">

			<div class="breadcrumb">
				<?php echo "<h5>".$msgstr["traducir"].": ".$rotulo."</h5>"?>
			</div>

			<div class="actions">
				<a href="javascript:Enviar()" class="defaultButton saveButton">
					<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
					<span><strong><?php echo $msgstr["m_guardar"]?></strong></span></a>
<?php if (isset($arrHttp["encabezado"])){?>
 				<a href="menu_traducir.php?encabezado=s" class="defaultButton backButton">
					<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
					<span><strong><?php echo $msgstr["back"]?></strong></span>
				</a>

<?php }?>
			</div>
			<div class="spacer">&#160;</div>
</div>
<?php
echo "
<div class=\"middle form\">
			<div class=\"formContent\">
";
echo "<font size=1> &nbsp; &nbsp; Script: translate.php</font><br>";
//error_reporting (0);
$componente=$arrHttp["componente"];

if ($componente=="" or $lang==""){
	echo $msgstr["sellang"]."<p><a href=javascript:history.back()>".$msgstr["regresar"]."</a>";
	die;
}

// Se lee el primer idioma de la tabla que es el que gobierna los mensajes en otros idiomas
echo "<h4><b>$lang/$componente</b></h4>";

$a=$db_path."/lang/".$_SESSION["lang"]."/$componente";
if (file_exists($a)) {
	$fp=file($a);
	foreach($fp as $var=>$value){		if (!empty($value)) {
			$m=explode('=',$value);
			$m[0]=trim($m[0]);
			$msg[$m[0]]=trim($m[1]);
		}
	}
}

$a=$db_path."/lang/00/$componente";
if (file_exists($a)) {
	$fp=file($a);
	foreach($fp as $var=>$value){
		if (!empty($value)) {
			$m=explode('=',$value);
			$m[0]=trim($m[0]);
			if (!isset($msg[$m[0]]))
				$msg[$m[0]]=trim($m[1]);
		}
	}
}


echo "<table width=100%>
<form method=post action=translate_update.php name=forma1>
<input type=hidden name=lang value=$lang>
<input type=hidden name=componente value=$componente>
\n";
if (isset($arrHttp["encabezado"]))   echo "<input type=hidden name=encabezado value=s>";
$ixmsg=-1;
foreach ($msg as $key=>$value){
	$ixmsg=$ixmsg+1;
	$v=explode('=',$value);
	$nomb=$key;
	echo "<tr><td width=20%>$ixmsg)";
	echo " $value <font color=darkred>".$key."</font></td><td ><input type=text size=100 name=msg_$key value=\"$value\">\n";
	echo "</td>";

}

echo "</table>";
?>
<br>
<br>
</form>
</div></div>
<?php echo include("../common/footer.php")?>
</body>
</html>
