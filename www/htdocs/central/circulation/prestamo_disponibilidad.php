<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      prestamo_disponibilidad.php
 * @desc:      Loan availability
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
// rutina para almacenar los préstamos otorgados,las renovaciones y las devoluciones
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
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";

include("leer_pft.php");
include("borrowers_configure_read.php");
$arrHttp["base"]="biblo/loans/";
include("databases_configure_read.php");
include("loanobjects_read.php");
include("calendario_read.php");
include("locales_read.php");

// Manejar la disponibilidad de los préstamos solicitados


function Disponibilidad(){
//se averigua si el ejemplar está prestado
global $xWxis,$arrHttp,$db_path,$Wxis;
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion="NI=".trim($arrHttp["inven"])."*TR_P";
 	$query = "&Opcion=librosprestados"."&base=trans&cipar=$db_path"."par/trans.par&Expresion=".$Expresion;
    include("../common/wxis_llamar.php");
	foreach($contenido as $linea){
		$linea=trim($linea);
		if (substr($linea,0,8)=='$$TOTAL:'){
			$npost=trim(substr($linea,8));
			if ($npost!=0){
				echo "<h3><font color=red>El ejemplar se encuentra prestado</font></h3>
				<a href=usuario_prestamos_presentar.php?base=users&usuario=".$arrHttp["usuario"].">Otro préstamo</a>";
				$disp= 0;
			}else{

                $disp= 1;
			}
		}else{
//			echo $linea;
		}
	}
	return $disp;
}

function ListarPrestamo($Expresion){
//se ubica el título en la base de datos de objetos de préstamo
global $xWxis,$arrHttp,$db_path,$Wxis;
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	$formato_obj=$db_path."biblo/loans/".$_SESSION["lang"]."/loans_display.pft";
    if (!isset($formato_obj)) $formato_obj=$db_path."biblo/loans/".$lang_db."/loans_display.pft";
 	$query = "&Opcion=".$arrHttp["Opcion"]."&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par&Expresion=".$Expresion."&Formato=$formato_obj";
    include("../common/wxis_llamar.php");
	return $contenido;
}
//cálculo del número de días transcurridos entre dos fechas
function getdays($day1,$day2)
{
  return round((strtotime($day2)-strtotime($day1))/(24*60*60),0);
}
include("fecha_de_devolucion.php");

function Libros_Prestados($Expresion){

}

// ------------------------------------------------------
// INICIO DEL PROGRAMA
// ------------------------------------------------------
include("../common/header.php");
include("../common/institutional_info.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";
?>
<Script>
ppu=new Array()
function TiposDeObjeto(){
	msgwin=window.open("loanobjects_ver.php?ver=s","ObjetosP","")
	msgwin.focus()
}
function VerCalendario(){
	msgwin=window.open("calendario.php?ver=s","ObjetosP","")
	msgwin.focus()
}
function GrabarPrestamo(){
    document.grabarp.submit()
}
</script>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["loan"]."/".$msgstr["return"]."/".$msgstr["reserve"]?>
	</div>
	<div class="actions">
		<a href="loan.php?base=".$arrHttp["base"]."&encabezado=s" class="defaultButton backButton">
			<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
			<span><?php echo $msgstr["back"]?></strong></span>
		</a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/asearch_schema.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/asearch_schema.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: prestamo_disponibilidad.php</font>\n";

echo "
	</div>
<div class=\"middle form\">
	<div class=\"formContent\">";

// se lee la tabla de tipos de objeto
unset($fp);
$archivo=$db_path."circulation/def/".$_SESSION["lang"]."/items.tab";
if (!file_exists($archivo)) $archivo=$db_path."circulation/def/".$lang_db."/items.tab";
$fp=file($archivo);
foreach ($fp as $value){
	$value=trim($value);
	if ($value!=""){
		$t=explode('|',$value);
		if (!isset($t[1])) $t[1]=$t[0];
		$type_items[$t[0]]=$t[1];
	}
}

 //se incluye el procedimiento para leer el usuario y los préstamos pendientes
include("ec_include.php");

// se obtiene el tipo del usuario y el número del usuario
$formato=$pft_uskey.'\'$$\''.$pft_ustype.'\'$$\''.$pft_usvig;
$formato=urlencode($formato);
$query = "&Expresion=".trim($uskey).$arrHttp["usuario"]."&base=users&cipar=$db_path"."par/users.par&Pft=$formato";
$contenido="";
$IsisScript=$xWxis."cipres_usuario.xis";
include("../common/wxis_llamar.php");
$user="";
foreach ($contenido as $linea){
	$linea=trim($linea);
	if ($linea!="")  $user.=$linea;
}
$userdata=explode('$$',$user);
echo "Código del usuario: ". $userdata[0] ."; Vigencia del usuario: ".$userdata[2]."<br>";

switch($userdata[2]){
	case "1":
		echo "<font color=red>Usuario no vigente</font>";
		break;
	case "2":
	    echo "<font color=red>Usuario suspendido</font>";
		break;
}

if ($userdata[2]!=0) die;

switch ($arrHttp["Opcion"]){
	case "inventario":
		$disp=0;
		$baseactiva=$arrHttp["base"];
		$ciparactivo=$arrHttp["cipar"];
	// se busca el título para ver el total de ejemplares y los ejemplares prestados
		$arrHttp["Opcion"]="disponibilidad";
		$Contenido=ListarPrestamo($arrHttp["Expresion"]); //Se lee el ejemplar a prestar para ver disponibilidad y tipo de material
		echo "<p><table bgcolor=#eeeeee width=100%><td bgcolor=white valign=top>";
		foreach($Contenido as $linea){
			$linea=trim($linea);
			if (substr($linea,0,8)=='$$TOTAL:'){
				$npost=trim(substr($linea,8));
				if ($npost==0){
					echo "<h3><font color=red>No se encontró el número de inventario</font></h3>
					<a href=usuario_prestamos_presentar.php?base=users&usuario=".$arrHttp["usuario"].">Otro préstamo</a>";
				}else{
					$disp=Disponibilidad();

				}
			}else{
				echo $linea;
			}
		}
		echo "</td>";

		break;
	case "signatura":
		$baseactiva=$arrHttp["base"];
		$ciparactivo=$arrHttp["cipar"];
	// se busca el título para ver el total de ejemplares y los ejemplares prestados
		$arrHttp["Opcion"]="disponibilidad";
		$Contenido=Disponibilidad($arrHttp["Expresion"]);
		foreach($Contenido as $linea)$Resultado=$Resultado.trim($linea);
		$Resultado=" |".$Resultado;
		break;
}

// se lee la información del los ejemplares del título : no. de inventario, tipo de registro, formato para almacenar en préstamos
if ($disp==1 and $npost>0){
	$item=str_replace("(","",$pft_in);
	$item=str_replace(")","",$item);
	$item="(if $item='".$arrHttp["inven"]."' then $item '\$\$' $pft_typeofr fi)'\$\$'$pft_storobj";
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($arrHttp["Expresion"]);
	$query = "&Opcion=disponibilidad&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par&Expresion=".$Expresion."&Pft=".urlencode($item);
	include("../common/wxis_llamar.php");
	$obj="";
	foreach ($contenido as $value){
		if (!empty($value))
			$obj.=$value;
	}
//	echo $obj;
	$objeto=explode('$$',$obj);
	echo "<td valign=top>No. inventario: ".$arrHttp["inven"]."<br>Tipo de material: ".$objeto[1]."<br>Tipo de usuario: ".$userdata[1]."<br><a href=javascript:PoliticaAplicar()>Política a aplicar</a> &nbsp; | &nbsp;<a href=javascript:VerCalendario()>Ver Calendario</a>";
	echo "<script>
	function PoliticaAplicar(){\n
		msgwin=window.open(\"\",\"\")
		";
		$i=-1;
		$ob=$politica[$objeto[1]][$userdata[1]];
		$obj=explode('|',$ob);
		foreach ($obj as $value){
			$i++;
			echo "msgwin.document.writeln(\"".$rows_title[$i]."=".$value."<br>\")\n";
		}
	echo "}
	</script>
	";
	$dia_cal=strftime("%d");
	$mes_cal=strftime("%m");
	$ano_cal=strftime("%Y");
	$hora_cal=strftime("%T");
	$date_format=explode("/",$config_date_format);
	$fp=date($date_format[0]."-".$date_format[1]."-".$date_format[2]]." h:i:s A");
	echo "<p>Fecha de préstamo: ".$fp;

	$fd=FechaDevolucion($obj[3],$obj[5]);
	echo "<br>Fecha de devolución: ".$fd;
	echo "<p><a href=javascript:GrabarPrestamo()>Grabar</a> | <a href=usuario_prestamos_presentar.php?base=users&usuario=".$arrHttp["usuario"].">Otro préstamo</a>";
	echo "</td></table>";
	$ix=strpos($fp," ");
	$diap=trim(substr($fp,0,$ix));
	$horap=trim(substr($fp,$ix));
	$ix=strpos($fd," ");
	$diad=trim(substr($fd,0,$ix));
	$horad=trim(substr($fd,$ix));

}
/*
$pft_totalitems=LeerPft("loans_totalitems.pft");
$pft_in=LeerPft("loans_inventorynumber.pft");
$pft_nc=LeerPft("loans_cn.pft");
$pft_dispobj=LeerPft("loans_display.pft");
$pft_storobj=LeerPft("loans_store.pft");
$pft_loandisp=LeerPft("loans_show.pft");
$pft_typeofr=LeerPft("loans_typeofobject.pft");

*/



//echo $Resultado;
	// se presenta el estado de cuenta del usuario
//	header("Location: prestamo_presentar.php?Opcion=prestamousuario&base=users&cipar=cipres.par&userid=".$arrHttp["userid"]."&Expresion=".$arrHttp["userid"]."&usuario=".$arrHttp["usuario"]."&inven=".$arrHttp["inven"]."&signa=".$arrHttp["signa"]."&libro=".str_replace(" ","+",$Resultado )); /* Redirect browser */

?>

<form name=grabarp action=prestamo_grabar.php>
<input type=hidden name=base value=trans>
<input type=hidden name=usuario value="<?php echo $arrHttp["usuario"]?>">
<input type=hidden name=inven value="<?php echo $arrHttp["inven"]?>">
<input type=hidden name=tu value="<?php echo $userdata[1]?>">
<input type=hidden name=to value="<?php echo $objeto[1]?>">
<input type=hidden name=fp value="<?php echo $diap?>">
<input type=hidden name=hp value="<?php echo $horap?>">
<input type=hidden name=fd  value="<?php echo $diad?>">
<input type=hidden name=hd  value="<?php echo $horad?>">
<input type=hidden name=referencia value="<?php echo $objeto[2]?>">
</form>
</body>
</html>