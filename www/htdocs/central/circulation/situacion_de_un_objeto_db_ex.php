<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      situacion_de_un_objeto_db_ex.php
 * @desc:      Shows the status of the items of an bibliographic record when the items are defined inside the bilbiographic record
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
// Situación de un objeto
if (!isset($_SESSION["permiso"])){	header("Location: ../common/error_page.php") ;
}if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
include("../config_loans.php");
$lang=$_SESSION["lang"];

include("../lang/admin.php");
include("../lang/prestamo.php");

include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
$Opcion_leida=$arrHttp["Opcion"];
include("leer_pft.php");
include("borrowers_configure_read.php");

include("loanobjects_read.php");
include("calendario_read.php");
include("locales_read.php");


function PrepararFecha($FechaP){

global $locales,$config_date_format;;
//Se convierte la fecha al formato de fecha local
	$df=explode('/',$config_date_format);
	switch ($df[0]){
		case "DD":
			$dia=substr($FechaP,6,2);
			break;
		case "MM":
			$mes=substr($FechaP,6,2);
			break;
	}
	switch ($df[1]){
		case "DD":
			$dia=substr($FechaP,4,2);
			break;
		case "MM":
			$mes=substr($FechaP,4,2);
			break;
	}
	$year=substr($FechaP,0,4);
	return $dia."-".$mes."-".$year;
}

function LocalizarInventario($inventory){
global $db_path,$Wxis,$xWxis,$arrHttp,$pft_totalitems,$pft_in,$pft_nc,$pft_typeofr,$tit_cat,$prefix_in,$multa;
	//$Expresion=trim($prefix_in).$arrHttp["inventory"];
    $Expresion="CONTROL_".$inventory;
	$formato_ex="v1'||'v10'||',(v959^i,'$$$ ',v959^l,'$$$ ',v959^b,'$$$ ',v959^o,'$$$ ',v959^v,'$$$ ',v959^t'###'),/";
	//se ubica el título en la base de datos de objetos de préstamo
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	$formato_obj="";
	$formato_obj.=urlencode($formato_ex);
	$query = "&Opcion=disponibilidad&base=loanobjects&cipar=$db_path"."par/loanobjects.par&Expresion=".$Expresion."&Pft=$formato_ex";
	echo $query;
	include("../common/wxis_llamar.php");
	$total=0;
	$tit_cat=array();
	$ix=0;
	foreach ($contenido as $linea){
		$linea=trim($linea);
		if (substr($linea,0,8)=='$$TOTAL:')
			$total=trim(substr($linea,8));
		else
				$tit_cat[]=$linea;

	}

	return $total;
}


// Se localiza el número de inventario en la base de datos de objetos  de préstamo
function ReadCatalographicRecord($control_number,$db){
global $db_path,$Wxis,$xWxis,$arrHttp,$pft_totalitems,$pft_in,$pft_nc,$pft_typeofr,$titulo,$prefix_in,$prefix_cn,$multa,$lang_db;

	//Read the FDT of the database for extracting the prefix used for indexing the control number
//	echo $control_number;
    $Expresion=$prefix_cn.$control_number;
	//se ubica el título en la base de datos de objetos de préstamo
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	echo "readcr=$Expresion<br>";
	$formato_obj=$db_path."$db/loans/".$_SESSION["lang"]."/loans_display.pft";
	if (!file_exists($formato_obj)) $formato_obj=$db_path."$db/loans/".$lang_db."/loans_display.pft";
	$query = "&Opcion=disponibilidad&base=$db&cipar=$db_path"."par/$db.par&Expresion=".$Expresion."&Formato=$formato_obj";
	include("../common/wxis_llamar.php");
	$total=0;
	$titulo="";
	foreach ($contenido as $linea){
		$linea=trim($linea);
		if (substr($linea,0,8)=='$$TOTAL:')
			$total=trim(substr($linea,8));
		else
			$titulo.=$linea."\n";
	}
	return $total;

}



function ListarPrestamo($Expresion){
//se ubican todas las copias disponibles para verificar si están prestadasglobal $xWxis,$arrHttp,$db_path,$Wxis;
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	$formato_obj="v20'$$$'if p(v500) then v500 else v40 fi,'###'" ;
 	$query = "&Opcion=".$arrHttp["Opcion"]."&base=trans&cipar=$db_path"."par/trans.par&Expresion=".$Expresion."&Pft=$formato_obj&desde=".$arrHttp["desde"]."&cuenta=10";
    include("../common/wxis_llamar.php");
	return $contenido;
}
include("fecha_de_devolucion.php");


// ------------------------------------------------------
// INICIO DEL PROGRAMA
// ------------------------------------------------------
include("../common/header.php");
include("../common/institutional_info.php");
?>
<script>
function EnviarForma(Opcion){	eliminar=""	if (Opcion==0){		document.continuar.submit()	}else{		for (i=0; i<document.objeto.elements.length;i++){			if (document.objeto.elements[i].type=="checkbox"){				if (document.objeto.elements[i].checked) eliminar+=document.objeto.elements[i].value+"|"			}		}
		if (eliminar==""){			alert("debe seleccionar las reservas que va a eliminar")
			return		}else{
			alert(eliminar)			document.continuar.action="reservas_eliminar_ex.php"
			document.continuar.reservas.value=eliminar
			document.continuar.submit()		}	}}
</script>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["ecobj"]?>
	</div>
	<div class="actions">
		<a href="situacion_de_un_objeto.php?base=".$arrHttp["base"]."&encabezado=s" class="defaultButton backButton">
			<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
			<span><?php echo $msgstr["back"]?></strong></span>
		</a>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/situacion_de_un_objeto.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/situacion_de_un_objeto.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: situacion_de_un_objeto_ex.php</font>\n";

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
//GET THE INFORMATION OF THE DATABASE SELECTED
$b=explode('|',$arrHttp["db"]);
$catalog_db=$b[0];
$arrHttp["db"]=$catalog_db;
require_once("databases_configure_read.php");
$total=ReadCatalographicRecord($arrHttp["code"],$catalog_db);
if ($total>1){	echo "<font color=red>".$msgstr["dupctrl"]."</font><p>";}
echo '<font color=darkblue><strong>'.$msgstr["bd"].": ". $catalog_db.". ".$msgstr["control_n"].": ".$arrHttp["code"]."</strong></font><br>";
echo $titulo;
echo "<table bgcolor=#dddddd cellpadding=5>
			<th></th>
			<th>".$msgstr["inventory"]."</th>
			<th>".$msgstr["volume"]."</th>
			<th>".$msgstr["tome"]."</th>
			<th>".$msgstr["typeofitems"]."</th>
			<th>".$msgstr["usercode"]."</th>
			<th>".$msgstr["devdate"]."</th>";
//SE OBTIENEN LOS EJEMPLARES DEL TITULO
$Expresion=$prefix_cn.$arrHttp["code"];
echo "----".$Expresion;
$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
$Expresion=urlencode($Expresion);
$pft_inventory=LeerPft("loans_inventorynumber.pft");
$pft_inventory="(".$pft_inventory."/)";
$query = "&Opcion=disponibilidad&base=$catalog_db&cipar=$db_path"."par/$catalog_db.par&Expresion=".$Expresion."&Pft=$pft_inventory";
include("../common/wxis_llamar.php");
foreach ($contenido as $val) {	if (trim($val)!="") ShowItems($val);}
echo "</table>";
echo "<p>";
echo "</div></div>";
include("../common/footer.php");
echo "</body></html>";


Function ShowItems($item){
	if (substr($item,0,8)!='$$TOTAL:'){
		$l=explode('$$$',$item);
		echo "<tr><td bgcolor=white></td>";
		echo "<td bgcolor=white>".$l[0]."</td>";
		echo "<td bgcolor=white>".$l[4]."</td>";
		echo "<td bgcolor=white>".$l[5]."</td>";
		echo "<td bgcolor=white>".$l[3]."</td>";
	    $Expresion="TR_P_".$l[0];
	    $cont=ListarPrestamo($Expresion);
	    $cont=implode("",$cont);
	    if (substr($cont,0,8)!='$$TOTAL:'){
	    	$cont=explode('###',$cont);
	    	$c=explode('$$$',$cont[0]);
	    	echo "<td bgcolor=white>".$c[0]."</td><td bgcolor=white>".PrepararFecha($c[1])."</td>";
	    }else{	    	echo "<td bgcolor=white>&nbsp;</td><td bgcolor=white>&nbsp;</td>";	    }
	 }

}


function EjemplaresPrestados($inventory){
global $db_path,$Wxis,$xWxis;	$formato_obj=$db_path."trans/pfts/".$_SESSION["lang"]."/loans_display.pft";
    if (!file_exists($formato_obj)) $formato_obj=$db_path."trans/pfts/".$lang_db."/loans_display.pft";
   	$query = "&Expresion=TR_P_".$inventory."&base=trans&cipar=$db_path"."par/trans.par&Formato=".$formato_obj;
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$prestamos=array();
	foreach ($contenido as $linea){
		$prestamos[]=$linea;
	}
	$nv=0;   //número de préstamos vencidos
	$np=0;   //Total libros en poder del usuario
	if (count($prestamos)>0) {

		foreach ($prestamos as $linea) {
			$p=explode("^",$linea);
			$np=$np+1;
			$dif= compareDate ($p[9]);
			$fuente="";
			$mora="0";
			if ($dif<0) {
				$nv=$nv+1;
				$mora=abs($dif/(60*60*24));    //cuenta de préstamos vencidos
			    $fuente="<font color=red>";
			}
			echo "<td  bgcolor=white valign=top><xa href=usuario_prestamos_presentar.php?usuario=".$p[10]." target=_blank>";
			echo $p[10];
			echo "</a></td>

				<td bgcolor=white align=center valign=top><xa href=VerPolitica('".$p[3]."','".$p[6]."')>". $p[3]. "</a></td><td bgcolor=white nowrap align=center valign=top>".$p[4]."</td><td nowrap bgcolor=white align=center valign=top>$fuente".$p[5]."</td><td align=center bgcolor=white valign=top>".$mora."</td></tr>";

		}
//		echo "</table></dd><br>";
        echo "<script>
		np=$np
		nv=$nv
		</script>\n";
	}
}
?>