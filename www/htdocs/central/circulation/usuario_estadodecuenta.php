<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      usuario_estadodecuenta.php
 * @desc:      Checks the user account status
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
if (!isset($_SESSION["login"])) die;
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
include("../config_loans.php");
$lang=$_SESSION["lang"];
include("../lang/prestamo.php");
// se lee la configuración local
include("locales_read.php");
// se leen las politicas de préstamo
include("loanobjects_read.php");
// se lee la configuración de la base de datos de usuarios
include("borrowers_configure_read.php");

$valortag = Array();
include("../common/get_post.php");
//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";

include ('../dataentry/leerregistroisispft.php');
include("../common/header.php");

echo "
<script language=Javascript src=../dataentry/js/lr_trim.js></script>
<script language=Javascript src=js/prestamo.js></script>
<script language=javascript src=js/popcalendar.js></script>
";
?>
<script>
ppu=new Array()
usuario='<?php echo $arrHttp["usuario"]?>'


function BuscarInventario(){
	base='biblo'
	cipar='biblo.par'
	inv=document.inventory_search.inventory.value
	inv=Trim(inv)
	if (inv==""){
		alert("Inventory number required")
		return
	}
	expresion="NI="+inv

	url="prestamo_disponibilidad.php?Opcion=inventario&Formato=prestar&Expresion="+expresion+"&usuario="+usuario+"&inven="+inv+"&base="+base+"&cipar="+cipar
	self.location.href=url
}

function BuscarItem(e,Accion) {
	if (nav4) // Navigator 4.0x
  		var whichCode = e.which

		else // Internet Explorer 4.0x
  			if (e.type == 'keypress') // the user entered a character
   				 var whichCode = e.keyCode
  			else
    				var whichCode = e.button;
	if (e.type == 'keypress' && whichCode==13 ){
		switch (Accion){
			case 1:        // prestar por No. de inventario
				BuscarInventario()
				break
			case 2:        // prestar por cota

//		    	BuscarLibro("S")
				break
		}
	}
}

function AbrirIndiceAlfabetico(xI,Prefijo,Subc,Separa,db,cipar,tag,postings,Repetible,Formato){
		Ctrl_activo=xI
		lang="en"
	    Separa="&delimitador="+Separa
	    Prefijo=Separa+"&tagfst="+tag+"&prefijo="+Prefijo
	    ancho=200
		url_indice="capturaclaves.php?opcion=autoridades&base="+db+"&cipar="+cipar+"&Tag="+tag+Prefijo+"&postings="+postings+"&lang="+lang+"&repetible="+Repetible+"&Formato="+Formato
		msgwin=window.open(url_indice,"Indice","width=480, height=425,scrollbars")
		msgwin.focus()
}

function AbrirIndice(TipoI,Ctrl){

	switch (TipoI){
		case "U":
			db="users"
			prefijo="NO_"
//			AbrirIndiceAlfabetico(Ctrl,"NO_","","","users","cipres.par","30",1,"","v30,`$$$`,v35")
			break
 		case "C":

			db="presta"
			prefijo="CU_"
//   			top.main.location="prestamos.php?Opcion=localiza_titulo&userid="+userid+"&base="+db+"&cipar=cipres.par&prefijo="+prefijo
			break
 		case "I":
            db="biblo"
  			Formato="v101^n";   //ojo, sustituir por el del archivo de configuración
  			Prefijo="NI="   //ojo, sustituir por el del archivo de configuración
  			AbrirIndiceAlfabetico(Ctrl,Prefijo,"","","biblo",db+".par","",1,"",Formato)
	}
}

function EnviarForma(Opcion){
	document.Procesar.Opcion.value=Opcion
	switch (Opcion){
		case "Inventory":
			document.Procesar.action.value="prestamo_disponibilidad.php"
			document.Procesar.inventory.value=document.inventory_search.inventory.value
			document.Procesar.base.value="biblo"
			document.Procesar.submit()
	}

	document.Procesar.
	alert(Opcion)

}
</script>
<?php
Function Iso2Fecha($fecha){
	$f=substr($fecha,6,2)."/".substr($fecha,4,2)."/".substr($fecha,0,4);
	return $f;
}

// se determina si el préstamo está vencido
function compareDate ($FechaP){
global $locales;

//Se convierte la fecha a formato ISO (yyyymmaa) utilizando el formato de fecha local
	switch ($locales["date1"]){
		case "d":
			$dia=substr($FechaP,0,2);
			break;
		case "m":
			$mes=substr($FechaP,0,2);
			break;
	}
	switch ($locales["date2"]){
		case "d":
			$dia=substr($FechaP,3,2);
			break;
		case "m":
			$mes=substr($FechaP,3,2);
			break;
	}
	$year=substr($FechaP,6,4);
	$exp_date=$year."-".$mes."-".$dia;
	$todays_date = date("Y-m-d");
	$today = strtotime($todays_date);
	$expiration_date = strtotime($exp_date);
	$diff=$expiration_date-$today;
	return $diff;

}//end Compare Date


// ------------------------------------------------------



echo "<body>";
 include("../common/institutional_info.php");
?>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["loan"]."/".$msgstr["return"]?>
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
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/prestamo_procesar.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php if ($_SESSION["permiso"]=="loanadm"){
		echo "        		<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/prestamo_procesar.html target=_blank>".$msgstr["edhlp"]."</a>";
      	echo "<font color=white>&nbsp; &nbsp; Script: usuario_prestamo_presentar.php</font>\n";
      }
?>
	</div>
<div class="middle form">
	<div class="formContent">
<form name=ecta>
<?php

include("ec_include.php");  //se incluye el procedimiento para leer el usuario y los préstamos pendientes
if ($np>-1){
	echo "<table border=0 width=100%><tr><td>
			<a href=javascript:Procesar('devolver')>".$msgstr["return"]."</a> &nbsp; &nbsp;
			<a href=javascript:Procesar('renovar')>".$msgstr["renew"]."</a>
			</td></table>";

}
echo "</form>";
//Se obtiene el código, tipo y vigencia del usuario
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
if (empty($userdata[2]))
	$vig=$msgstr["activo"];
else
	$vig=$userdata[2];
echo "Código del usuario: ". $userdata[0] ."; &nbsp;Tipo de usuario: ".$userdata[1]."; &nbsp;Vigencia del usuario: ".$vig;
echo " &nbsp; <a href=javascript:PoliticasUsuario(\"".$userdata[1]."\")>Ver politicas</a><br>";
//Se analiza la vigencia del usuario
switch($userdata[2]){
	case "1":
		echo "<font color=red>Usuario no vigente</font>";
		break;
	case "2":
	    echo "<font color=red>Usuario suspendido</font>";
		break;
}

if (!empty($userdata[2])) {
	echo $msgstr[$userdata[2]];
	die;
}
//Se determina el total de objetos permitidos prestar al usuario
$tprestamos_p=0;
foreach ($politica as $key=>$ob){
	foreach($ob as $key1=>$obj1)
	if ($key1==$userdata[1]){
		$obj=explode('|',$obj1);
		$tprestamos_p+=$obj[2];
    }
}
echo "Total objetos a prestar: $tprestamos_p;  Total objetos prestados: $np;  Total objetos en mora: $nv";
// se determina si el usuario puede recibir más préstamos (la variable $np de calcula en ec_include.php)
if ($tprestamos_p>$np){

?>
	<h5><?php echo $msgstr["loan"].". ".$msgstr["loanoption"]?></h5>
	<div class="searchBox">
		<form name=inventory_search action=usersearch.php method=post onsubmit="javascript:return false">
		<input type=hidden name=Indice>
		<table width=100%>
			<td width=100>
			<label for="searchExpr">
				<strong><?php echo $msgstr["inventory"]?></strong>
			</label>
			</td><td>
			<input type="text" name="inventory" id="inventory" value="" class="textEntry" onfocus="this.className = 'textEntry textEntryFocus';"  onblur="this.className = 'textEntry';" onKeyPress="javascript:BuscarItem(event,1)"/>
			<input type="submit" name="ok" value="<?php echo $msgstr["search"]?>" class="submit" onClick=BuscarInventario() />
			<input type="submit" name="index" value="<?php echo $msgstr["list"]?>" class="submit" onClick="javascript:AbrirIndice('I',document.inventory_search.inventory)" />
		</td></table>
			</td></table>
		</form>
	</div>
	<p>
<?php if ($classification_number){
?>
	<div class="searchBox">
		<form name=signaturesearch action=search_loan.php method=post onsubmit="javascript:return false">
		<input type=hidden name=Opcion value=signature>
		<input type=hidden name=Indice>
		<table width=100%>
			<td width=100>
			<label for="searchExpr">
				<strong><?php echo $msgstr["signature"]?></strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
			</label>
			</td><td>
			<input type="text" name="usercode" id="usercode" value="" class="textEntry" onfocus="this.className = 'textEntry textEntryFocus';"  onblur="this.className = 'textEntry';" onKeyPress="javascript:BuscarItem(event,2)"/>

			<input type="submit" name="ok" value="<?php echo $msgstr["search"]?>" class="submit" onclick="javascript:return false" />
			<input type="submit" name="index" value="<?php echo $msgstr["list"]?>" class="submit" onClick="javascript:AbrirIndice('U',document.usersearch.usercode.value)"" />
			</td></table>
		</form>
	</div>
	<p>
<?php
}
if ($advanced_search){
?>
			<div class="searchBox">
				<form name=advancedsearch action=search_object.php method=post onsubmit="javascript:return false">
				<input type=hidden name=Indice>
				<table width=100%>
					<td width=100>
					<label for="searchExpr">
						<strong><?php echo $msgstr["adsearch"]?></strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
					</label>
					</td><td>
					<input type="text" name="usercode" id="usercode" value="" class="textEntry" onfocus="this.className = 'textEntry textEntryFocus';"  onblur="this.className = 'textEntry';" />

					<input type="submit" name="ok" value="<?php echo $msgstr["search"]?>" class="submit" onclick="javascript:return false" />
					<input type="submit" name="index" value="<?php echo $msgstr["index"]?>" class="submit" onClick="javascript:AbrirIndice('U',document.usersearch.usercode)"" />
					</td></table>
				</form>
			</div>
	<?php }?>
	<!--
	<br><br>

			<?php echo $msgstr["inventory"]?><input type=text name=inven onKeyPress="javascript:codes(event,1)"
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
		</table>-->
<?php
	}

    echo "</div></div>\n";
	include("../common/footer.php");
?>
<form name=procesar method=post  onsubmit="javascript:return false" >
<input type=hidden name=base value=biblo>
<input type=hidden name=cipar value=biblo.par>

<input type=hidden name=Opcion value="">
<input type=hidden name=inventory>
<input type=hidden name=usuario value"<?echo $arrHttp["usuario"]?>">
<br><br>
</body>
</html>
<script>
function PoliticasUsuario(){
		msgwin=window.open("","")
		msgwin.document.writeln("<html><body><font face=arial size=2 color=#222222>")
		<?php

		foreach ($politica as $key=>$ob){
			foreach($ob as $key1=>$obj1)
			if ($key1==$userdata[1]){
				$obj=explode('|',$obj1);
				$i=-1;
				foreach ($obj as $value){
					$i++;
					echo "msgwin.document.writeln(\"".$rows_title[$i]." = ".$value."<br>\")\n";
				}
				echo "msgwin.document.writeln(\"<p><hr><p>\")\n";
			}
		}

		?>
		msgwin.document.writeln("</body></html>")
}
</script>

