<?php 
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      campos.php
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

include ("../lang/admin.php");
include("../common/header.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
echo "<script>\n";
echo "is_marc='".$arrHttp["is_marc"]."'\n";
echo "PickList=new Array()\n";
if (!isset($arrHttp["wks"])) $arrHttp["wks"]=$arrHttp["base"].".fdt";

// se lee la FDT para conseguir la etiqueta del campo donde se coloca la numeración automática y el prefijo con el cual se indiza el número de control
	$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["wks"];
	if (file_exists($archivo)){
		$fp=file($archivo);
	}else{
		$archivo=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$arrHttp["wks"];
		if (file_exists($archivo)){
			$fp=file($archivo);
		}else{
			echo $arrHttp["falta"].": ".$archivo;
		    die;
		 }
	}
	$found=false;
	$ix=-1;

	foreach ($fp as $linea){
		if (!empty($linea)) {
			$l=explode('|',$linea);
			if ($l[1]==$arrHttp["tag"] or $found==true){
				$found=true;
				if ($ix==-1){					$subc=$l[5];
					$ix=0;				}else{					$ix=$ix+1;
					if ($l[0]=="S") {						$ind_sc=$ix-1;
	                    $Ind="";
	                    if ($ind_sc<2 and $arrHttp["is_marc"]=="S"){
	                    	if (substr($subc,$ind_sc,1)==1 or substr($subc,$ind_sc,1)==2)
	                    		$Ind="I";
	                    }
	                    $key=$Ind.substr($subc,$ind_sc,1);
						if (!empty($l[11])) {
							PickList($key,$l[11]);						}else{
							$l=$ix-1;
							echo "PickList['".$key."']=''\n";						}					}else{						break;					}				}
			}		}
	}
echo "</script>\n";

?>
<STYLE type=text/css>TABLE {	BACKGROUND-COLOR: #ffffff; FONT-FAMILY: Verdana, Helvetica, Arial; FONT-SIZE: 8pt}BODY {	FONT-FAMILY: Verdana, Helvetica, Arial; FONT-SIZE: 8pt}
input 		{BORDER-TOP-COLOR: #000000; BORDER-LEFT-COLOR: #000000; BORDER-RIGHT-COLOR: #000000; BORDER-BOTTOM-COLOR: #000000; BORDER-TOP-WIDTH: 1px; BORDER-LEFT-WIDTH: 1px; FONT-SIZE: 12px; BORDER-BOTTOM-WIDTH: 1px; FONT-FAMILY: Arial,Helvetica; BORDER-RIGHT-WIDTH: 1px}select 		{BORDER-TOP-COLOR: #000000; BORDER-LEFT-COLOR: #000000; BORDER-RIGHT-COLOR: #000000; BORDER-BOTTOM-COLOR: #000000; BORDER-TOP-WIDTH: 1px; BORDER-LEFT-WIDTH: 1px; FONT-SIZE: 12px; BORDER-BOTTOM-WIDTH: 1px; FONT-FAMILY: Arial,Helvetica; BORDER-RIGHT-WIDTH: 1px}textarea	{BORDER-TOP-COLOR: #000000; BORDER-LEFT-COLOR: #000000; BORDER-RIGHT-COLOR: #000000; BORDER-BOTTOM-COLOR: #000000; BORDER-TOP-WIDTH: 1px; BORDER-LEFT-WIDTH: 1px; FONT-SIZE: 12px; BORDER-BOTTOM-WIDTH: 1px; FONT-FAMILY: Arial,Helvetica; BORDER-RIGHT-WIDTH: 1px}text		{BORDER-TOP-COLOR: #000000; BORDER-LEFT-COLOR: #000000; BORDER-RIGHT-COLOR: #000000; BORDER-BOTTOM-COLOR: #000000; BORDER-TOP-WIDTH: 1px; BORDER-LEFT-WIDTH: 1px; FONT-SIZE: 12px; BORDER-BOTTOM-WIDTH: 1px; FONT-FAMILY: Arial,Helvetica; BORDER-RIGHT-WIDTH: 1px}checkbox	{BORDER-TOP-COLOR: #000000; BORDER-LEFT-COLOR: #000000; BORDER-RIGHT-COLOR: #000000; BORDER-BOTTOM-COLOR: #000000; BORDER-TOP-WIDTH: 1px; BORDER-LEFT-WIDTH: 1px; FONT-SIZE: 12px; BORDER-BOTTOM-WIDTH: 1px; FONT-FAMILY: Arial,Helvetica; BORDER-RIGHT-WIDTH: 1px}</STYLE></head><xscript language=Javascript src=js/windowdhtml.js></xscript>
<script language=JavaScript src=js/terminoseleccionado.js></SCRIPT><script language=JavaScript src=js/lr_trim.js></SCRIPT><script language=javascript>
	base=window.opener.top.base
	url_indice=""
	Ctrl_activo=""
	lista_sc=Array()
	function getElement(psID) {	if(!document.all) {
		return document.getElementById(psID);

	} else {
		return document.all[psID];
	}
}
	function AbrirIndiceAlfabetico(xI,Prefijo,SubC,Separa,db,cipar,tag,Formato){		Ctrl_activo=getElement(xI)
	    document.forma1.Indice.value=xI
	    Separa="&delimitador="+Separa	    Prefijo=Separa+"&tagfst="+tag+"&prefijo="+Prefijo	    ancho=200		url_indice="capturaclaves.php?opcion=autoridades&base="+db+"&cipar="+cipar+"&Tag="+xI+Prefijo+"&indice="+xI+"&repetible=0"+"&Formato="+Formato+"&postings=10&sfe=s"  		msgwin=window.open(url_indice,"indice","width=550, height=425,resizable, scrollbar")
  		msgwin.focus()    	return

	}

	function AbrirIndice(ira){		url_indice=url_indice+ira	    ancho=screen.width-500-20		msgwin=window.open(url_indice,"Indice","status=yes,resizable=yes,toolbar=no,menu=yes,scrollbars=yes,width=500,height=600,top=20,left="+ancho)		msgwin.focus()	}

	function Ayuda(tag){
		help=window.opener.url_H
		if (help!=""){			url=help		}else{
			tagx=String(tag)
			if (tagx.length<3) tagx="0"+tagx
			if (tagx.length<3) tagx="0"+tagx
			url="../documentacion/ayuda_db.php?help="+base+"/ayudas/<?php echo $_SESSION["lang"]."/"?>tag_"+tagx+".html"
		}
		msgwin=window.open(url,"Ayuda","status=yes,resizable=yes,toolbar=no,menu=yes,scrollbars=yes,width=600,height=400,top=100,left=100")
		msgwin.focus()	}

</script><body>
	<div class="helper">
	<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/assist_sc.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
	<?php if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"])) echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/assist_sc.html target=_blank>".$msgstr["edhlp"]."</a>";
	echo "<font color=white>&nbsp; &nbsp; Script: campos.php" ?>
</font>
	</div>
 <div class="middle form">
			<div class="formContent">
<body link=black vlink=black bgcolor=white><form name=forma1>

<input type=hidden name=tagcampo><input type=hidden name=occur><input type=hidden name=ep><input type=hidden name=NoVar><input type=hidden name=Indice value=""><input type=hidden name=base><input type=hidden name=cipar>
<input type=hidden name=Formato>
<input type=hidden name=Repetible><input type=hidden name=Indice>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class=td2>    <tr>
    	<td>			<script language=JavaScript src=js/editarocurrencias.js></SCRIPT>
		</td>
	<tr>
		<td id="asubc">
		</td>	<tr>		<td><br>		<!--	<font size=1>Use los símbolos <img src=../img/add.gif> y <img src=../img/delete.gif> para agregar o eliminar ocurrencias respectivamente.            Cuando agregue una ocurrencia, la misma se adicionará al menú de selección cuando haga clic sobre <img src=../img/aceptar.gif alt='Aceptar estos cambios' border=0 height=15>.			Al terminar la edición del campo, haga clic sobre <img src=../img/pasaraldocumento.gif alt='Pasar al registro' border=0 height=15> para actualizar el formulario de ingreso.			Si no desea pasar los cambios al formulario, haga clic sobre <img src=../img/cancelar.gif alt='cancelar la edición del campo' border=0 height=15>-->		</td></table><br><center><table width=200 bgcolor=#FFFFFF border=0 cellspacing=5>	<td align=center>		<a href=javascript:AceptarCambios()><img src=img/aceptar.gif  border=0><br><?php echo $msgstr["aceptar"]?></a>	</td>	<td align=center><a href=javascript:ActualizarForma()><img src=img/pasaraldocumento.gif  border=0><br><?php echo $msgstr["actualizar"]?></a>	</td>
	<td align=center>
		<a href="javascript:self.close()"><img src=img/cancelar.gif  border=0><br><?php echo $msgstr["cancelar"]?></a>
	</td></table><script language=javascript>
  	if (Occ>0) {  		TerminoSeleccionado()
  	}else{
  		Redraw("")  	}</script></form>
</center>
</div>
</div>
<?php include("../common/footer.php")?><p></body></html>
<?php
// ===============================================
function PickList($ix,$file){
global $db_path,$lang_db,$arrHttp;	$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$file;
	if (!file_exists($archivo)) $archivo=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$file;
	if (file_exists($archivo)){		$fp=file($archivo);
		$Options="";
		foreach ($fp as $value) {			$value=rtrim($value);
			if ($value!=""){				$Options.=$value."$$$$";			}		}	}
	echo "PickList['$ix']='".str_replace("'","&#39;",$Options)."'\n";}
?>

