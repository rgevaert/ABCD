<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      carga_iso.php
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
$lang=$_SESSION["lang"];

include("../lang/admin.php");
include("../lang/dbadmin.php");
include("../lang/soporte.php");

include("../common/get_post.php");

if (!isset($arrHttp["accion"])) $arrHttp["accion"]="";
//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";

if ($arrHttp["accion"]!="eliminar"){
	include("../common/header.php");

?>
<script src=js/lr_trim.js></script>
<body>



<script language=javascript>
function Seleccionar(iso){
	borrarBd=false
	if (document.explora.borrar.checked){
		if (confirm("<?php echo $msgstr["mnt_ibd"]?> ??")==true){
			borrarBd=true
		}else{
			return
		}
	}
	fullinvBd=false
	if (document.explora.fullinv.checked){
		fullinvBd=true
	}
	url= "carga_iso.php?base=<?php echo $arrHttp["base"]."&cipar=".$arrHttp["base"].".par&tipo=".$arrHttp["tipo"]?>&cnv="+iso+"&accion=importar"
	url+="&borrar="+borrarBd
	if (fullinvBd==true) url+="&fullinv="+fullinvBd
	if (confirm("<?php echo $msgstr["conf_import"]?> ??")==true) self.location=url
}
function Eliminar(Archivo){
	if (confirm("<?php echo $msgstr["cnv_deltab"]?>"+" "+Archivo)==true){
		url="carga_iso.php?base=<?php echo $arrHttp["base"]."&tipo=".$arrHttp["tipo"]?>&cnv="+Archivo+"&Opcion=cnv&accion=eliminar"
		self.location=url
	}
}
</script>

<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["cnv_import"]." ".$msgstr["cnv_iso"]?>
	</div>
	<div class="actions">
<?php echo "<a href=\"administrar.php?base=".$arrHttp["base"]."\"  class=\"defaultButton backButton\">";
?>
		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<span><strong><?php echo $msgstr["regresar"]?></strong></span></a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<?php
echo "
	<div class=\"helper\">
	<a href=../documentacion/ayuda.php?help=". $_SESSION["lang"]."/importiso.html target=_blank>".$msgstr["help"]."</a>&nbsp &nbsp";
	if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
		echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/importiso.html target=_blank>".$msgstr["edhlp"]."</a>";
		echo "<font color=white>&nbsp; &nbsp; Script: carga_iso.php</font>";
	echo "

	</div>
	 <div class=\"middle form\">
			<div class=\"formContent\">
	";
?>
<form name=explora action=eliminararchivo.php method=post>
<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
<input type=hidden name=tablacnv value="">
<?
}
$Dir=$db_path."wrk";
$the_array = Array();

$handle = opendir($Dir);
if ($arrHttp["accion"]!="eliminar"){

	while (false !== ($file = readdir($handle))) {
	   if ($file != "." && $file != "..") {
	   		if(is_file($Dir."/".$file))
	            $the_array[]=$file;
	        else
	            $dirs[]=$Dir."/".$file;
	   }
	}
	closedir($handle);
	if (count($the_array)>=0){
		sort ($the_array);
		reset ($the_array);
		$Url="";
		echo "<dd><h5><input type=checkbox name=borrar>".$msgstr["deldb"]."";
		echo "<dd><input type=checkbox name=fullinv>".$msgstr["mnt_gli"]."</h5>";
		echo "<dd><h5>".$msgstr["seleccionar"]." ".$msgstr["cnv_iso"]." </h5>";
		echo "<dd><table border=0  cellspacing=1 cellpadding=4 bgcolor=#cccccc>
		     <tr><td>".$msgstr["seleccionar"]."</td><td>".$msgstr["eliminar"]."</td><td>".$msgstr["archivo"]."</td>
			 <tr>
		   			<td bgcolor=white width=10></td>
		   			<td bgcolor=white></td>
		   			<td bgcolor=white></td>";
		while (list ($key, $val) = each ($the_array)) {
		   echo "<tr>

		   			<td bgcolor=white align=center><a href=javascript:Seleccionar('$val')><img src=img/aceptar.gif alt=\"".$msgstr["cnv_sel"]."\" border=0></a></td>
		   			<td bgcolor=white align=center><a href=javascript:Eliminar('$val')><img src=img/delete.gif border=0 alt=\"".$msgstr["eliminar"]."\"></a></td>
					<td bgcolor=white><strong>$val</strong></td>";
		}
		echo "</table>";

	}
}
if (isset($arrHttp["accion"])){
	switch ($arrHttp["accion"]){
		case "importar":
			echo "<dd><table><td>";
			$IsisScript=$xWxis."export_txt.xis";
			$query = "&base=".$arrHttp["base"] . "&cipar=$db_path"."par/".$arrHttp["cipar"]."&Opcion=importar&archivo=$Dir/".$arrHttp["cnv"]."&borrar=".$arrHttp["borrar"];
			if (isset($arrHttp["fullinv"]))
				$query.="&fullinv=".$arrHttp["fullinv"];
			include("../common/wxis_llamar.php");
			foreach($contenido as $linea) {
				$linea=trim($linea);
				$ix=strpos($linea,'[');
				if ($ix===false){
					echo "$linea<br>";
				}else{
					$ix1=strpos($linea,']');
					$msg=substr($linea,$ix+1,$ix1-$ix-1);
					echo substr($linea,$ix1+1)." ".$msgstr[$msg]."<br>";
				}

			}
			echo "</td></table>";
			break;
		case "eliminar":
			$fp=$db_path."wrk/".$arrHttp["cnv"];
			if (file_exists($fp)) {
				$r=unlink($db_path."wrk/".$arrHttp["cnv"]);
			}
			header("Location: carga_iso.php?base=".$arrHttp["base"]."&Opcion=cnv&tipo=".$arrHttp["tipo"]);
			die;
			break;
		default:

	}
}
echo "</form>";
if ($arrHttp["accion"]!="importar")
 	echo "

<form action=upload.php method=POST enctype=multipart/form-data>

<input type=hidden name=dir value=wrk>
<dd><table bgcolor=#eeeeee>
<tr>
<tr><td class=menusec1>".$msgstr["subir"]." ".$msgstr["cnv_iso"]. "</td><td class=menusec1></td>

<tr><td><input name=userfile[] type=file size=50></td><td></td>
<tr><td>  <input type=submit value='".$msgstr["subir"]."'></td>
<td><input type=hidden name=path value=wrk>
</table>
<p>
</form>";
if (!isset($arrHttp["fullinv"]))
	echo "<dd><h5><".$msgstr["recordarli"]."</h5>";
echo "</div></div>";
include("../common/footer.php");
?>
