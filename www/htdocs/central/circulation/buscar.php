<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      buscar.php
 * @desc:      Search the loans databases
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
if (!isset($_SESSION["login"])) die;
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";

include("../config.php");
$lang=$_SESSION["lang"];
include("../lang/prestamo.php");


//include ("leerregistroisispft.php");

include("../lang/admin.php");
// Busqueda libre
function EjecutarBusqueda(){
global $arrHttp,$OS,$db_path,$xWxis,$tagisis,$Wxis;
		$vienede=$arrHttp["Opcion"];
		if ($arrHttp["Opcion"]!="continuar"){
			$Expresion=PrepararBusqueda();
		}else{
		 	$Expresion=$arrHttp["Expresion"];
			$arrHttp["Opcion"]="busquedalibre";
		}
		$Expresion=urlencode(trim($Expresion));
		if ($arrHttp["desde"]!="dataentry"){
			if (!isset($arrHttp["from"])) $arrHttp["from"]=1;
			if (!isset($arrHttp["Mfn"])) $arrHttp["Mfn"]=$arrHttp["from"];
			if (isset($arrHttp["from"]))$arrHttp["count"]=1;
			if (!isset($arrHttp["Formato"]))$arrHttp["Formato"]="ALL";
			$Formato=$arrHttp["Formato"];
			if ($Formato!="ALL" ){
				$Formato=$db_path.$arrHttp["base"]."/www/".$Formato;
			}
			$query = "?xx=  "."&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["cipar"]."&Expresion=".$Expresion."&Opcion=".$arrHttp["Opcion"]."&count=".$arrHttp["count"]."&Mfn=".$arrHttp["Mfn"]."&Formato=$Formato";
	//			echo $query;
			putenv('REQUEST_METHOD=GET');
			putenv('QUERY_STRING='.$query);
			$contenido="";
	// Ruta al directorio donde se encuentra wwwisis
			exec("\"".$Wxis."\" IsisScript=$xWxis"."buscar.xis",$contenido);
			foreach ($contenido as $linea){
				if (!empty($linea)) {
		 		    echo "$linea\n";
				}
			}
		}else{
			if ($vienede=="buscar_en_este"){
				echo "<script>
						window.opener.top.browseby=\"search\"
						window.opener.top.Expresion=\"".$Expresion."\"
						window.opener.top.mfn=1
						window.opener.top.Menu(\"ejecutarbusqueda\");
						self.close()
					</script>
				";
			}else{
				echo "<script>
						top.browseby=\"search\"
						top.Expresion=\"".$Expresion."\"
						top.mfn=1
						top.Menu(\"ejecutarbusqueda\");
					</script>
				";
			}
		}
}

function PrepararBusqueda(){
global $arrHttp;
	if (!isset($arrHttp["id"])) $arrHttp["id"]="";
	$expresion=explode(" ~~~ ",$arrHttp["Expresion"]);
	$campos=explode(" ~~~ ",$arrHttp["id"]);
	if (isset($arrHttp["Operadores"])){
		$operadores=explode(" ~~~ ",$arrHttp["Operadores"]);
	}
	// se analiza cada sub-expresion para preparar la fórmula de búsqueda
	$nse=-1;
	for ($i=0;$i<count($expresion);$i++){
		$expresion[$i]=trim($expresion[$i]);
		if ($expresion[$i]!=""){
			$formula=str_replace("  "," ",$expresion[$i]);
			$subex=Array();
			if (!empty($campos[$i]) and trim($campos[$i])!="---"){
				$id="/(".trim($campos[$i]).")";
			}else{
				$id="";
			}
			$xor=$id."¬or¬";
			$xand=$id."¬and¬";
			$formula=str_replace (" or ", $xor, $formula);
			$formula=str_replace (" and ", $xand, $formula);
			$formula=str_replace ('\"', '"', $formula);

			$formula=stripslashes($formula);
			while (is_integer(strpos($formula,'"'))){
				$nse=$nse+1;
				$pos1=strpos($formula,'"');
				$xpos=$pos1+1;
				$pos2=strpos($formula,'"',$xpos);
				$subex[$nse]=trim(substr($formula,$xpos,$pos2-$xpos));
				if ($pos1==0){
					$formula="{".$nse."}".substr($formula,$pos2+1);
				}else{
					$formula=substr($formula,0,$pos1-1)."{".$nse."}".substr($formula,$pos2+1);
				}
			}
			$formula=str_replace (" ", "(F)", $formula);

			while (is_integer(strpos($formula,"{"))){
				$pos1=strpos($formula,"{");
				$pos2=strpos($formula,"}");
				$ix=substr($formula,$pos1+1,$pos2-$pos1-1);
				if ($pos1==0){
					$formula=$subex[$ix].substr($formula,$pos2+1);
				}else{
					$formula=substr($formula,0,$pos1)." ".$subex[$ix].substr($formula,$pos2+1);
				}
			}
			$formula=str_replace ("¬", " ", $formula);
			$expresion[$i]=trim($formula).$id;
		}
	}
	$formulabusqueda="";
	for ($i=0;$i<count($expresion);$i++){
		if (!empty($expresion[$i])) {
			$formulabusqueda=$formulabusqueda." (".$expresion[$i].")";
			$resto="";
			for ($j=$i+1;$j<count($expresion);$j++){
				$resto=$resto.trim($expresion[$j]);
			}
			if (!empty($resto)) $formulabusqueda=$formulabusqueda." ".$operadores[$i];
		}
	}
	return $formulabusqueda;

}


function DibujarFormaBusqueda(){
global $arrHttp,$camposbusqueda,$db_path,$tagisis,$msgstr;

include("../common/header.php");



// Preparación de la forma de búsqueda


?>

<script languaje=javascript>

function Ayuda(){
	msgwin=window.open("../html/ayuda_expresion.html")
}
function switchMenu(obj) {
	var el = document.getElementById(obj);
	if ( el.style.display != "none" ) {
		el.style.display = 'none';
	}
	else {
		el.style.display = '';
	}
}

function LeerExpresion(TipoR,Obj){
	var el = document.getElementById('Expre')
	el.style.display = 'none';
	var el = document.getElementById('myvar')
	el.style.display = '';
	url="busqueda_leer.php?base=<?php echo $arrHttp["base"]?>&tipor="+TipoR+"&obj="+Obj
	msgwin=window.open(url,"busqueda","menu=no,status=yes")
	msgwin.focus()
}

function Buscar_Expre(){
	document.forma1.Expresion.value=document.forma1.Expre_b.value
<?	if (!isset($arrHttp["Target"])){
		echo "msgwin=window.open(\"\",\"Resultados\",\"status=yes,resizable=yes,toolbar=no,menu=no,scrollbars=yes,width=630,height=400\")
		msgwin.focus()\n";
	}
?>
	document.forma1.submit()
}

function LimpiarBusqueda() {
  for (i=0; i<document.forma1.camp.length; i++){
      document.forma1.expre[i].value=""
      }
}


function PrepararExpresion(){
	j=document.forma1.expre.length

	Expresion=""
	Operadores=""
	Campos=""
	for (i=0;i<j;i++){
	    if (document.forma1.expre[i].value==""){
			document.forma1.expre[i].value=" "
		}
		if (Expresion==""){
			Expresion=document.forma1.expre[i].value
		}else{
			if (document.forma1.expre[i].value==" "){
				Expresion=Expresion+" ~~~ "
				document.forma1.expre[i].value=""
			}else{
				Expresion=Expresion+" ~~~ "+document.forma1.expre[i].value
			}
		}
		ixSel=document.forma1.camp[i].selectedIndex
		cc=document.forma1.camp[i].options[ixSel].value
		if (Campos==""){
			Campos=cc
		}else{
			Campos=Campos+" ~~~ "+cc
		}
		if (i<j-1){
			ixSel=document.forma1.oper[i].selectedIndex
			if (ixSel=="undefined") ixSel=0
			cc=document.forma1.oper[i].options[ixSel].value
			if (Operadores==""){
				Operadores=cc
			}else{
				Operadores=Operadores+" ~~~ "+cc
			}
		}
	}
	document.forma1.Expresion.value=Expresion
	document.forma1.Campos.value=Campos
	document.forma1.Operadores.value=Operadores
<?
	if (isset($arrHttp["Target"])){
		echo "msgwin=window.open(\"\",\"Resultados\",\"status=yes,resizable=yes,toolbar=no,menu=no,scrollbars=yes,width=630,height=400\")
		msgwin.focus()\n";
	}
?>
	for (i=0;i<j;i++){
		if (document.forma1.expre[i].value==" ") document.forma1.expre[i].value=""
	}
	SinExpre="S"
	for (i=0;i<j;i++){
		if (document.forma1.expre[i].value!="") SinExpre="N"
	}

	if (SinExpre=="S"){
		alert("<?php echo $msgstr["faltaexpr"]?>")
	}else{
		if (document.forma1.Tabla.value=="s") document.forma1.Opcion.value="actualizarportabla"
  		if (document.forma1.Tabla.value=="cGlobal") document.forma1.Opcion.value="cGlobal"
  		if (document.forma1.Tabla.value=="imprimir") document.forma1.Opcion.value="imprimir"
  		if (document.forma1.Tabla.value=="reportes") document.forma1.Opcion.value="reportes"
		document.forma1.submit()
	}
}


function Diccionario(jx){
	j=document.forma1.camp[jx].selectedIndex
	if (document.forma1.camp[jx].options[j].value=="---"){
		alert("<?php echo $msgstr["selcampob"]?>")
		return
	}

<?php
	echo "document.diccio.base.value=\"".$arrHttp['base']."\"\n";
	echo "document.diccio.cipar.value=\"".$arrHttp['cipar']."\"\n";


?>
	document.diccio.Opcion.value="diccionario";
	nombrec=dt[j][0]
    prefijo=dt[j][2]
	if (prefijo=="--"){
		alert("<?php echo $msgstr["sinindice"]?>")
	}else{
		msgwin=window.open("","Diccionario","status=yes,resizable=yes,toolbar=no,menu=no,scrollbars=yes,height=400")
		msgwin.focus()
		id=dt[j][1]
		document.diccio.campo.value=escape(nombrec)
		document.diccio.prefijo.value=prefijo
		document.diccio.id.value=id
		document.diccio.Diccio.value=jx
		document.diccio.submit()
	}

}


</script>
<?php
	echo "<script>\n";
	echo "var dt= new Array()\n";
	$ix=0;
	echo "dt[".$ix."]= new Array \n";
	echo "dt[".$ix."][0]=\"---\"\n";
	echo "dt[".$ix."][1]=\"\"\n";
	echo "dt[".$ix."][2]=\"\"\n";

	foreach ($camposbusqueda as $linea) {
		$ix=$ix+1;
		$l=explode('|',$linea);
		$parte2=trim(substr($linea,39));
		$ixpos=strpos($parte2," ");
		$parte3=$l[2];
		$parte2=$l[1];
		$parte1=$l[0];
		echo "dt[".$ix."]= new Array \n";
		echo "dt[".$ix."][0]=\"".$parte1."\"\n";
		echo "dt[".$ix."][1]=\"".$parte2."\"\n";
		echo "dt[".$ix."][2]=\"".$parte3."\"\n";
	}
	$Tope=$ix+1;  //significa que se van a colocar 7 cajas de texto con la expresión de búsqueda
	echo "</script>\n";
	echo "<body>";
	include("../common/institutional_info.php");
	echo "
		<div class=\"sectionInfo\">
			<div class=\"breadcrumb\">".
				$msgstr["users"].". ".$msgstr["adsearch"]."
			</div>
			<div class=\"actions\">\n";

				echo "<a href=\"browse.php?encabezado=s\" class=\"defaultButton backButton\">
					<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
					<span><strong>". $msgstr["back"]."</strong></span>
				</a>
			</div>
			<div class=\"spacer\">&#160;</div>
		</div>
		<div class=\"middle form\">
			<div class=\"formContent\">";

	echo "<form method=post name=forma1 action=buscar.php  \"onSubmit=Javascript:return false\" >";

	echo '<input type=hidden name=tag8999 value="">';
	echo "<input type=hidden name=desde value=".$arrHttp['desde'].">\n";
	echo "<input type=hidden name=base value=".$arrHttp['base'].">\n";
	echo "<input type=hidden name=cipar value=".$arrHttp['cipar'].">\n";
    echo "<input type=hidden name=from value=1>\n";
	if (isset($arrHttp["Formato"]))echo "<input type=hidden name=Formato value=".$arrHttp['Formato'].">\n";
	echo "<input type=hidden name=count value=".$arrHttp['count'].">\n";
	echo "<input type=hidden name=Opcion value=".$arrHttp['Opcion'].">\n";
	if (isset($arrHttp["prologo"])) echo "<input type=hidden name=prologo value=".$arrHttp['prologo'].">\n";
	if (isset($arrHttp["epilogo"])) echo "<input type=hidden name=epilogo value=".$arrHttp['epilogo'].">\n";
	echo "<input type=hidden name=Campos value=\"\">\n";
	echo "<input type=hidden name=Operadores value=\"\">\n";
	echo "<input type=hidden name=Expresion value=\"\">\n";
	if (isset($arrHttp["prestamo"])) echo "<input type=hidden name=prestamo value=\"".$arrHttp['prestamo']."\">\n";
	echo "<br>\n<center>\n";
	echo "	<table border=0 width=700 valign=center cellpadding=0 Cellspacing=0 >\n";

	echo "			<table border=0 width=700 valign=center cellpadding=0 cellspacing=0 >\n";
	echo "			<tr>\n";
	echo "				<td class=td align=center background=../dataentry/img/fondo0.jpg>\n";
	echo $msgstr["mensajeb"];
	echo "				</td></tr></div>
					</table>\n";

	echo "
					<table border=0 width=610 valign=center cellpadding=0 cellspacing=2>";
	echo "			<tr><td bgcolor=#cccccc><strong>".$msgstr["campo"]."</b></td>";
	echo "				<td bgcolor=#cccccc colspan=3><strong>&nbsp; ".$msgstr["expresion"]."</td></tr>\n";
	for ($jx=0;$jx<$Tope;$jx++){
		echo "<tr><td  valign=center>";
		echo "<SELECT name=camp>";
		$asel="";
		echo "<OPTION value=\"---\">---";
		for ($i=0;$i<count($camposbusqueda);$i++){

			$linea=$camposbusqueda[$i];
			$l=explode('|',$linea);
			$parte3=$l[2];
			$parte2=$l[1];
			$parte1=$l[0];
			echo "<OPTION value=".$parte2.$asel;
			if ($i==$jx) echo " selected ";
			echo ">".$parte1;
		}
		echo "</SELECT></td>";
		echo "<td> &nbsp;<input type=text size=70 name=expre value=\"\"> &nbsp;</td>";
		if ($jx<$Tope-1){
       		echo "<td><select name=oper size=1>";
       		echo "<option value=and selected>and\n";
       		echo "<option value=or>or\n";
       		echo "<option value=^>and not\n";
       		echo "</select></td>\n";
 		}else {
       		echo "<td><input type=hidden name=oper></td>";
    	}
    	echo "<td><a href=\"javascript:Diccionario(".$jx.")\">".$msgstr["indice"]."</a>\n";
   		echo "</td>\n";

	}
	echo "<tr>\n";
	echo "<td colspan=3 align=center><p><br>\n";
	?>
	<div class="sectionButtons">
		<a href="javascript:PrepararExpresion()" class="defaultButton multiLine listButton">
		<img src="../images/mainBox_iconBorder.gif" alt="" title="" />
							<span><?php echo $msgstr["m_buscar"]?></strong></span>
						</a>
		<a href="javascript:LimpiarBusqueda(this,1)" class="defaultButton multiLine listButton">
		<img src="../images/mainBox_iconBorder.gif" alt="" title="" />
							<span><?php echo $msgstr["borrar"]?></strong></span>
						</a>
	</div>
	<?
	echo "</td>\n";
	echo "</table>\n";
	echo "</form>\n";
	echo "<form name=diccio method=post action=../dataentry/diccionario.php target=Diccionario>\n";

	echo "<input type=hidden name=base value=".$arrHttp['base'].">\n";
	echo "<input type=hidden name=cipar value=".$arrHttp['cipar'].">\n";
	echo "<input type=hidden name=Formato value=".$arrHttp['Formato'].">\n";
	echo "<input type=hidden name=Opcion value=buscar>\n";
	echo "<input type=hidden name=prefijo value=\"\">\n";
	echo "<input type=hidden name=campo value=\"\">\n";
	echo "<input type=hidden name=id value=\"\">\n";
	echo "<input type=hidden name=Diccio value=\"\">\n";
	echo "<input type=hidden name=desde value=".$arrHttp["desde"].">";
	if (isset($arrHttp["epilogo"])) echo "<input type=hidden name=epilogo value=".$arrHttp['epilogo'].">\n";
	if (isset($arrHttp["prologo"])) echo "<input type=hidden name=prologo value=".$arrHttp["prologo"].">\n";
	if (isset($arrHttp["prestamo"])) {
	    echo "<input type=hidden name=prestamo value=".$arrHttp['prestamo'].">\n";
	}
	if (isset($arrHttp["Target"])) {
	    echo "<input type=hidden name=Target value=".$arrHttp['Target'].">\n";
	}
	if (isset($arrHttp["Tabla"])) {
	    echo "<input type=hidden name=Tabla value=".$arrHttp['Tabla'].">\n";
	}
	echo "</form>\n";
    echo "</div></div></center>\n";
    include("../common/footer.php");
	echo "</body>";
	echo "</html>";

}




//include 'leerregistroisispft.php';

// ------------------------------------------------------
// INICIO DEL PROGRAMA
// ------------------------------------------------------

$host="";
$arrHttp=Array();

foreach ($HTTP_GET_VARS as $var => $value) {
		if (!empty($value)) $arrHttp[$var]=$value;
	}
if (count($arrHttp)==0){
	foreach ($HTTP_POST_VARS as $var => $value) {
		$arrHttp[$var]=$value;
	}
}
if (!isset($arrHttp['Tabla'])) $arrHttp["Tabla"]="";

//foreach ($arrHttp as $var => $value) 	echo "$var = $value<br>";

if (isset($arrHttp["prestamo"])) {
	    $arrHttp["Target"]="N";
	}

if (!isset($arrHttp["prologo"])) {
	    $arrHttp["prologo"]="p";
	}
if (!isset($arrHttp["desde"])) $arrHttp["desde"]="";
if (!isset($arrHttp['count'])) $arrHttp["count"]="10";
// Se carga la tabla con las opciones de búsqueda

	$a= $db_path.$arrHttp["base"]."/www/camposbusqueda.tab";
	$fp=file_exists($a);
	if (!$fp){
		echo "<br><br><h4><center>".$msgstr["faltacamposbusqueda"]."</h4>";
		die;
	}
	$fp = fopen ($a, "r");
	$fp = file($a);
	foreach ($fp as $linea){		$linea=trim($linea);		if ($linea!=""){			$camposbusqueda[]= $linea;
			$t=explode('|',$linea);
			$tag=$t[1];
			$matriz_c[$tag]=$linea;
		}
	}
//	echo $arrHttp["bdrel"];
//		echo $arrHttp["Opcion"];
switch ($arrHttp["Opcion"]){
	case "busquedalibre":
		EjecutarBusqueda();
		break;
	case "continuar":
		EjecutarBusqueda();
		break;
	case "formab":
	    $arrHttp["Opcion"]="buscar";
		DibujarFormaBusqueda();
		break;
	case "buscar":
		$Exp=stripslashes($arrHttp["Expresion"]);
		$Expresion=PrepararBusqueda();
		//echo $Exp."<br>".$Expresion;
		if (!isset($arrHttp["from"])) $arrHttp["from"]=1;
		header("Location: browse.php?encabezado=s&Expresion_b=".trim(urlencode($Exp))."&Expresion=".trim(urlencode($Expresion))."&Indice=".$arrHttp["id"]."&from=".$arrHttp["from"]);
		break;
	case "ubicar":
		$arrHttp["Formato"]="actual";
		UbicarRegistro();
		break;
	case "buscar_en_este":
		EjecutarBusqueda();
		break;

}

?>