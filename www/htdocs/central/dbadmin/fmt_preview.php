<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      fmt_preview.php
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
// ==================================================================================================
// INICIO DEL PROGRAMA
// ==================================================================================================

if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../common/get_post.php");
$arrHttp["login"]=$_SESSION["login"];
$arrHttp["password"]=$_SESSION["password"];

$base =$arrHttp["base"];
$cipar =$arrHttp["cipar"];
$login=$arrHttp["login"];
$password=$arrHttp["password"];

// se lee el archivo mm.fdt
$fp = file("../../bases/".$base."/tipom.tab");
foreach ($fp as $linea){
	if (!empty($linea)) {
  		$i=strpos($linea," ");
		if (($i===false)) {
	    	$i=strlen($linea)+1;
		}
  		$tipom=trim(substr($linea,0,$i));

		$fpTm = file($db_path.$base."/$tipom");
		if (!$fpTm) {
	    	echo $base."/$tipom"." no existe";
			die;
		}
		foreach ($fpTm as $linea){
			if (!empty($linea)) {
	  			if (substr($linea,0,2)!='##'){
	  	  			$tag=trim(substr($linea,50,3));
	  	  			if ($tag!="" and $tag!="*"){
	  	  				if (!isset($Fdt[$tag])){
			    			$Fdt[$tag]=$linea;
						}
					}
				}
			}
		}
	}
}
//sort($Fdt);
if (isset($arrHttp["wks"])) {
	if ((strpos($arrHttp["wks"],".php")===false)) {
	    $fp = file($db_path.$base."/www/".$arrHttp["wks"].".wks");
		$arrHttp["tagsel"]="";
		foreach($fp as $linea) $arrHttp["tagsel"].=trim($linea)."|";
	}
	$arrHttp["tagsel"]=str_replace(',','|', $arrHttp["tagsel"] );
    $t=explode('|',$arrHttp["tagsel"]);
  	$Formato="";
  	foreach ($t as $tag){
  	  	if ($tag!=""){
	    	$tag_s[$tag]="S";
		}
	}
}
include("../common/header.php");
?>

<script languaje=javascript>

function AbrirVentana(Archivo){
	xDir="<?php echo $xSlphp.'ayudas/'?>"
	msgwin=window.open(xDir+Archivo,"Ayuda","menu=no, resizable,scrollbars")
	msgwin.focus()
}



function GenerarFormato(){
  	formato=""
  	for (i=0;i<document.forma1.list21.options.length;i++){
	    campo=document.forma1.list21.options[i].value
		alert(campo)
	    tag=Trim(campo.substr(50,3))
	    formato+=tag+","
	}
	if (formato=="" ){
	  	alert("debe seleccionar los campos que va a incluir en la salida")
	  	return
	}
	alert(formato)
	document.forma1.wks.value=formato
 	document.forma1.submit()
}

function Buscar(){
	base=document.forma1.base.value
	cipar=document.forma1.cipar.value
	ix=top.menu.document.forma1.formato.selectedIndex
	if (ix==-1) ix=0
	Formato=top.menu.document.forma1.formato.options[ix].value
	FormatoActual="&Formato="+Formato
  	self.location.href="buscar.php?Opcion=formab&prologo=prologoact&Target=s&Tabla=imprimir&base="+base+"&cipar="+cipar+FormatoActual
}

</script>
<body>

<form name=forma1 method=post action=generarfe_g.php onsubmit="Javascript:return false" >
<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
<input type=hidden name=cipar value=<?echo $arrHttp["cipar"]?>>
<input type=hidden name=Expresion value='<?echo stripslashes($arrHttp["Expresion"])?>'>
<input type=hidden name=tagsel>
<input type=hidden name=Opcion>
<input type=hidden name=wks>

<center><br>
<table cellpading=5 cellspacing=5 border=0 background=<?echo $xSlphp?>img/fondo0.jpg width=600>
		<td colspan=4 class=title align=center>Generar formato de ingreso &nbsp; &nbsp; <a href="javascript:AbrirVentana('generarfe.html')"><img src=<?echo $xSlphp?>img/about.gif border=0></a></td>

</table>

<table border=1 background=<?echo $xSlphp?>img/fondo0.jpg>

	<td valign=top colspan=4 class=subtitlebody>Proceda a crear un formato de ingreso seleccionando los campos</td><tr><td><Select name=list11 style="width=250px" multiple size=20 onDblClick="moveSelectedOptions(this.form['list11'],this.form['list21'],false)">

<? foreach ($Fdt as $linea){
   		$key=trim(substr($linea,50,3));
   		if (!isset($tag_s[$key]))
	   		echo "<option value='".$linea."'>".trim(substr($linea,3,30))." (".trim(substr($linea,50,3)).")\n";
  	}
?>
	</select></td>
	<TD VALIGN=MIDDLE ALIGN=CENTER>
		<A HREF="#" onClick="moveSelectedOptions(document.forms[0]['list11'],document.forms[0]['list21'],false);return false;"><img src=<?echo $xSlphp?>img/flecha_right.gif border=0></A><BR><BR>
		<A HREF="#" onClick="moveAllOptions(document.forms[0]['list11'],document.forms[0]['list21'],false); return false;"><img src=<?echo $xSlphp?>img/flecha_right.gif border=0><img src=<?echo $xSlphp?>img/flecha_right.gif border=0></A><BR><BR>
		<A HREF="#" onClick="moveAllOptions(document.forms[0]['list21'],document.forms[0]['list11'],false); return false;"><img src=<?echo $xSlphp?>img/flecha_left.gif border=0><img src=<?echo $xSlphp?>img/flecha_left.gif border=0></A><BR><BR>
		<A HREF="#" onClick="moveSelectedOptions(document.forms[0]['list21'],document.forms[0]['list11'],false); return false;"><img src=<?echo $xSlphp?>img/flecha_left.gif border=0></A>

	</TD>
	<TD>
	<SELECT NAME="list21" MULTIPLE SIZE=20 style="width=250px" onDblClick="moveSelectedOptions(this.form['list21'],this.form['list11'],false)">
<? foreach ($tag_s as $key=>$value){
		foreach ($Fdt as $linea){

   			if ($key==trim(substr($linea,50,3)) ){
		   		echo "<option  value='".$linea."'>".trim(substr($linea,3,30))." (".trim(substr($linea,50,3)).")\n";
			}
		}
  	}
?>
	</SELECT>
	</TD>
	<TD ALIGN="CENTER" VALIGN="MIDDLE">
	<INPUT TYPE="button" VALUE="subir" onClick="moveOptionUp(this.form['list21'])">
	<BR><BR>
	<INPUT TYPE="button" VALUE="bajar" onClick="moveOptionDown(this.form['list21'])">
	</TD>
	<tr><td colspan=4 class=home>Al terminar la selección de los campos suministre los datos para almacenarlo en la lista</td></tr>
	<tr><td valign=top colspan=4 class=subtitlebody>
		nombre <input type=text name=nombre size=8 maxlength=12> descripción: <input type=text size=50 maxlength=50 name=descripcion> &nbsp;
		<a href=javascript:GenerarFormato()><img src=<?echo $xSlphp?>img/save_32.gif alt="guardar" border=0 align=absmiddle></a>
		</td>
<script>
<?php if ((isset($arrHttp["wks"]))) {
       echo "document.forma1.nombre.value=\"".$arrHttp['wks']."\"\n";
	   echo "document.forma1.descripcion.value=\"".$arrHttp['desc']."\"\n";
   }
?>
</script>
</table>
</form>
</body>
</html>

