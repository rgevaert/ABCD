<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS 
 * @file:      copies_add_add.php
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
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
$lang=$_SESSION["lang"];

include("../lang/acquisitions.php");
include("../lang/admin.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value)  echo "$var=$value<br>";
//die;
include("../common/header.php");
echo "<body>\n";

?>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $arrHttp["base"].": ".$msgstr["createcopies"]?>
	</div>
	<div class="actions">
    	<a href="javascript:top.Menu('addcopies')" class="defaultButton backButton">
				<img src=../images/defaultButton_iconBorder.gif alt="" title="" />
				<span><strong><?php echo $msgstr["back"]?></strong></span>
			</a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/acquisitions/copies_create.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/acquisitions/copies_create.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: copies_add_add.php</font>\n";
?>
	</div>
<div class="middle form">
			<div class="formContent">
<?php
$arrHttp["database"]=$arrHttp["tag10"];
$arrHttp["objectid"]=$arrHttp["tag1"];
// se lee la FDT para conseguir la etiqueta del campo donde se coloca la numeración automática

LeerFst($arrHttp["database"]);

// Se lee la base de datos catalográfica para determinar si el objeto ha sido ingresado
$Formato=$db_path.$arrHttp["database"]."/pfts/".$_SESSION["lang"]."/".$arrHttp["database"].".pft" ;
if (!file_exists($Formato)) $Formato=$db_path.$arrHttp["database"]."/pfts/".$lang_db."/".$arrHttp["database"].".pft" ;
$Formato="@$Formato,/";
$Expresion="$pref_ctl".$arrHttp["objectid"];
$query = "&base=".$arrHttp["database"]."&cipar=$db_path"."par/".$arrHttp["database"].".par"."&Expresion=$Expresion&Formato=$Formato&Opcion=buscar";
$IsisScript=$xWxis."imprime.xis";
include("../common/wxis_llamar.php");
$cont_database=implode('',$contenido);
if (empty($cont_database)) {
	echo "<h4>".$arrHttp["objectid"].": ".$msgstr["objnoex"]."</h4>";
	echo "\n<script>top.toolbarEnabled=\"\"</script>\n";
	die;
}
echo $cont_database;
echo "<br>".$msgstr["copies_no"].": ".$arrHttp["copies"];

$Mfn="";
if (isset($arrHttp["tag30"])){
	$inven=explode("\n",$arrHttp["tag30"]);
	unset($arrHttp["tag30"]);
	foreach ($inven as $cn) CrearCopia(trim($cn));
}else{
	for ($ix=1;$ix<=$arrHttp["copies"];$ix++ ){
		echo "<hr>";
		$cn=ProximoNumero("copies");   // GENERATE THE INVENTORY NUMBER
		CrearCopia($cn);
	}
}


echo "</div></div>";
include("../common/footer.php");
echo "</body></html>";
echo "\n<script>top.toolbarEnabled=\"\"</script>\n";

//=================================================================

function CrearCopia($cn){
global $msgstr,$arrHttp,$xWxis,$Wxis,$db_path;
	echo "<br>".$msgstr["createcopies"].": ";
	echo $msgstr["inventory"].": $cn";
	// Se verifica si ese número de inventario no existe
	$res=BuscarCopias($database,$order,$suggestion,$bidding,$cn);
	if ($res>0){
		echo "<font color=red> &nbsp;".$msgstr["invdup"]."</font>";
	}else{
		$ValorCapturado="";
		foreach ($arrHttp as $var=>$value){
			if (substr($var,0,3)=="tag"){
				$tag=trim(substr($var,3));
				if (strlen($tag)==1) $tag="000".$tag;
				if (strlen($tag)==2) $tag="00".$tag;
				if (strlen($tag)==3) $tag="0".$tag;

				if ($ValorCapturado=="")
					$ValorCapturado=$tag.$value;
				else
					$ValorCapturado.="\n".$tag.$value;
			}
		}
		$ValorCapturado.="\n0030$cn";
		$ValorCapturado=urlencode($ValorCapturado);
		$IsisScript=$xWxis."actualizar.xis";
		$query = "&base=copies&cipar=$db_path"."par/copies.par&login=".$_SESSION["login"]."&Mfn=New&Opcion=crear&ValorCapturado=".$ValorCapturado;
		include("../common/wxis_llamar.php");
		foreach ($contenido as $linea){
			if (substr($linea,0,4)=="MFN:") {
				echo " &nbsp; Mfn: <a href=../dataentry/show.php?base=copies&Mfn=".trim(substr($linea,4))." target=_blank>".trim(substr($linea,4))."</a>";
	    		$Mfn.=trim(substr($linea,4))."|";
			}else{
				if (!empty($linea)) echo $linea."\n";
			}
		}
   	}

}

function ProximoNumero($base){
global $db_path,$msgstr;
	$archivo=$db_path.$base."/data/control_number.cn";
	if (!file_exists($archivo)){
		echo "<h2>".$msgstr["notfound"].": $base/data/control_number.cn</h2>";
		return 0;
	}
	$perms=fileperms($archivo);

	if (is_writable($archivo)){
	//se protege el archivo con el número secuencial
		chmod($archivo,0555);
	// se lee el último número asignado y se le agrega 1
		$fp=file($archivo);
		$cn=implode("",$fp);
		$cn++;
	// se remueve el archivo .bak y se renombre el archivo .cn a .bak
		if (file_exists($db_path.$base."/data/control_number.bak"))
			unlink($db_path.$base."/data/control_number.bak");
		$res=rename($archivo,$db_path.$base."/data/control_number.bak");
		chmod($db_path.$base."/data/control_number.bak",0666);
		$fp=fopen($archivo,"w");
	    fwrite($fp,$cn);
	    fclose($fp);
	    chmod($archivo,0666);
	    return $cn;
	}
}

function BuscarCopias($database,$order,$suggestion,$bidding,$inventario){
global $xWxis,$db_path,$Wxis;
	if ($inventario!=""){
		$Prefijo="IN_".$inventario;
	}else{
		$Prefijo='ORDER_'.$order.'_'.$suggestion.'_'.$bidding;
	}
	$IsisScript= $xWxis."ifp.xis";
	$query = "&base=copies&cipar=$db_path"."par/copies.par&Opcion=diccionario&prefijo=$Prefijo&campo=1";
	$contenido=array();
	include("../common/wxis_llamar.php");
	foreach ($contenido as $linea){
		if (!empty($linea)) {
			$pre=trim(substr($linea,0,strlen($Prefijo)));
			if ($pre==$Prefijo){
				$l=explode('|',$linea);
				return $l[1];
				break;
			}
		}
	}
	return 0;
}

function LeerFst($base){
global $tag_ctl,$pref_ctl,$arrHttp,$db_path,$AI,$lang_db,$msgstr,$error;
// GET THE FST TO FIND THE CONTROL NUMBER OF A BIBLIOGRAPHIC DATABASE
	$archivo=$db_path.$base."/data/".$base.".fst";
	if (!file_exists($archivo)){
		echo $msgstr["notfound"].": ".$base."/data/".$base.".fst";
		die;
	}
	$fp=file($archivo);
	$tag_ctl="";
	$pref_ctl="CN_";
	foreach ($fp as $linea){
		$linea=trim($linea);
		$ix=strpos($linea,"\"CN_\"");
		if ($ix===false){
			$ix=strpos($linea,'|CN_|');
		}
		if ($ix===false){
		}else{
			$ix=strpos($linea," ");
			$tag_ctl=trim(substr($linea,0,$ix));
			break;
		}
	}
	// Si no se ha definido el tag para el número de control en la fdt, se produce un error
	if ($tag_ctl==""){
		$error="missingctl";
	}
}

?>
