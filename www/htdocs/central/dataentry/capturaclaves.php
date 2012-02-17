<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      capturaclaves.php
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
include ("../config.php");

// ==================================================================================================
// INICIO DEL PROGRAMA
// ==================================================================================================
include("../common/get_post.php");
$arrHttp["lang"]=$_SESSION["lang"];
include("../lang/admin.php");

//foreach ($arrHttp as $var => $value )	echo "$var = $value; ";
//die;
$primeravez="";
if (!isset($arrHttp["baseactiva"])){
	$primeravez="S";	$arrHttp["baseactiva"]=$arrHttp["base"];
	$arrHttp["ba_formato"]=$arrHttp["Formato"];
	$arrHttp["pref"]=$arrHttp["prefijo"];}
//READ FILE WITH TESAURIS DEFINITION
$tesaurus=$db_path.$arrHttp["baseactiva"]."/def/".$_SESSION["lang"]."/tesaurus.def";
unset($tesau);
if (!file_exists($tesaurus)){
	$tesaurus=$db_path.$arrHttp["baseactiva"]."/def/".$lang_db."/tesaurus.def";
}
if (file_exists($tesaurus)){
	$tesau=parse_ini_file($tesaurus,1);
}
if (isset($arrHttp["tesauro"])){	$arrHttp["base"]=$arrHttp["tesauro"];
	$arrHttp["cipar"]=$arrHttp["tesauro"].".par";
	$arrHttp["Formato"]=$tesau[$arrHttp["base"]][$arrHttp["index"]."_pft"];
	if (!isset($arrHttp["prefijo"]) || $arrHttp["prefijo"]=="**"){		$arrHttp["prefijo"]=$tesau[$arrHttp["base"]][$arrHttp["index"]."_pref"];
	}
	$arrHttp["pref"]=$tesau[$arrHttp["base"]][$arrHttp["index"]."_pref"];
}else{   if (!isset($arrHttp["prefijo"]) || $arrHttp["prefijo"]=="**"){
		$arrHttp["prefijo"]=$arrHttp["ba_prefijo"];
		$arrHttp["pref"] =$arrHttp["ba_pref"];
	}
}

if (!isset($arrHttp["subc"])) $arrHttp["subc"]="";

	if (!isset($arrHttp["tagfst"])) $arrHttp["tagfst"]="";
	if (!isset($arrHttp["delimitador"])) $arrHttp["delimitador"]="";
	if (!isset($arrHttp["separa"])) $arrHttp["separa"]="";
	if (!isset($arrHttp["postings"])) $arrHttp["postings"]="ALL";
	$arrHttp["Formato"]=stripslashes($arrHttp["Formato"]);
	if (substr($arrHttp["Formato"],0,1)=="@"){
		$Formato=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/".substr($arrHttp["Formato"],1);
		if (!file_exists($Formato))
			$Formato=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/".substr($arrHttp["Formato"],1);
        $Formato="@".$Formato;
	}else
		$Formato=$arrHttp["Formato"];
  	$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["cipar"]."&Opcion=autoridades"."&tagfst=".substr($arrHttp["tagfst"],3)."&prefijo=".strtoupper($arrHttp["prefijo"])."&pref=".strtoupper($arrHttp["pref"])."&postings=".$arrHttp["postings"]."&formato_e=".urlencode($Formato);
//	echo $query;
	$IsisScript=$xWxis."ifp.xis";
	include("../common/wxis_llamar.php");
	$contenido = array_unique ($contenido);
	echo "<HTML>
	<head>
		<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">
		<META HTTP-EQUIV=\"Cache-Control\" CONTENT=\"no-cache\">
		<title>".$msgstr["listterm"]."</title>
		<script languaje=Javascript>

		document.onkeypress =
  			function (evt) {
    			var c = document.layers ? evt.which
            		: document.all ? event.keyCode
            		: evt.keyCode;
    			if (c==13) IrA()
    			return true;
  			}
  function closeit(){
top.closeit()
}
msg_pv=\"\"\n";
	if (!isset($arrHttp["subcampos"])) $arrHttp["subcampos"]="";
	if (!isset($arrHttp["delimitador"])) $arrHttp["delimitador"]="";
	if (!isset($arrHttp["separa"])) $arrHttp["separa"]="";
	if (!isset($arrHttp["repetible"])) $arrHttp["repetible"]="";
  	echo "subC=\"".$arrHttp["subcampos"]."\"\n";
	echo "Repetible=\"".$arrHttp["repetible"]."\"\n";
	echo "Tag=\"".$arrHttp["Tag"]."\"\n";
	echo "Delimitador=\"".$arrHttp["delimitador"]."\"\n";
	echo "Prefijo=\"".$arrHttp["prefijo"]."\"\n";
	echo "Separa=\"".$arrHttp["separa"]."\"\n";
	echo "Pref='".$arrHttp["pref"]."'\n";
	if (isset($arrHttp["indice"]))
		echo "Indice=\"".$arrHttp["indice"]."\"\n";
	else
		echo "Indice='N'\n";
	if (isset($arrHttp["sfe"]))
		echo "SubFieldEditor='s'\n" ;
	else
		echo "SubFieldEditor='n'\n" ;
?>
	if (Repetible=='R'){
	 	if (Delimitador.substr(1,1)==' '|| Delimitador==''){
	      	Delimitador="\r"
	  	}else{
			Delimitador=Delimitador.substr(1,1)
		}
	}else{
		Delimitador=''
	}
	Var=eval("window.opener.document.forma1."+Tag)
 	VarOriginal=Var
	if (Var.length>1)Var=eval(Var[Indice])
	a=Var.type
	a=a.toUpperCase()
	if (a=="TEXTAREA") Separa="\r"

	function CopyTerm(Term){
		Var=eval("window.opener.document.forma1."+Tag)
		if (Var.type=="text")
			Separa=";"
		else
			Separa=Delimitador		a=Var.value
		if (a=="" || Repetible!="R"){
			Var.value=Term
		}else{
			Var.value=a+Separa+Term
		}
		a=Var.value
		if (Var.type=="textarea") {
			b=a.split("\n")
			if(b.length>Var.rows) Var.rows=b.length+1

		}	}
    selected=""
    function ObtenerTerminos(){
		index="<?php if (isset($arrHttp["index"])) echo $arrHttp["index"]?>"
		if (index=="jerar") document.Lista.preview.checked=true
		Seleccion=""
		Pref="<?php echo $arrHttp["prefijo"]?>"

		Var=eval("window.opener.document.forma1."+Tag)
		if (Var.type=="text")
			Separa=";"
		else
			Separa=Delimitador

		for (i=0;i<document.Lista.autoridades.options.length; i++){
			if (document.Lista.autoridades.options[i].selected){
				optval=document.Lista.autoridades.options[i].text
				term=document.Lista.autoridades.options[i].value
				if (term=="") term=optval
				// si se llama desde la plantilla de subcampos, se elimina el primer subcampo
				if (Indice!="N"){					if (term.substring(0,1)=="^")
						term=term.substring(2)				}
				tt='$$'+term+'$$'
				if (Repetible!="R"){     //   NON REPETEABLE FIELD					Seleccion=term				}else{
					if (selected.indexOf(tt)==-1){
						if (Seleccion==""){  //EMPTY BOX
							Seleccion=term
						}else{
							Seleccion+=Separa+term
						}
						selected+=tt
					}
				}
			}
		}
		if (Seleccion=="") return
        if (document.Lista.preview.checked){
        	msg_pv="Y"
        	if (index=="jerar"){        		optval=term;
        	}
        	url="base=<?php echo $arrHttp["base"]?>&Expresion="+escape(Pref+optval)+"&Opcion=buscar"
        	<?php
        	if (isset($arrHttp["tesauro"]))
        		echo "script_name='tesauro_show.php'\n";
        	else
        		echo "script_name='show.php'\n";
        	?>
        	msgwin_preview=window.open(script_name+"?"+url,"preview","width=400, height=400,resizable,scrollbars,status")
			msgwin_preview.focus()
			return
		}
        if (SubFieldEditor=="s"){
        	x=Seleccion.split('^')

        	if (x.length>1){	        	window.opener.Redraw(Seleccion)
	        	self.close()
	        	return
	      }        }


		VarOriginal=Var.value
		ss=Seleccion.split("^")
		nn=Var.name.split("_")
        if (Seleccion.indexOf('^')==-1 && Seleccion.substr(0,4)!="_TAG"){
        	a=Var.value
			if (a=="" || Repetible!="R"){
				Var.value=Seleccion
			}else{

				Var.value=a+Separa+Seleccion
				a=Var.value

				if (Var.type=="textarea") {
					b=a.split("\n")
					if(b.length>Var.rows) Var.rows=b.length
				}
			}        }else{
			if (subC.length>0 ){
				for (jsc=0;jsc<subC.length;jsc++){
					Ctrl=eval("window.opener.document.forma1."+nn[0]+"_"+nn[1]+"_"+subC.substr(jsc,1))
					ixpos=Seleccion.indexOf("^"+subC.substr(jsc,1))
					if (ixpos>=0){
						cc=Seleccion.substr(ixpos+2)
						ixpos=cc.indexOf("^")
						if (ixpos>0) cc=cc.substr(0,ixpos)
							Ctrl.value=cc
					}else{
						Ctrl.value=""
					}
				}
			}else{
				if (Seleccion.substr(0,4)=="_TAG"){					CopyToFields(Seleccion)
					self.close()
					return				}
				if (Var.type=="textarea" || Var.type=="text"){
					if (Seleccion!=""){
						a=Var.value
						if (a=="" || Repetible!="R"){
							Var.value=Seleccion
						}else{
							Var.value=a+Separa+Seleccion
						}
					}

				}
				a=Var.value

				if (Var.type=="textarea") {
					b=a.split("\n")

					if(b.length>Var.rows) Var.rows=b.length
				}
			}
		}
		if (Var.type!="textarea") self.close()
	}

function CopyToFields(Seleccion){
	t=Seleccion.split('_TAG');
	for (ix=1;ix<t.length;ix++){
		val=t[ix].split(':')		Ctrl=eval("window.opener.document.forma1.tag"+val[0])
		Ctrl.value=val[1]	}}

function Continuar(){
	i=document.Lista.autoridades.length-1
	a=document.Lista.autoridades[i].text
	AbrirIndice(a)
}

function IrA(ixj){	a=document.Lista.ira.value

	AbrirIndice(a)
}

function CloseWindows(){	if (msg_pv=="Y"){		msgwin_preview.close()	}}

function AbrirTesauro(Tes,Index){
	if (Tes=="<?php echo $arrHttp["baseactiva"]?>"){        document.Lista.baseactiva.value=""
        document.Lista.tesauro.value=""
        document.Lista.base.value="<?php echo $arrHttp["baseactiva"]?>"
        document.Lista.Formato.value="<?php echo $arrHttp["ba_Formato"]?>"
        document.Lista.pref.value="<?php echo $arrHttp["ba_pref"]?>"
        document.Lista.pref.value="<?php echo $arrHttp["ba_prefijo"]?>"	}else{
		document.Lista.tesauro.value=Tes

		document.Lista.pref.value=""
	}
	document.Lista.index.value=Index
	document.Lista.prefijo.value="**"
	document.Lista.submit()}

<?php
echo "function AbrirIndice(Termino){
		Prefijo='".$arrHttp["pref"]."'+Termino
		Pref='".$arrHttp["pref"]."'
		document.Lista.prefijo.value=Prefijo
		document.Lista.pref.value=Pref
		document.Lista.submit()

	}

</script>\n";
if (isset($arrHttp["width"])){	$width=$arrHttp["width"];
}else{
	$width="100%";
}
?>
	</head>
	<body bgcolor=#eeeeee LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" onunload=CloseWindows() onsubmit="javascript: return false">
	<?php echo "<font face=arial><strong>".$msgstr["termsdict"].": ";
	if (!isset($tesau[$arrHttp["base"]]["name"]))
		echo $arrHttp["base"];
	else
		echo $tesau[$arrHttp["base"]]["name"];
	echo " ".$arrHttp["baseactiva"];
	echo "</strong><br>";?>
	<font color=black size=1 face=arial><?php echo $msgstr["ayudaterminos"]?>
	<center>
	<table cellpadding=0 cellspacing=0 border=0 width=100%>
			<form method=post name=Lista onSubmit="javascript:return false">
	<td  bgcolor=#cccccc align=center colspan=2><font size=1 face="arial"><?php for ($i=65;$i<91;$i++ ) echo "<a href=javascript:AbrirIndice('".chr($i)."')>".chr($i)."</a>&nbsp; "?></td>
	<tr>

<?php
if (!isset($arrHttp["index"])) $arrHttp["index"]="";
switch($arrHttp["index"]){
	case "permu":
		foreach ($contenido as $linea)  {

			$linea=trim($linea);			$ix=strpos($linea," ");
			$permuta=substr($linea,0,$ix);
			$permuta=substr($permuta,4);
			$permuta=trim($permuta);
			if (strlen($permuta)>1){
				$termino=trim(substr($linea,$ix));
				$ix=strpos($termino,$permuta);
				if ($ix==0){
					$parte1="&nbsp;";
					$parte2=$termino;
				}else{
					$parte1=substr($termino,0,$ix)."&nbsp;";
					$parte2=substr($termino,$ix);
				}
				echo "<tr><td align=right bgcolor=white><font size=1>".$parte1." </td><td bgcolor=white><font size=1>".$parte2."</td>";
			}
		}
		echo "</table>";
		break;
	default:
		echo "<td width=95% valign=top>";
		echo "
			<Select name=autoridades multiple size=24 style=\"width:'".$width."'\" onclick=javascript:ObtenerTerminos()>\n";
		foreach ($contenido as $linea){
			$f=explode('$$$',$linea);
			if (isset($f[2])) $f[1]=$f[2];
			if (!isset($f[1])) $f[1]=$f[0];
			echo "<option value=\"";
			echo $f[1];
			echo "\">";
	        if (substr($f[0],0,1)=="^") $f[0]=substr($f[0],2);
	        echo $f[0];
	        echo "</option>";

		}
		echo "</select></td>";
		break;
}

?>


	</table>
	<table cellpadding=0 cellspacing=0 border=0 width=100% bgcolor=#cccccc>
		<td><font size=1><input type=checkbox name=preview><?php echo $msgstr["vistap"]?> &nbsp; &nbsp;
		<a href=Javascript:Continuar() class=boton><font size=1><?php echo $msgstr["continuar"]?></a></td>
		<td class=menusec2><font size=1><?php echo $msgstr["avanzara"]?>: <input type=text name=ira size=15 value=""><a href=Javascript:IrA() onKeyPress="codes(event)" ><img src=img/barArrowRight.png border=0 align=ABSBOTTOM></a></td>
	</table><p>
<?php
if (isset($tesau)) {	foreach ($tesau as $key=>$value){
		$name_t=$key;
		echo "</center><br>".$tesau[$key]["name"].": <a href=\"javascript:AbrirTesauro('$key','alpha')\">".$msgstr["alfabet"]."</a>&nbsp; &nbsp;";
		if (isset($tesau[$key]["permu_pref"])) echo  " | <a href=\"javascript:AbrirTesauro('$key','permu')\">".$msgstr["permutado"]."</a>&nbsp; &nbsp;";
		if (isset($tesau[$key]["jerar_pref"])) echo  " | <a href=\"javascript:AbrirTesauro('$key','jerar')\">".$msgstr["jerarq"]."</a>&nbsp; &nbsp;";	}}
if (isset($arrHttp["baseactiva"]) and isset($tesau)){	    echo "<br><a href=\"javascript:AbrirTesauro('".$arrHttp["baseactiva"]."')\">".$msgstr["bd"]." ".$arrHttp["baseactiva"]."</a>&nbsp; &nbsp;";}

	echo "<input type=hidden name=baseactiva value=\"".$arrHttp["baseactiva"]."\">\n";
if ($primeravez=="S") {	echo "<input type=hidden name=ba_Formato value=".$arrHttp["Formato"].">\n";
	echo "<input type=hidden name=ba_Tag value=".$arrHttp["Tag"].">\n";
	echo "<input type=hidden name=ba_pref value=".$arrHttp["pref"].">\n";
	echo "<input type=hidden name=ba_prefijo value=".$arrHttp["prefijo"].">\n";
	echo "<input type=hidden name=ba_repetible value=".$arrHttp["repetible"].">\n";}else{	echo "<input type=hidden name=ba_formato value=".$arrHttp["ba_formato"].">\n";
	echo "<input type=hidden name=ba_Tag value=".$arrHttp["ba_Tag"].">\n";
	echo "<input type=hidden name=ba_pref value=".$arrHttp["ba_pref"].">\n";
	echo "<input type=hidden name=ba_prefijo value=".$arrHttp["ba_prefijo"].">\n";
	echo "<input type=hidden name=ba_repetible value=".$arrHttp["ba_repetible"].">\n";}
echo "<input type=hidden name=base value=>\n";
echo "<input type=hidden name=Formato value=".$arrHttp["Formato"].">\n";
echo "<input type=hidden name=Tag value=".$arrHttp["Tag"].">\n";
echo "<input type=hidden name=pref value=".$arrHttp["pref"].">\n";
echo "<input type=hidden name=prefijo value=".$arrHttp["prefijo"].">\n";
echo "<input type=hidden name=repetible value=".$arrHttp["repetible"].">\n";
echo "<input type=hidden name=postings value=".$arrHttp["postings"].">\n";
echo "<input type=hidden name=index";
if (isset($arrHttp["index"])) echo " value=".$arrHttp["index"];
echo ">\n";
if (isset($arrHttp["tesauro"])){	echo "<input type=hidden name=tesauro value=".$arrHttp["tesauro"].">\n";}else{	echo "<input type=hidden name=tesauro>\n";}
?>

</form>


	</body></html>


