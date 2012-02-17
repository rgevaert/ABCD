<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      c_global_ex.php
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


include("../lang/soporte.php");
include("../lang/admin.php");
set_time_limit(0);


function CambiarCampo($Mfn,$ValorCapturado,$Tag){
global $xWxis,$db_path,$Wxis,$arrHttp,$wxisUrl;
   	$query="&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["cipar"]."&Mfn=$Mfn&count=1";
 	$query.="&login=".$arrHttp["login"]."&ValorCapturado=".urlencode($ValorCapturado);
 	$contenido="";
 	$IsisScript=$xWxis."actualizar_cg.xis";
	include("../common/wxis_llamar.php");
	foreach ($contenido as $value) echo "$value<br>";
 	$nfilas=0;

}

function EjecutarCambio($cont,$Anterior,$Tag,$Actual){
global $arrHttp,$ValorCapturado,$Cambiado;

	switch ($arrHttp["tipoc"]){
		case "eliminar":
			if ($Anterior!=""){
				if (strtoupper($Actual)==strtoupper($Anterior)){
			//	$ValorCapturado.="a".$Tag."²".$arrHttp["nuevo"]."²\n";
					$Cambiado="S";
				}
			}else{				$Cambiado="S";			}
			break;
		case "eliminarocc":
			if ($Anterior!=""){
				if (strtoupper($Actual)==strtoupper($Anterior)){
			//	$ValorCapturado.="a".$Tag."²".$arrHttp["nuevo"]."²\n";
					$Cambiado="S";
				}
			}else{
				$Cambiado="S";
			}
			break;
		case "modificar":
		case "modificarocc":
			if ($arrHttp["tipoa"]=="cadena"){
				if (strpos($Actual,$Anterior)===false){				}else{
					$NuevoValor=str_replace($Anterior,$arrHttp["nuevo"],$Actual);
					$ValorCapturado.="a".$Tag."×".$NuevoValor."×\n";
					$Cambiado="S";
				}
			}else{
				if (strcasecmp(trim($Actual), trim($Anterior)) == 0){
					$ValorCapturado.="a".$Tag."×".$arrHttp["nuevo"]."×\n";
					$Cambiado="S";
				}
			}

			break;
		case "dividir":
			$ix=strpos($Actual,$arrHttp["separar"]);
			if ($ix===false){
			}else{
				$campo_ad=trim(substr($Actual,0,$ix));
				$campo_dd=trim(substr($Actual,$ix+strlen($arrHttp["separar"])));
				if (isset($arrHttp["posicion"])){
					switch ($arrHttp["posicion"]){
						case "antes":
							$amover=$campo_ad;
							$resto=$campo_dd;
							break;
						case "despues":
							$amover=$campo_dd;
							$resto=$campo_ad;
							break;
					}
				}
				if ($resto!="") {
					$ValorCapturado.="a".$Tag."×".$resto."×\n";
					$Cambiado="S";
				}
				if ($amover!=""){
					$T=explode('|',$arrHttp["nuevotag"]);
					$Tag_n=$T[0];
					if ($Tag_n!="")
						$ValorCapturado.="a".$Tag_n."×".$amover."×\n";
					$Cambiado="S";
				}
			}
			break;
		case "agregar":
		case "agregarocc":
			$Cambiado="S";
			$ValorCapturado.="a".$Tag."×".$arrHttp["nuevo"]."×\n";
			break;
		}
}


include("leer_fdt.php");

global  $arrHttp,$xWxis;

// ==================================================================================================
// INICIO DEL PROGRAMA
// ==================================================================================================

//


include("../common/get_post.php");
//foreach ($arrHttp as $val=>$value) echo "$val=$value<br>";
?>
<script>
function Presentar(Mfn){	url="leer_all.php?base=<?php echo $arrHttp["base"]?>&cipar=<?php echo $db_path."par/".$arrHttp["base"]?>.par&Mfn="+Mfn+"&count=1"
	msgwin=window.open(url,"SEE","")
	msgwin.focus()}
</script>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["cg_titulo"].": ".$arrHttp["base"]?>
	</div>
	<div class="actions">
<?php echo "<a href=\"c_global.php?base=".$arrHttp["base"]."\"  class=\"defaultButton backButton\">";?>

		<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
		<span><strong><?php echo $msgstr["regresar"]?></strong></span></a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<?php
$base =$arrHttp["base"];
$cipar =$arrHttp["cipar"];
$arrHttp["login"]=$_SESSION["login"];
$arrHttp["password"]=$_SESSION["password"];
$login=$arrHttp["login"];
$password=$arrHttp["password"];
$MaxMfn=$arrHttp["MaxMfn"];

//foreach ($arrHttp as $key => $value) echo "$key = $value <br>";
// se lee el archivo mm.fdt
if ($arrHttp["login"]==""){
  	echo $msgstr["menu_noau"];
  	die;
}
if (!isset($arrHttp["nuevo"])) {
    $arrHttp["nuevo"]="";
}
$Fdt=LeerFdt($base);
foreach ($Fdt as $tag=>$linea){
	$Titulos[$tag]=trim(substr($linea,3,34));
}

//echo $arrHttp["anterior"];
if (isset($arrHttp["actual"])) $ValorAnterior=explode("\r",$arrHttp["actual"]);
include("../common/header.php");
echo "
	<div class=\"helper\">
	<a href=../documentacion/ayuda.php?help=". $_SESSION["lang"]."/cglobal_ex.html target=_blank>".$msgstr["help"]."</a>&nbsp &nbsp";
	if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
		echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/cglobal_ex.html target=_blank>".$msgstr["edhlp"]."</a>";
	echo "<font color=white>&nbsp; &nbsp; Script: c_global_ex.php</font>";
	echo "

	</div>
	 <div class=\"middle form\">
			<div class=\"formContent\">

	<form name=tabla method=post action=c_global_ex.php>
	<br><br><center>
";

	$T=explode('|',$arrHttp["global_C"]);
	$tit= $Titulos[$T[0]];
    $tx=explode('|',$tit);
	echo "<strong>(".$T[0].") ".$tx[1]."</strong>";
	$Tag=$T[0];
	while (strlen($Tag)<3) $Tag="0".$Tag;
	echo "<center><div style=\"width:700px;border-style:solid;border-width:1px; \">";
	if (isset($ValorAnterior)){

		echo "<strong>".$msgstr["cg_locate"].": </strong>";
		if ($arrHttp["tipoc"]!="dividir"){
			foreach ($ValorAnterior as $value) echo stripslashes($value)."<br>";
		}
	}
	echo $msgstr["g_newval"].": <font color=#222222>".stripslashes($arrHttp["nuevo"])."</font>";
	echo "</div>";
?>
<center>
<table bgcolor=#cccccc cellspacing=1 border=0 cellpadding=5>
<tr><td bgcolor=white align=center>Mfn</td><td bgcolor=white align=center>
    </td><td bgcolor=white align=center></td>
<?
$Formato="(V".$T[0].'+|$$|)';

$IxMfn=0;
if (!isset($arrHttp["from"])) $arrHttp["from"]=1;
if (!isset($arrHttp["count"])) $arrHttp["count"]=100;
if ($arrHttp["Opcion"]=="rango"){						//se construye el rango de Mfn's a procesar
	$tope=$arrHttp["from"]+$arrHttp["count"]-1;
	if ($tope>=$arrHttp["to"]) $tope=$arrHttp["to"];
	$hasta=$arrHttp["to"];
}else{
	$tope=$arrHttp["count"]+$arrHttp["from"]-1;
	$arrHttp["from"]=1;
	$tope=50;

}


$ixMfn=0;
if (!isset($arrHttp["Expresion"])) $arrHttp["Expresion"]="";
for ($Mfn=$arrHttp["from"];$Mfn<=$tope;$Mfn++){
  	$IxMfn=$IxMfn+1;
   	$query="&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["cipar"]."&Mfn=$Mfn&count=1";
 	$query.="&Formato=".urlencode($Formato)."&Expresion=".urlencode(stripslashes($arrHttp["Expresion"]))."&Opcion=".$arrHttp["Opcion"];
 	$contenido="";
 	$IsisScript=$xWxis."act_tabla.xis";
	include("../common/wxis_llamar.php");
 	$nfilas=0;
	$ValorCapturado="";
	if ($arrHttp["tipoc"]!="agregarocc")
		$ValorCapturado="d".$Tag."\n";
	$Actualizar="";
	foreach ($contenido as $linea){
		$xcampos=explode('|',$linea);
	   	$linea=$xcampos[0];
		$posicion=trim(substr($linea,11));
		$ipos=strpos($posicion,'$$');
		$desde=substr($posicion,0,$ipos);
		$hasta=substr($posicion,$ipos+2);
		if ($arrHttp["Opcion"]=="rango")
			if (isset($arrHttp["to"])) $hasta=$arrHttp["to"];
		$arrHttp["from"]=$desde+1;
		$desde=$desde+1;
		$Nreg=$xcampos[1];
		$contenido_c=explode('$$',$xcampos[2]);
		if ($arrHttp["Opcion"]=="rango"){
			$seq="";
		}else{
			$seq="($Mfn)";
			//	$Nreg=1;
		}

		foreach($contenido_c as $cont){
   			$Cambiado="";

   			if (isset($ValorAnterior)){
   				$verifica="";
				foreach ($ValorAnterior as $Anterior){					if ($cont==$Anterior){
						$verifica="S";
						EjecutarCambio($Mfn,$Anterior,$Tag,$cont);
						if ($Cambiado==""){
		  					$ValorCapturado.="a".$Tag."²" .$cont."²\n";
						}else{
							echo "<tr><td bgcolor=white>$seq <a href=javascript:Presentar($Mfn)>".$Nreg."</a></td><td bgcolor=white><font size=2>".$cont."</td>\n<td bgcolor=white>";							echo " <b>OK!!!</b>";
							$Actualizar="S";
							break;
						}
					}
					if ($verifica=="")	$ValorCapturado.="a".$Tag."²" .$cont."²\n";				}
			}else{
				EjecutarCambio($Mfn,"",$Tag,$cont);
				echo $arrHttp["nuevo"];
				if ($Cambiado==""){
  					$ValorCapturado.="a".$Tag."²" .$cont."²\n";
				}else{
					echo "<tr><td bgcolor=white>$seq <a href=javascript:Presentar($Mfn)>".$Nreg."</a></td><td bgcolor=white><font size=2>".$cont."</td>\n<td bgcolor=white>";
					echo " <b>OK!!!</b>";
					$Actualizar="S";
					break;
				}
			echo "</td>\n";
			}
		}
		if ($Actualizar=="S") CambiarCampo($Nreg,$ValorCapturado,$Tag);
		if ($arrHttp["Opcion"]=='buscar') $tope=$hasta;
	}
}

echo "</table>";
switch ($arrHttp["Opcion"]){
  	case "rango":
  		$arrHttp["from"]=$Mfn;
  		break;
	case "buscar":
		echo "<br><div style=\"width=700;border-style:solid;border-width:1px \"><font size=1 face=arial>Expresion: ".stripslashes($arrHttp["Expresion"])."<br>Recuperados en la búsqueda : $hasta registros</div>";
		break;
}
?>
<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
<input type=hidden name=cipar value=<?php echo $arrHttp["cipar"]?>>
<input type=hidden name=actual value="<?php echo stripslashes($arrHttp["actual"])?>">
<input type=hidden name=nuevo value="<?php echo stripslashes($arrHttp["nuevo"])?>">
<input type=hidden name=to value="<?php echo stripslashes($arrHttp["to"])?>">
<input type=hidden name=MaxMfn value="<?php echo $arrHttp["MaxMfn"]?>">
<input type=hidden name=Expresion value="<?php echo stripslashes($arrHttp["Expresion"])?>">
<input type=hidden name=Opcion value="<?php echo $arrHttp["Opcion"]?>">
<input type=hidden name=global_C value="<?php echo $arrHttp["global_C"]?>">
<input type=hidden name=tipoc value="<?php echo $arrHttp["tipoc"]?>">
<input type=hidden name=tipoa value="<?php echo $arrHttp["tipoa"]?>">
<input type=hidden name=posicion value="<?php if (isset($arrHttp["posicion"])) echo $arrHttp["posicion"]?>">
<input type=hidden name=nuevotag value="<?php if (isset($arrHttp["nuevotag"])) echo $arrHttp["nuevotag"]?>">
<input type=hidden name=separar value="<?php if (isset($arrHttp["separar"])) echo $arrHttp["separar"]?>">
<?
if ($arrHttp["Opcion"]=='buscar') {
	$arrHttp["from"]=1;
	$Mfn=1;
}
if ($Mfn<$hasta){
 	$IxMfn=$IxMfn+1;
	echo "<p><font face=arial size=1>
	Próximo registro:<input type=text size=5 name=from value='".$arrHttp["from"]."'>,
	procesar <input type=text size=5 name=count value=".$arrHttp["count"]."> registros más
	<br><a href=javascript:document.tabla.submit()>".$msgstr["continuar"]."</a><br>";
}else{

}
?>
</td>
</form>
</table>
<form name=menu method=post action=c_global.php>
<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
<input type=hidden name=cipar value=<?php echo $arrHttp["cipar"]?>>
</form>
<p>
</div>
</div>
<?php include("../common/footer.php")?>
</body>
</html>
