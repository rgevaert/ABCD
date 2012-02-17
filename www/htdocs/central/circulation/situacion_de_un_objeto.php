<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      situacion_de_un_objeto.php
 * @desc:      Checks an item status
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
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
$lang=$_SESSION["lang"];

include("../lang/prestamo.php");
include("../lang/admin.php");
include("../common/get_post.php");
$arrHttp["base"]="biblo";
//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";
include("../common/header.php");

function LeerPft($pft_name,$base){
global $arrHttp,$db_path,$lang_db;
	$pft="";
	$archivo=$db_path.$base."/loans/".$_SESSION["lang"]."/$pft_name";
	if (!file_exists($archivo)) $archivo=$db_path.$base."/loans/".$lang_db."/$pft_name";
	$fp=file_exists($archivo);
	if ($fp){
		$fp=file($archivo);
		foreach ($fp as $value){
			$pft.=$value;
		}

	}
    return $pft;
}

function LeerNumeroClasificacion($base){global $db_path,$lang_db;
	$prefix_nc="";	$archivo=$db_path.$base."/loans/".$_SESSION["lang"]."/loans_conf.tab";
	if (!file_exists($archivo)) $archivo=$db_path.$base."/loans/".$lang_db."/loans_conf.tab";
	$fp=file_exists($archivo);
	if ($fp){
		$fp=file($archivo);
		foreach ($fp as $value){
			$ix=strpos($value," ");
			$tag=trim(substr($value,0,$ix));
			switch($tag){
				case "IN": $prefix_in=trim(substr($value,$ix));
					break;
				case "NC":
					$prefix_nc=trim(substr($value,$ix));
					break;
			}
		}
	}
	return $prefix_nc;}
?>
<script src=../dataentry/js/lr_trim.js></script>
<script>
document.onkeypress =
  function (evt) {
    var c = document.layers ? evt.which
            : document.all ? event.keyCode
            : evt.keyCode;
	if (c==13) EnviarForma()
    return true;
  }

function EnviarForma(){	if (Trim(document.inventorysearch.code.value)==""){		alert("<?php echo $msgstr["falta"]." ".$msgstr["control_n"]?>")
		return	}
<?php
if (file_exists($db_path."loans.dat"))
	echo 'document.inventorysearch.action="situacion_de_un_objeto_db_ex.php"';
else
    echo 'document.inventorysearch.action="situacion_de_un_objeto_ex.php"';
?>

	document.inventorysearch.submit()}

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

function AbrirIndice(Ix,Ctrl){
	switch (Ix){		case "S":
			bd_sel=document.inventorysearch.db.selectedIndex
			if (bd_sel<=0){				alert("debe seleccinar una base de datos")
				return			}
			bd_a=document.inventorysearch.db.options[bd_sel].value
			b=bd_a.split('|')
			bd=b[0]
			prefijo=b[2]
			formato=b[3]
			AbrirIndiceAlfabetico(Ctrl,prefijo,"","",bd,bd+".par","3",1,"",formato)
			break
		case "I":

			AbrirIndiceAlfabetico(Ctrl,"CN_","","","loanobjects","copies.par","1",1,"","v1")
			break
	}
}

function PresentarDiccionario(){
		msgwin=window.open("","Diccionario","scrolling, height=400")
		ix=document.searchBox.indexes.selectedIndex
		if (ix<1){
			alert("<?php echo $msgstr["selcampob"]?>")
			return
		}
		sel=document.searchBox.indexes.options[ix].value
		t=sel.split('|')

		document.diccionario.campo.value=escape(t[0])
		document.diccionario.prefijo.value=t[2]
		document.diccionario.id.value=t[1]
		document.diccionario.Diccio.value="document.inventorysearch.searchExpr"
		document.diccionario.submit()
		msgwin.focus()
	}
</script>
<?php
$encabezado="";
echo "<body>\n";
include("../common/institutional_info.php");
$link_u="";
if (isset($arrHttp["usuario"]) and $arrHttp["usuario"]!="") $link_u="&usuario=".$arrHttp["usuario"];
?>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["ecobj"]?>
	</div>
	<div class="actions">
		<?php include("submenu_prestamo.php");?>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/circulation/object_statment.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/circulation/object_statment.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: situacion_de_un_objeto.php</font>\n";
?>
	</div>
<div class="middle list">
	<div class="searchBox">
	<form name=inventorysearch action="situacion_de_un_objeto_ex.php" method=post onsubmit="javascript:return false">
<?php
//READ BASES.DAT TO FIND THOSE DATABASES IF NOT WORKING WITH COPIES DATABASES
$sel_base="N";
if (file_exists($db_path."loans.dat")){
	$fp=file($db_path."loans.dat");
	$sel_base="S";
?>
	<table width=100% border=0>
		<td width=150>
		<label for="dataBases">
			<strong><?php echo $msgstr["basedatos"]?></strong>
		</label>
		</td><td>
		<select name=db>
		<option></option>
<?php
	foreach ($fp as $value){		$value=trim($value);
		$v=explode('|',$value);
		//SE LEE EL PREFIJO PARA OBTENER EL NUMERO DE CLASIFICACION DE LA BASE DE DATOS
		$prefijo_nc=LeerNumeroClasificacion($v[0]);
		$pft_nc=LeerPft("loans_cn.pft",$v[0]);
		$value.="|".$prefijo_nc."|".urlencode($pft_nc);
		echo "<option value='$value'>".$v[1]."</option>";
	}
?>
		</select>
		</td>
	</table>
	</div>

<?php }?>
	<div class=\"spacer\">&#160;</div>
	<div class="searchBox">

	<input type=hidden name=Opcion value=inventario>
	<input type=hidden name=desde value=1>
	<table width=100%>
		<td width=150>
		<label for="searchExpr">
			<strong><?php echo $msgstr["controlnum"]?></strong>
		</label>
		</td><td>
		<input type="text" name="code" id="searchExpr" value="" class="textEntry" onfocus="this.className = 'textEntry';"  onblur="this.className = 'textEntry';" />

		<input type="button" name="list" value="<?php echo $msgstr["list"]?>" class="submit" onclick="javascript:AbrirIndice('<?php if ($sel_base=="S") echo "S"; else echo "I";?>',document.inventorysearch.code)"/>
		<input type="submit" name="ok" value="<?php echo $msgstr["search"]?>" class="submit" onClick=EnviarForma() />
		</td></table>
	</form>
	</div>
</div>
<form name=EnviarFrm method=post>
<input type=hidden name=base value="<?php echo $arrHttp["base"]?>">
<input type=hidden name=usuario value="">
<input type=hidden name=desde value=1>
</form>
<form name=diccionario method=post action=diccionario.php target=Diccionario>
	<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
	<input type=hidden name=cipar value=<?php echo $arrHttp["base"]?>.par>
	<input type=hidden name=prefijo>
	<input type=hidden name=Formato>
	<input type=hidden name=campo>
	<input type=hidden name=id>
	<input type=hidden name=Diccio>
	<input type=hidden name=from value=$desde>
	<input type=hidden name=Opcion value=diccionario>
	<input type=hidden name=Target value=s>
	<input type=hidden name=Expresion>
</form>
<?php include("../common/footer.php");
echo "</body></html>" ;

?>