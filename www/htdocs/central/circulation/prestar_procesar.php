<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      prestar_procesar.php
 * @desc:      Loan process 
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
if (!isset($_SESSION["login"])) die;
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
$lang=$_SESSION["lang"];
include("../lang/prestamo.php");

/////////////////////////////////////////////
include("../common/get_post.php");

// se lee la configuración de la base de datos de objetos de préstamos
include("leer_pft.php");
include("databases_configure_read.php");

//se busca la el número de inventario en el archivo de transacciones
$formato_obj=$db_path."trans/pfts/".$_SESSION["lang"]."/loans_display.pft";
if (!file_exists($formato_obj)) $formato_obj=$db_path."trans/pfts/".$lang_db."/loans_display.pft";
$query = "&Expresion=TR_P_".$arrHttp["inventory"]."&base=trans&cipar=$db_path"."par/trans.par&Formato=".$formato_obj;
$IsisScript=$xWxis."cipres_usuario.xis";
include("../common/wxis_llamar.php");
$prestamos=array();
foreach ($contenido as $linea){	$prestamos[]=$linea;
}
$die="";
$msg="";
$reservado="N";
$Opcion="";
$msg_1=$msgstr["reserve"];
$titulo="";

// Se extraen las variables necesarias para extraer la información del título al cual pertenece el ejemplar
// se toman de databases_configure_read.php
// pft_totalitems= pft para extraer el número total de ejemplares del título
// pft_in= pft para extraer el número de inventario
// pft_nc= pft para extraer el número de clasificación
// pft_typeofr= pft para extraer el tipo de registro

$Expresion=trim($prefix_in).$arrHttp["inventory"];
$formato_ex='\'$$$\''.$pft_totalitems."'||'".$pft_in."'||'".$pft_nc."'||'".$pft_typeofr;

//se ubica el título en la base de datos de objetos de préstamo
$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";

$Expresion=urlencode($Expresion);
$arrHttp["base"]="biblo";
$formato_obj=$db_path."biblo/loans/".$_SESSION["lang"]."/loans_display.pft";
if (!file_exists($formato_obj)) $formato_obj=$db_path."biblo/loans/".$lang_db."/loans_display.pft";
$formato_obj.="\n /".urlencode($formato_ex);
$query = "&Opcion=disponibilidad&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par&Expresion=".$Expresion."&Formato=$formato_obj";
include("../common/wxis_llamar.php");
$total=0;
foreach ($contenido as $linea){	$linea=trim($linea);
	if (substr($linea,0,8)=='$$TOTAL:')
		$total=trim(substr($linea,8));
	else
		$titulo.=$linea;
}
//se extrae la información relativa al ejemplar

$tt=explode('$$$',$titulo);
$titulo=$tt[0];
$ejemplar=$tt[1];
$xt=explode('||',$ejemplar);
$n_clasificacion=$xt[2];
$Opcion="";
// se leen las reservas del título
if ($total==0) { //Si el script wxis devuelve $$TOTAL:0 no existe el número de inventario en la base de datos de objetos	$titulo.=$arrHttp["inventory"].": ".$msgstr["copynoexists"];
}else{
	if (count($prestamos)>0){   // Si ya existe una transacción de préstamo para ese número de inventario, el ejemplar está prestado		$msg=$msgstr["itemloaned"];
	}else{		$Opcion="prestar";
		$action="usuario_prestamos_prestar.php";
		$msg_1=$msgstr["loan"];
	}}
include("../common/header.php");
?>
<script src=../dataentry/js/lr_trim.js></script>
<script>
PrimeraVez="s"
function BorrarUsuario(Ctrl){	if (PrimeraVez=="s") Ctrl.value=""
	PrimeraVez="n"}

document.onkeypress =
	function (evt) {
			var c = document.layers ? evt.which
	       		: document.all ? event.keyCode
	       		: evt.keyCode;
			if (c==13) EnviarForma()
			return true;
	}
function EnviarForma(){	if (Trim(document.usersearch.usuario.value)==""){		alert("<?php echo $msgstr["falta"]." ".$msgstr["usercode"]?>")
		return	}
	document.usersearch.submit()}

function Prestar(){	self.location.href="prestar.php?encabezado=s"}

function AbrirIndiceAlfabetico(xI){
		Ctrl_activo=xI
		lang="en"
		Separa=""
		tag=""
		Prefijo="NO_"
	    Separa="&delimitador="+Separa
	    db="users"
	    Repetible=""
	    cipar="users.par";
	    postings=1
	    Formato="v30,`$$$`,v35"
	    Prefijo=Separa+"&tagfst="+tag+"&prefijo="+Prefijo
	    ancho=200
		url_indice="capturaclaves.php?opcion=autoridades&base="+db+"&cipar="+cipar+"&Tag="+tag+Prefijo+"&postings="+postings+"&lang="+lang+"&repetible="+Repetible+"&Formato="+Formato
		msgwin=window.open(url_indice,"Indice","width=480, height=425,scrollbars")
		msgwin.focus()
}

</script>
<body>
<?php
include("../common/institutional_info.php");
$link_u="";
if (isset($arrHttp["usuario"])) $link_u="&usuario=".$arrHttp["usuario"];
?>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msg_1?>
	</div>
	<div class="actions">
		<?php include("submenu_prestamo.php");?>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<?php
echo "<a href=documentacion/ayuda.php?help=". $_SESSION["lang"]."/prestar.html target=_blank>". $msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=documentacion/edit.php?archivo=". $_SESSION["lang"]."/prestar.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: prestar_procesar.php </font>
	</div>";
// prestar, reservar
echo "
<div class=\"middle list\">
	<div class=\"spacer\">&#160;</div>
";
	if ($Opcion=="prestar")echo "<dd><h4>".$msg_1.": </font>".$msgstr["inventory"].": ".$arrHttp["inventory"]."</h4>";
	echo "<ul><li>$titulo";
	echo "</ul>";
	if ($msg!="") echo"<dd><h3><font color=red>".$msg."";
	$msgreserva="";
	if ($Opcion=="reservar"){		$msgreserva= "<dd><h3><font color=red><a href=javascript:PresentarReservas()>".$msgstr["reservewait"].": ".$reservado."</a>";		$msgreserva.= "<dd>".$msg_1.": ".$msgstr["signature"].": ".$n_clasificacion."</font></h3>";	}
echo "
	<div class=\"spacer\">&#160;</div>
	<div class=\"searchBox\">
	<form name=usersearch action=".$action." method=post onsubmit=\"javascript:return false\">";
if ($Opcion=="prestar" or $Opcion=="reservar"){
?>		<label for="searchExpr">
			<?php echo $msgreserva?>
			<strong><?php echo $msgstr["usercode"]?></strong>
		</label>
		<input type="text" name="usuario"  id="user" value="<?php if (isset($arrHttp["usuario"])) echo $arrHttp["usuario"]?>" size=50 onclick='javascript:BorrarUsuario(this)'/>
		<input type="hidden" name="Opcion" value="<?php echo $Opcion?>"/>
		<input type="submit" name="list" value="<?php echo $msgstr["list"]?>" class="submitButton" onclick="javascript:AbrirIndiceAlfabetico(document.usersearch.usuario)"/>
        <input type=hidden name=inventory value="<?php echo $arrHttp["inventory"]?>">
        <input type=hidden name=ejemplar value="<?php echo urlencode($ejemplar)?>">
        <input type=hidden name=titulo value="<?php echo urlencode($titulo)?>">
<?}else{	echo "<br><br>";}?>
	</form>
	</div>

</div>

</body>
<?php include("../common/footer.php")?>;
</html>