<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      borrar_prestamo_presentar.php
 * @desc:      Show loan delete
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

// rutina para almacenar los préstamos otorgados,las renovaciones y las devoluciones

global $REQUEST_METHOD, $HTTP_POST_VARS, $HTTP_GET_VARS;

$valortag = Array();
$arrHttp=Array();
include("leerarchivoini.php");
include ('leerregistroisispft.php');

function ExistenciasRegex($xNoinv){
global $arrHttp,$OS,$pathwxis,$xWxis,$totale;
	$Expresion="NI=$xNoinv";
//	if (isset($_SESSION["centro"]) and $_SESSION["centro"]!="*****")
//	 $Expresion.="*".$_SESSION["centro"];
	$arrHttp["Opcion"]="busquedalibre";
	$Expresion=urlencode(trim($Expresion));
	$query = "?xx=  "."&base=regex&cipar=marc.par&Expresion=".$Expresion."&Opcion=".$arrHttp["Opcion"]."&Formato=p";
	if (isset($_SESSION["centro"]) and $_SESSION["centro"]!="*****") $query.="&centro=".$_SESSION["centro"];
	$script="wxis/regex.xis";
	putenv('REQUEST_METHOD=GET');
	putenv('QUERY_STRING='.$query);
	$contenido="";
	if (stristr($OS,"WIN")==false){
		exec("./wxis.exe IsisScript=".$script,$contenido);
	}else{
		exec("wxis.exe IsisScript=".$script,$contenido);
	}
	$regex=array();
	$linea="";
	foreach($contenido as $value) {
	//	echo $value;
		if (!empty($value))  $linea.=$value;
	}
	//echo "$linea";
	$a=explode('##',$linea);
	foreach ($a as $value){
		$value=trim($value);
		$ixpos=strpos($value,'|');
		$llave=trim(substr($value,0,$ixpos));
		$e=trim(substr($value,$ixpos+1));
		if ($e!=""){
			if (!empty($llave)) $totale++;
			if (isset($regex[$llave])){
				$regex[$llave].=$e;
			}else{
				$regex[$llave]=$e;
			}
		}
	}
	ksort($regex);
	return ($regex);
}

Function Iso2Fecha($fecha){
	$f=substr($fecha,6,2)."/".substr($fecha,4,2)."/".substr($fecha,0,4);
	return $f;
}

function compareDate ($FechaP)
{
$exp_date = $FechaP;
$todays_date = date("Y-m-d");

$today = strtotime($todays_date);
$expiration_date = strtotime($exp_date);

	return $expiration_date-$today;

}//end Compare Date



// ------------------------------------------------------

foreach ($HTTP_GET_VARS as $var => $value) {
	if (!empty($value)) $arrHttp[$var]=$value;
	}
if (count($arrHttp)==0){
	foreach ($HTTP_POST_VARS as $var => $value) {
		$arrHttp[$var]=$value;
	}
}

//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";

// se conoce el no. de inventario y se quiere ver que usuario lo tiene
if ($arrHttp["Opcion"]=="ubicausuario") {
    $query = "?xx=&base=presta&cipar=cipres.par&Expresion=".$arrHttp["Expresion"]. "&Opcion=ubicausuario";
	putenv('REQUEST_METHOD=GET');
	putenv('QUERY_STRING='.$query);
	$contenido="";
	if (stristr($OS,"win")==false){
		exec("./wxis.exe IsisScript=wxis/cipres.xis ",$contenido);
	}else{
		exec("wxis.exe IsisScript=wxis/cipres.xis ",$contenido);
	}
	foreach ($contenido as $linea){
		if (substr($linea,0,10)=='$$USUARIO:')
			$arrHttp["usuario"]=trim(substr($linea,10));
		else
			echo "$linea<br>";
	}
	if (!isset($arrHttp["usuario"]) or $arrHttp["usuario"]==""){
		echo "<h4>No se encontró el número de inventario en la base de datos de préstamos</h4>";
		die;
	}
	$arrHttp["Opcion"]="prestamousuario";
}

if (isset($arrHttp["libro"])) {
    $catalogo=explode('|',$arrHttp["libro"]);
}


$salida_prestamos="";


?>
<html>
<title>Prestamo</title>
<link rel="stylesheet" href="js/style01.css" type="text/css">
<link rel="stylesheet" href="../styles/basic.css" type="text/css">
<script language=Javascript src=js/prestamo.js></script>
<script language=javascript src=js/popcalendar.js></script>
<style type="text/css">

.myLayersClass { position: relative; visibility: hidden; }

</style>

<script>

var Feriados= new Array(12)
<?
// Se lee la tabla de feriados

$archivo=$db_path."bases/users/def/feriados.dat";
$fp=file($archivo);
if (!$fp) {
    echo "Falta el archivo ".$archivo;
	exit;
}
$i=-1;

foreach($fp as $linea){
	$i++;
	$linea=trim($linea);
	if ($i==0) {
	    echo "Ntb=\"$linea\"\n";
	}else{
		echo "Feriados[$i]=\"$linea\"\n";
	}
}

// Se lee la tabla de tipos de préstamos

$archivo=$db_path."bases/users/def/prestamo.tab";
$fp=file($archivo);
if (!$fp) {
    echo "Falta el archivo ".$archivo;
	exit;
}
$i=-1;
echo "var TipoP = new Array()\n";
foreach($fp as $linea){
	$i++;
	$linea=trim($linea);
	if ($i>2 && $linea!="") {
		$j=$i-3;
		echo "TipoP[$j]=\"$linea\"\n";
	}
}

?>
	userid="<?php echo  $arrHttp["userid"]?>"
	usuario="<?php echo  $arrHttp["usuario"]?>"



</script>
<body>
<form name=Prestar method=post action=prestamo_actualizar.php onsubmit="javascript:return false" >
<input type=hidden name=Expresion value="<?php echo $arrHttp["Expresion"]?>">
<input type=hidden name=base value=presta>
<input type=hidden name=cipar value=cipres.par>
<input type=hidden name=datoslibro value="<?php echo  $catalogo[2]?>">
<input type=hidden name=Opcion value=prestar>
<input type=hidden name=userid value=<?php echo $_SESSION["login"]?>>
<input type=hidden name=fpreiso value="">
<input type=hidden name=fdeviso value="">
<input type=hidden name=usuario value="<?php echo $arrHttp["usuario"]?>">
<input type=hidden name=Formato value=prestar>
<input type=hidden name=ejemp value="">
<input type=hidden name=centro value="<?php echo $_SESSION["centro"]?>">
<input type=hidden name=inventario value="">
<?
// se presenta la  información del usuario

   	$query = "?xx=  &usuario=".$Expresion."&base=users" ."&cipar=".$arrHttp["cipar"]."&userid=".$arrHttp["userid"]."&Expresion=".$arrHttp["usuario"]."&from=" . "&Opcion=".$arrHttp["Opcion"];
	putenv('REQUEST_METHOD=GET');
	putenv('QUERY_STRING='.$query);
	$contenido="";
	if (stristr($OS,"win")==false){
		exec("./wxis.exe IsisScript=wxis/cipres.xis ",$contenido);
	}else{
		exec("wxis.exe IsisScript=wxis/cipres.xis ",$contenido);
	}
	echo "<table width=100% border=0 cellpadding=0 cellspacing=0><td>";
	foreach ($contenido as $linea){
		echo $linea."\n";
	}
	echo "</td><td align=right>
	<div id = div1 class = myLayersClass align=center>";
	if (strpos(trim($linea),"Usuario no existe")===false){
?>

		<table border=1 cellpadding=0 cellspacing=0 bordercolor=darkred width=95%>
			<tr>
				<td class=td5p colspan=2>Prestar</td>
			</tr>
			<tr>
        		<td class=td_N>No. inventario: <input type=text name=inven class=td4_S onKeyPress="javascript:codes(event,1)"
		<?php if (isset($arrHttp["inven"])) echo "value=".$arrHttp["inven"]?>>
			<a href=javascript:BuscarLibro("I")>
			<img src=img/buscarlupa.gif border=0 align=center alt="Buscar"></a>
				<br>
				No.Clasificación<input type=text name=signa class=td4_S  onKeyPress="codes(event,2)"
		<?php if (isset($arrHttp["signa"])) echo "value=".$arrHttp["signa"]?>>
		<a href=javascript:BuscarLibro("S")>
			<img src=img/buscarlupa.gif border=0 alt="Buscar" align=center></a>
		</td>

		<td align=center valign=top width=90>
			<a href=Javascript:BuscarCatalogo()>
				<img src=img/books06.gif align=center border=0 alt="Buscar"><br><font size=1>Catálogo</a>
		</td>
	</tr>
	</table>

<?}?>
</div>
</td></table>

<?

//	foreach ($prestamo as $linea) echo "$linea<br>";

// se leen los prestamos pendientes
   	$query = "?xx=  &usuario=".$Expresion."&base=presta&cipar=cipres.par&userid=".$arrHttp["userid"]."&Expresion=".$arrHttp["usuario"]."-P&from=" . "&Opcion=libros_prestados&Path=".$arrHttp["Path"];
	putenv('REQUEST_METHOD=GET');
	putenv('QUERY_STRING='.$query);
	$contenido="";
	if (stristr($OS,"win")==false){
		exec("./wxis.exe IsisScript=wxis/cipres.xis ",$contenido);
	}else{
		exec("wxis.exe IsisScript=wxis/cipres.xis ",$contenido);
	}
	foreach ($contenido as $linea){
		$prestamos[]=$linea;
	}
	if (count($prestamos)>0) {
		$salida_prestamos="<table border=0 width=100%><tr><td colspan=2><br>";
		if (!isset($arrHttp["libro"])){
			$salida_prestamos.="<dd><a href=javascript:Procesar('devolver') class=td5a>Devolver</a> &nbsp; &nbsp;
				<a href=javascript:Procesar('renovar') class=td5a>Renovar</a>";
		}
		$salida_prestamos.="	<dd><font class=td>Préstamos pendientes:</font></dd><dd><table width=95% bgcolor=#cccccc class=td> ";
		$salida_prestamos.="<td> </td><td>Centro</td><td>Ejemplar</td><td>Signatura</td><td>Título</td><td>T.pre</td><td>F.prestamo</td><td>F.Devoluc.</td>";
		$np=-1;   //número de préstamos
		$nv=0;    //número de préstamos vencidos
		foreach ($prestamos as $linea) {
			$p=explode("\^",$linea);
			$np++;
			$p[4]=Iso2Fecha($p[4]);
			$p[5]=Iso2Fecha($p[5]);

			$dif= compareDate ($p[5]);
	//		echo $dif;
			$fuente="";

			if ($dif<0) {
				$nv++;    //cuenta de préstamos vencidos
			    $fuente="<font color=red>";
			}
//		v800^c,'^',v800^q,'^',v800^n," Ej."v800^l,'^',v800^t,'^',v800^p,'^',v800^h,'^',v800^o,'^',f(mfn,1,0),/
			echo "<script>
					ppu[$np]=new Array()
					ppu[$np][0]=$p[7]         //mfn
					ppu[$np][1]=\"$p[1]\"     //Centro
					ppu[$np][2]=\"$p[2]\"    //No. inventario
					ppu[$np][3]=\"$p[0]\"    //cota
					ppu[$np][4]=\"$p[3]\"    //descripcion
					ppu[$np][5]=\"$p[6]\"    //tipo de material
					ppu[$np][6]=\"$p[4]\"    //Fecha de préstamo
					ppu[$np][7]=\"$p[5]\"    //Fecha de devolución
					ppu[$np][8]=0         //dias que faltan para el vencimiento
				   </script>\n";

			$salida_prestamos.= "<tr><td  bgcolor=white>";
			if (!isset($arrHttp["libro"])  )   //and $p[1]==$_SESSION["centro"]
				$salida_prestamos.="<input type=checkbox name=chkPr value=$np>";
			else
				$salida_prestamos.="&nbsp;";
			$salida_prestamos.= "</td>

				<td bgcolor=white nowrap>".$p[1]."</td><td bgcolor=white nowrap>".$p[2]."</td><td bgcolor=white>". $p[0]. "</td><td bgcolor=white>".$p[3]."</td><td width=50 bgcolor=white>".$p[6]."</td><td bgcolor=white nowrap>".$p[4]."</td><td width=50 bgcolor=white nowrap>$fuente".$p[5]."</td></tr>";

		}
		$salida_prestamos.= "</table></dd><br>";
		$salida_prestamos.= "<script>
		np=$np
		nv=$nv
		</script>\n";
	}

?>

<?


if ($np==0 || $np>0 and $nv==0){
?>


<?php if (isset($catalogo[2])) {
	echo "<table><tr><td colspan=2><font class=td3><dd>";
	if ($arrHttp["Opcion"]=="Renovar") {
		echo "Renovar";
	}else{
		echo "Prestar";
	}
	echo "</font><br>
		<dd><table border=0 cellpadding=0 cellspacing=0 width=95%>
		<td colspan=4 bgcolor=#FFFACD class=td>Datos del libro</td>";

	echo "<tr><td colspan=4><font color=darkred size=1>";
	if ($catalogo[6]==0 ) {
		echo "El material solicitado no existe en la base de datos";
		echo "<script>show(true,'div1');</script>";
	}else{
		if (!empty($catalogo[0])) {
	    	echo "No. de inventario: ".$catalogo[0];
			echo "<input type=hidden name=xinven value='".$catalogo[0]."'> ";
		}
		if (!empty($catalogo[1])) {
	    	echo "No.clasificación: ".$catalogo[1]."<br>";
			echo "<input type=hidden name=cota value='".$catalogo[1]."'> ";
		}

		echo $catalogo[4]."<br>";

		if ($arrHttp["Opcion"]!="Renovar") {
			$totale=0;
			$regex=ExistenciasRegex($catalogo[0]);
			echo "<font color=black>Ejemplares: ".$totale." &nbsp &nbsp; &nbsp;<img src=img/delete.gif> Prestados: ".$catalogo[5];
			echo " &nbsp; &nbsp; Para seleccionar un ejemplar, haga clic sobre la casilla <input type=radio> que lo precede";
			echo "<table bgcolor=linen width=100% cellpadding=2>";
			foreach ($regex as $value) {
				echo $value;
			}

		    echo "</table>";
			if ($totale==$catalogo[5]) echo "<font color=black> <i><u>No hay ejemplares disponibles</i>";
		}


		echo "</td></tr>";
	}
	echo "</table>";
}
if ( $totale>0 or !isset($catalogo[5])) {

?> <script>show(true,'div1');</script>

<?}
  if (isset($catalogo[2]) and $totale-$catalogo[5]>0) {
?>
	<dd><table border=0 cellpadding=0 cellspacing=0 width=95% bgcolor=linen>
	<tr>
        <td colspan=6 bgcolor=#eeeeee class=td>

<?
if ($arrHttp["Opcion"]=="Renovar") {
	echo "Datos de la Renovación";
}else{
	echo "Datos del Préstamo";
}?>
</td>
	<tr>
		<td class=td_N>Tipo de préstamo</td>
		<td><select class=td name=tp onChange=javascript:Vencimiento()>
			<script>
			document.writeln("<option value=''> </option>")
			for (i=0;i<=TipoP.length-1;i++){
				xsel="";
				if (i==1) xsel=" selected"
				document.writeln("<option value=\""+TipoP[i]+"\""+xsel+">"+TipoP[i].substr(5,20)+" ["+TipoP[i].substr(27,2)+" "+TipoP[i].substr(25,2)+"]")
			}

			</script>

			</select>
		</td>
		<td class=td_N>	Fecha de préstamo</td>
		<td><input type=text name=fp class=td size=10 onBlur="javascript:Vencimiento()" onFocus="javascript:popUpCalendar(document.Prestar.fp, fp, 'dd/mm/yyyy',1);return false" value=
			<?$today=getdate();
			$dia=$today["mday"];
			if ($dia<10) $dia="0".$dia;
			$mes=$today["mon"];
			if ($mes<10) $mes="0".$mes;
			echo $dia."/".$mes."/".$today["year"];?>><a href="" onClick="javascript:popUpCalendar(this, fp, 'dd/mm/yyyy',1);return false"><img src=img/calendar.gif border=0 width=34 height=21 align=ABSBOTTOM ></a>

		</td>
		<td colspan=2>&nbsp; &nbsp; </td>
	<tr>
		<td colspan=2></td>
		<td class=td_N>Fecha devolución</td>
		<td><input type=text name=fd class=td  value="" size=10></td>
		<td><a href=javascript:EnviarPrestamo()><img src=img/prestar.gif alt=prestar align=middle border=0></a></td>
		<td><a href=javascript:CancelarPrestamo()><img src=img/cancelarprestamo.gif alt=cancelar align=middle border=0></a></td>

	</table>
<script>
fechaDia=document.Prestar.fp.value
Vencimiento()
</script>
<?  }


}
	echo $salida_prestamos;
?>


</form>


<br><br>
<font size=1>prestamo_presentar
</body>
</html>