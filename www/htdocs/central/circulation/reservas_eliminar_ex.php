<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      reservas_eliminar_ex.php
 * @desc:      Delete reservations EX
 * @author:    Guilda Ascencio
 * @since:     20091203
 * @version:   1.0
 *
 * == BEGIN LICENSE ==
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Lesser General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Lesser General Public License for more details.
 *   
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *   
 * == END LICENSE ==
*/
session_start();
// Situación de un objeto
if (!isset($_SESSION["login"])){
	echo "Session expired" ;
//	die;
}
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
include("../config_loans.php");
$lang=$_SESSION["lang"];

include("../lang/prestamo.php");

include("../common/get_post.php");
foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
$Opcion_leida=$arrHttp["Opcion"];

include("leer_pft.php");
include("borrowers_configure_read.php");
$arrHttp["base"]="biblo/loans/";
include("databases_configure_read.php");
include("loanobjects_read.php");
include("calendario_read.php");
include("locales_read.php");
include("../common/header.php");
include("../common/institutional_info.php");
?>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["delreserve"]?>
	</div>
	<div class="actions">
		<a href="javascript:document.continuar.submit()" class="defaultButton backButton">
			<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
			<span><?php echo $msgstr["back"]?></strong></span>
		</a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/reservas_eliminar.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if ($_SESSION["permiso"]=="loanadm"){
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/reservas_eliminar.html target=_blank>".$msgstr["edhlp"]."</a>";
	echo "<font color=white>&nbsp; &nbsp; Script: reservas_eliminar_ex.php</font>\n";
}

echo "
	</div>
<div class=\"middle form\">
	<div class=\"formContent\">";

$Mfn=explode('|',$arrHttp["reservas"]);
foreach ($Mfn as $value) {	$IsisScript=$xWxis."eliminarregistro.xis";
   	$query = "&base=reserva&cipar=$db_path"."par/reserva.par&login=".$_SESSION["login"]."&Mfn=" . $value;
    include("../common/wxis_llamar.php");
    foreach($contenido as $val) echo "$val<br>";
    echo $msgstr["delete"]." $value<br>";}
if (isset($arrHttp["Expresion"]))
	$url="&Expresion=".$arrHtp["Expresion"];
else
	$url="&code=".$arrHttp["code"];
echo "<form name=continuar action=situacion_de_un_objeto_ex.php method=post>
<input type=hidden name=base value=".$arrHttp["base"].">
<input type=hidden name=Opcion value=".$arrHttp["Opcion"].">";
if (isset($arrHttp["Expresion"]))
	echo "<input type=hidden name=Expresion value=".$arrHttp["Expresion"].">\n";
if (isset($arrHttp["code"]))
	echo "<input type=hidden name=code value=".$arrHttp["code"].">\n";
echo"<input type=hidden name=desde value=1>
<P><center><input type=submit value=\"".$msgstr["ecobj"]."\">
</center>
</div></div>";
include("../common/footer.php");
echo "</body></html>";

?>