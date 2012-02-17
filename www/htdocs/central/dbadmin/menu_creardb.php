<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      menu_creardb.php
 * @desc:      Implements the option to create a database
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
unset($_SESSION["FDT"]);
unset($_SESSION["PFT"]);
unset($_SESSION["FST"]);
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include ("../config.php");
$lang=$_SESSION["lang"];



include("../lang/admin.php");
include("../lang/soporte.php");
include("../lang/dbadmin.php");
include("../common/get_post.php");

//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";
include("../common/header.php")
?>
<script src=../dataentry/js/lr_trim.js></script>
<script languaje=javascript>


function Validar(){
	dbn=Trim(document.forma1.nombre.value)
	if (dbn==""){
		alert("<?php echo $msgstr["falta"]." ".$msgstr["dbn"]?>")
		return
	}
	ix=document.forma1.base_sel.options.length
	for (i=1;i<ix;i++){
		if (document.forma1.base_sel.options[i].value==dbn){
			alert("<?php echo $msgstr["bdexiste"]?>")
			return
		}
	}
	desc=Trim(document.forma1.desc.value)
	if (desc==""){
		alert("<?php echo $msgstr["falta"]." ".$msgstr["descripcion"]?>")
		return
	}
	ix=document.forma1.base_sel.selectedIndex
	if (ix<1){
		alert("<?php echo $msgstr["falta"]." ".$msgstr["cpdb"]?>")
		return
	}
	switch(ix){
		case 1:
			document.forma1.base.value=dbn
			document.forma1.desc.value=desc
			document.forma1.action="fdt.php"
			document.forma1.Opcion.value="new"
			document.forma1.submit()
			break
		case 2:
			document.forma1.action="winisis.php"
			document.forma1.submit()
			break
		default:
			document.forma1.action="crearbd_ex_copy.php"
			document.forma1.submit()
			break
	}

}
</script>
	</head>
	<body>
<?php
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
	$encabezado="s";
}else{
	$encabezado="";
}
?>
	<div class="sectionInfo">

		<div class="breadcrumb">
				<?php echo $msgstr["createdb"]?>
		</div>

		<div class="actions">
<?php if (isset($arrHttp["encabezado"])){
	echo "<a href=\"../common/inicio.php?reinicio=s\" class=\"defaultButton cancelButton\">
					<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
					<span><strong>". $msgstr["cancel"]."</strong></span>
				</a>
	";
}
?>
		</div>
		<div class="spacer">&#160;</div>
	</div>

	<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/admin.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/admin.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: menu_creardb.php</font>";
?>
	</div>
	<form method=post name=forma1 onsubmit="javascript:return false">
		<input type=hidden name=Opcion>
		<input type=hidden name=base>
		<?php if (isset($arrHttp["encabezado"])) echo "<input type=hidden name=encabezado value=s>\n"?>
		<div class="middle form">
			<div class="formContent">
	<!--			<h4>New Database</h4>-->

				<div id="formRow01" class="formRow">
					<label for="field01"><strong><?php echo $msgstr["dbn"]?></strong></label>
					<div class="frDataFields">
						<input type="text" name="nombre"  id="field01" value="" class="textEntry singleTextEntry" onfocus="this.className = 'textEntry singleTextEntry textEntryFocus';document.getElementById('formRow01').className = 'formRow formRowFocus';" onblur="this.className = 'textEntry singleTextEntry';document.getElementById('formRow01').className = 'formRow';" />
						<p>
					</div>
					<div class="spacer">&#160;</div>
				</div>
				<div id="formRow02" class="formRow">
					<label for="field02"><strong><?php echo $msgstr["descripcion"]?></strong></label>
					<div class="frDataFields">
						<input type=text name="desc" id="field02" class="textEntry singleTextEntry" onfocus="this.className = 'textEntry singleTextEntry textEntryFocus';document.getElementById('formRow02').className = 'formRow formRowFocus';" onblur="this.className = 'textEntry singleTextEntry';document.getElementById('formRow02').className = 'formRow';">
						<p>
					</div>
					<div class="spacer">&#160;</div>
				</div>

				<div id="formRow3" class="formRow formRowFocus">
					<label for="field3"><strong><?php echo $msgstr["createfrom"]?>:</strong></label>
					<div class="frDataFields">
	<!--					<input type=radio name=optbd value="~~NewDB"><?php echo $msgstr["newdb"]?><br>
						<input type=radio name=optbd value="~~NewDB"><?php echo $msgstr["winisisdb"]?><br>
						<input type=radio name=optbd value="~~NewDB"><?php echo $msgstr["cpdb"]?><br>  -->
						<select name="base_sel" id="field3" class="textEntry singleTextEntry">
							<option value=""></option>
							<option value="~~NewDb"><?php echo $msgstr["newdb"]?></option>
							<option value="~~WinIsis"><?php echo $msgstr["winisisdb"]?></option>
<?
$fp = file($db_path."bases.dat");
$bdatos=array();
foreach ($fp as $linea){
	if (!empty($linea)) {
		$bdatos[]=$linea;
		$b=explode('|',$linea);
		$llave=$b[0];
		if ($llave!="acces") echo "<option value=$b[0]>".$b[1];
	}

}
?>
						</select>
<!--						<p>
						<input type=checkbox value=ok> Create copies database  -->
					</div>
					<div class="spacer">&#160;</div>
				</div>

			</div>
		</div>
		<div class="formFoot">
			<div class="pagination">
				<a href="javascript:Validar()" class="singleButton singleButtonSelected">
						<span class="sb_lb">&#160;</span>
						[<?php echo $msgstr["continuar"]?>]
						<span class="sb_rb">&#160;</span>
					</a>
				<div class="spacer">&#160;</div>
			</div>
			<div class="spacer">&#160;</div>
		</div>
	</div>
	</form>
<? include("../common/footer.php");?>
	</body>
</html>
