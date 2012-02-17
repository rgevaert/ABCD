<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      tables_generate_ex.php
 * @desc:      GENERATES THE OUTPUT FROM THE STATISTICAL TABLES
 *             The process allows to select four diferent outputs: to write in a
 *             Microsoft Excel file; to write in a Microsoft Word file; to write
 *             directly into printer; or to generate an animated graphic into video.
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
include ("../config.php");
include ("../lang/statistics.php");

//VALORES QUE VIENEN DE LA PÁGINA
include("../common/get_post.php");
//foreach ($arrHttp as $key => $value) echo "$key = $value <br>";
$arrHttp["rows"]=urldecode($arrHttp["rows"]);
// SELECCION DE LOS REGISTROS
if (isset($arrHttp["Expresion"]))
	$Opcion="search";
else
	$Opcion="range";

//TOOLBAR O PÁGINA COMPLETA?
if (isset($arrHttp["encabezado"]))
	$encabezado="&encabezado=S";
else
	$encabezado="";


//HEADER DEL LA PÁGINA HTML Y ARCHIVOS DE ESTIVO
include("../common/header.php");
?>
<script>
//PARA AGREGAR NUEVAS VARIABLES A LA LISTA
function returnObjById( id )
{
    if (document.getElementById)
        var returnVar = document.getElementById(id);
    else if (document.all)
        var returnVar = document.all[id];
    else if (document.layers)
        var returnVar = document.layers[id];
    return returnVar;
}

function getElement(psID) {
	if(!document.all) {
		return document.getElementById(psID);

	} else {
		return document.all[psID];
	}
}

function SendTo(Opcion){
<?php
	$titulo="";
	if (isset($arrHttp["rows"])){
		$l=explode('|',$arrHttp["rows"]);
		$titulo=$l[0];
	}
	if (isset($arrHttp["tables"])){
		$l=explode('|',$arrHttp["tables"]);
		$titulo=$l[0];
	}
	if (isset($arrHttp["cols"])){
		if ($titulo!="") $titulo.="/";
		$l=explode('|',$arrHttp["cols"]);
		$titulo.=$l[0];
	}
	$titulo= urlencode($titulo);

	?>

	if (Pie=="S")
		urlPie="?tipo=pie&Pie=S"
	else
		urlPie="?tipo="
	urlPie+="&base=<?php echo $arrHttp["base"]."&cipar=".$arrHttp["base"].".par&titulo=$titulo"?>"
	switch(Opcion){
		case 'AG':
			msgwin=window.open("cuadro_animado.php"+urlPie,"cuadro")
			msgwin.focus()
			break;
		case 'NAG':
			break
			msgwin=window.open("cuadro_noanimado.php"+urlPie,"cuadro")
			msgwin.focus()
			break;
		default:
			if (Opcion=="P"){
				msgwin=window.open("","STATS","")
				msgwin.focus()
			}else{
				document.sendto.target=""
			}
			seccion=returnObjById( "results" )
			html=seccion.innerHTML
			document.sendto.html.value=html
			document.sendto.Opcion.value=Opcion
			document.sendto.submit()
			if (Opcion=="P") msgwin.focus()
	}
}
</script>
<body>
<?php
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
	$encabezado="&encabezado=s";
}
?>
<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["stats"].": ".$arrHttp["base"]?>
	</div>

	<div class="actions">
<?php
if (isset($arrHttp["encabezado"]))
	echo "<a href=\"tables_generate.php?base=".$arrHttp["base"]."$encabezado\" class=\"defaultButton backButton\">
	<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
<span><strong>".$msgstr["back"]."</strong></span></a>
	";
?>

</div>

<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/stats/stats_tables_generate.html#TABLE target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/stats/stats_tables_generate.html#TABLE target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: tables_generate_ex.php";
?>
</font>
</div>
<form name=forma1 method=post onsubmit="Javascript:return false">
<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
<input type=hidden name=cipar value=<?php echo $arrHttp["base"]?>.par>

<?php if (isset($arrHttp["encabezado"])) echo "<input type=hidden name=encabezado value=s>\n";
?>
<div class="middle form">
	<div class="formContent">
<?

//SE LEE LA LISTA DE VARIABLES
unset($fp);
$file=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/stat.cfg";
if (!file_exists($file)) $file=$db_path.$arrHttp["base"]."/def/".$lang_db."/stat.cfg";
if (!file_exists($file)){
	$error="S";
}else{
	$tab_vars=file($file);
}
echo "<dd>".$msgstr["sendto"].": ";
echo "<a href=javascript:SendTo(\"W\")>".$msgstr["wks"]."</a> &nbsp; | &nbsp; ";
echo "<a href=javascript:SendTo(\"D\")>".$msgstr["doc"]."</a> &nbsp; | &nbsp; ";
echo "<a href=javascript:SendTo(\"P\")>".$msgstr["prn"]."</a> &nbsp; | &nbsp; ";
echo "<a href=javascript:SendTo(\"AG\")>".$msgstr["ag"]."</a> &nbsp; | &nbsp; ";
//echo "<a href=javascript:SendTo(\"NAG\")>".$msgstr["nag"]."</a> &nbsp; | &nbsp; ";
echo "<div id=results>";
// SE DETERMINA SI ES UNA TABLA DE FRECUENCIA O UNA TABLA DE CONTINGENCIA
// Y sE CONSTRUYE EL FORMATO PARA EXTRAER LAS FILAS Y/O LAS COLUMNAS
$Pie="";
if (isset($arrHttp["tables"])){
    $arrHttp["tables"]=stripslashes($arrHttp["tables"]);
	$Formato=Contingencia();
}else{
// SE CONSTRUYE LA VARIABLE $ARRHTTP["TABLES"], SIMULANDO LA LECTURA DE UNA TABLA DESDE LA PÁGINA
	if (isset($arrHttp["rows"]) and isset($arrHttp["cols"])){
		$arrHttp["rows"]=stripslashes($arrHttp["rows"]);
		$arrHttp["cols"]=stripslashes($arrHttp["cols"]);
		$t=explode('|',$arrHttp["rows"]);
		$titulo=$t[0];
		$arrHttp["tables"]=$t[0];
		$t=explode('|',$arrHttp["cols"]);
		$titulo.="/".$t[0];
		$arrHttp["tables"].="|".$t[0];
		$arrHttp["tables"]=$titulo."|".$arrHttp["tables"];
		unset($arrHttp["rows"]);
		unset($arrHttp["cols"]);
		$Formato=Contingencia();
	}else{
		$Pie="S";
 		if (isset($arrHttp["rows"])){
 			$Formato=Frecuencia($arrHttp["rows"]);
 		}else{
 			if (isset($arrHttp["cols"])) {
 			   $Formato=Frecuencia($arrHttp["cols"]);
 			}
 		}
 	}
}
//
echo "\n<script>Pie=\"$Pie\"</script>\n";
$tab=array();
$total_r=0;
$total_c=0;
switch ($Opcion){
	case "range":
		$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["cipar"]."&Opcion=rango&Formato=".$Formato;
		$query.="&from=".$arrHttp["Mfn"]."&to=".$arrHttp["to"];
		$IsisScript=$xWxis. "imprime.xis";
		include ("../common/wxis_llamar.php");
		LeerRegistros($contenido);
		break;
	case "search":
		$Expresion=urlencode($arrHttp["Expresion"]);
		$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["cipar"]."&Opcion=buscar&Formato=".$Formato;
		$query.="&Expresion=$Expresion";
		$IsisScript=$xWxis. "imprime.xis";
		include ("../common/wxis_llamar.php");
		LeerRegistros($contenido);
		break;
}
// PARA CONSTRUIR EL STRING QUE SE LE PASARÁ AL PROGRAMA QUE GRAFICA
$arreglo="";



//TABLAS DE CONTINGENCIA
if (isset($arrHttp["tables"]) or isset($arrHttp["cols"]) and isset($arrHttp["rows"]) ){
	//ENCABEZADO DE LA TABLA
	$ix=count($col);
	echo "<table border class=statTable cellpadding=5>";
	echo "<tr><th>&nbsp;</th><th colspan=$ix align=center>$tcol_tit</th><th>&nbsp;</th>";
	echo "<tr><th>$trow_tit</th>";
	ksort($col);
	ksort($row);

	foreach ($col as $y){
		if (empty($y)) $y=$msgstr["nodata"];
		echo "<th>$y</th>";
		$arreglo.="|$y";    // Título de las columnas
	}
	echo "<th>".$msgstr["total"]."</th>";
	$total_r=0;
	$total_c=array();
	// IMPRESION  DE LA TABLA
	foreach ($row as $x){
		if (empty($x)) 
			echo "<tr><td bgcolor=#ffffff>".$msgstr["nodata"]."</td>";
		else
			echo "<tr><td bgcolor=#ffffff>$x</td>";
		$total_r=0;
		$arreglo.="###$x".'|'; //Título de las filas
		foreach ($col as $y){
			if (!isset($tab[$x][$y])){
				$cell='&nbsp;';
				if (!isset($total_c[$y]))
					$total_c[$y]=0;
				$arreglo.="0".'|';  // valor de las columnas
			}else{
				$cell=$tab[$x][$y];
				if (!isset($total_c[$y]))
					$total_c[$y]=$cell;
				else
					$total_c[$y]+=$cell;
				$total_r+=$cell;
				$arreglo.=$cell.'|';
			}
			echo "<td align=center bgcolor=#ffffff>$cell</td>";
		}
		echo "<td align=center bgcolor=#ffffff>$total_r</td>";
	}
	echo "<tr><td bgcolor=#ffffff><strong>".$msgstr["total"]."</strong></td>";
	$total=0;
	foreach ($total_c as $cell) {
		if ($cell==""){
			$cell="&nbsp";
		}else{
			$total+=$cell;
		}
		echo "<td align=center bgcolor=#ffffff>$cell</td>";
	}
	echo "<td align=center bgcolor=#ffffff>$total</td>";
	echo "</table>";
}else{
//TABLAS DE FRECUENCIA
	//ENCABEZADO DE LA TABLA
	$ix=count($tab);
	echo "<table border class=statTable bgcolor=#eeeeee>";
	echo "<tr><th>$trow_tit</th>";
	ksort($tab);

	echo "<th>".$msgstr["total"]."</th>";
	$total=0;
	$arreglo='';
	// IMPRESION  DE LA TABLA
	foreach ($tab as $x=>$val){
		if (empty($x)) $x=$msgstr["nodata"];
		$arreglo.="###$x|$val"; //Título de las filas
		echo "<tr><td bgcolor=#ffffff>$x</td>";
		echo "<td align=center bgcolor=#ffffff>".$val."</td>";
        $total+=$val;

	}
	echo "<tr><td align=center bgcolor=#ffffff>".$msgstr["total"]."</td>";
	echo "<td bgcolor=#ffffff><strong>$total</strong></td>";
	echo "</table>";
}
echo "</div></div>";
echo "<dd>".$msgstr["sendto"].": ";
echo "<a href=javascript:SendTo(\"W\")>".$msgstr["wks"]."</a> &nbsp; | &nbsp; ";
echo "<a href=javascript:SendTo(\"D\")>".$msgstr["doc"]."</a> &nbsp; | &nbsp; ";
echo "<a href=javascript:SendTo(\"P\")>".$msgstr["prn"]."</a> &nbsp; | &nbsp; ";
echo "<a href=javascript:SendTo(\"AG\")>".$msgstr["ag"]."</a> &nbsp; | &nbsp; ";
//echo "<a href=javascript:SendTo(\"NAG\")>".$msgstr["nag"]."</a> &nbsp; | &nbsp; ";
echo "<p>";
?>
</div>
</center>
</form>

<!-- FORMA PARA ENVIAR LA SALIDA A UNA HOJA DE CALCULO O A UN DOCUMENTO-->
<form name=sendto action=sendto.php method=post target="STATS">
<input type=hidden name=base value=<?echo $arrHttp["base"]?>>
<input type=hidden name=html>
<input type=hidden name=Opcion>
<?php
if (isset($arrHttp["Expresion"]))
	echo "<input type=hidden name=Expresion value=\"".urlencode($arrHttp["Expresion"])."\">\n";
if (isset($arrHttp["tables"]))
	echo "<input type=hidden name=tables value=\"".urlencode($arrHttp["tables"])."\">\n";
if (isset($arrHttp["cols"]))
	echo "<input type=hidden name=cols value=\"".urlencode($arrHttp["cols"])."\">\n";
if (isset($arrHttp["rows"]))
	echo "<input type=hidden name=rows value=\"".urlencode($arrHttp["rows"])."\">\n";
if (isset($arrHttp["tables"]))
	echo "<input type=hidden name=tables value=\"".urlencode($arrHttp["tables"])."\">\n";
if (isset($arrHttp["Mfn"]))
	echo "<input type=hidden name=tables value=\"".$arrHttp["Mfn"]."\">\n";
if (isset($arrHttp["to"]))
	echo "<input type=hidden name=tables value=\"".$arrHttp["Mfn"]."\">\n";
echo "</form>";
//PIE DE PAGINA
include("../common/footer.php");

session_unregister('matriz');
$_SESSION['matriz'] = $arreglo;

?>
</body>
</html>

<?php

// SE LEEN LOS REGISTROS DE LA BASE DE DATOS
function LeerRegistros($contenido){
global $trow,$tcol,$row,$col,$tab;
	foreach ($contenido as $linea){
		if (!empty($linea)) {
			$t=explode('$$$$',$linea);
			if ($trow!="" and $tcol!=""){
				if (!isset($row[$t[0]])) $row[$t[0]]=$t[0];
				if (!isset($col[$t[0]])) $col[$t[1]]=$t[1];
				if (!isset($tab[$t[0]][$t[1]]))
					$tab[$t[0]][$t[1]]=1;
				else
					$tab[$t[0]][$t[1]]++;
			}else{
				if ($trow!=""){
					if (!isset($tab[$t[0]]))
						$tab[$t[0]]=1;
					else
						$tab[$t[0]]++;
				}else{
					if (!isset($tab[$t[1]]))
						$tab[$t[1]]=1;
					else
						$tab[$t[1]]++;
				}
			}
		}
	}
}

// SE CONSTRUYE EL FORMATO PARA LA TABLA DE FRECUENCIA
function Frecuencia($rc){
global $arrHttp,$tab_vars,$ttit,$trow,$trow_tit;
	$rc=stripslashes($rc);
	$tabla=explode('|',$rc);
	$ttit=$tabla[0];
	$trow=$tabla[1];
	$trow_tit=$tabla[0];
	$Formato=$tabla[1]."'$$$$'/";
	echo "<H4>$ttit</H4>";
	return ($Formato);
}

// SE CONSTRUYE EL FORMATO PARA LA TABLA DE CONTINGENCIA
function Contingencia(){
global $arrHttp,$tab_vars,$ttit,$trow,$trow_tit,$tcol,$tcol_tit;
// SE LEE LA LISTA DE VARIABLES PARA FORMAR LA TABLA
	$tabla=explode('|',$arrHttp["tables"]); //[0]=NOMBRE DE LA TABLA, [1]=VARIABLE FILAS , [2]=VARIABLE COLUMNAS
	$ttit=$tabla[0];    // TITULO DE LA TABLA
	$trow="";           // VARIABLE DE LAS FILAS
	$trow_tit="";       // TITULO DE LAS FILAS
	$tcol="";           // VARIABLE DE LAS COLUMNAS
	$tcol_tit="";       // TITULO DE LAS COLUMNAS
	foreach ($tab_vars as $value) {
		$value=trim($value);
		if ($value!=""){
			$t=explode('|',$value);
			if ($tabla[1]== $t[0]){
				$trow=$t[1];
				$trow_tit=$t[0];
			}else{
				if (isset($tabla[2]) and $tabla[2]!=""){
					if ($tabla[2]== $t[0]){
						$tcol=$t[1];
						$tcol_tit=$t[0];
					}
				}
			}
		}
	}
	echo "<H4>$ttit</H4>";
	$Formato=$trow."'$$$$'".$tcol."'$$$$'/";
	return $Formato;
}
