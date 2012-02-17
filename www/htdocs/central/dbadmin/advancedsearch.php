<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      advancedsearch.php
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
$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");;

include("../common/get_post.php");
$archivo=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/camposbusqueda.tab";
if (!file_exists($archivo)) $archivo= $db_path.$arrHttp["base"]."/pfts/".$lang_db."/camposbusqueda.tab";
if (file_exists($archivo)){
	$arrHttp["Opcion"]="update";
}else{
	$arrHttp["Opcion"]="new";
}
include("../common/header.php");


?>
<link rel="STYLESHEET" type="text/css" href="../css/dhtmlXGrid.css">
	<link rel="STYLESHEET" type="text/css" href="../css/dhtmlXGrid_skins.css">
	<script  src="../dataentry/js/dhtml_grid/dhtmlXCommon.js"></script>
	<script  src="../dataentry/js/dhtml_grid/dhtmlXGrid.js"></script>
	<script  src="../dataentry/js/dhtml_grid/dhtmlXGridCell.js"></script>
	<script  src="../dataentry/js/dhtml_grid/dhtmlXGrid_drag.js"></script>
	<script  src="../dataentry/js/dhtml_grid/dhtmlXGrid_excell_link.js"></script>
 	<script  src="../dataentry/js/lr_trim.js"></script>
	<script languaje=javascript>
		pl_type=""
		Opcion="<?php echo $arrHttp["Opcion"]?>"
		valor=""
		prefix=""
		fila=""
		columna=11

		function Asignar(){
			mygrid.cells2(fila,columna).setValue(valor)
			mygrid.cells2(fila,12).setValue(prefix)
			closeit()
		}
		function Capturar_Grid(){
			cols=mygrid.getColumnCount()
			rows=mygrid.getRowsNum()
			VC=""
			for (i=0;i<rows;i++){
				if (Trim(mygrid.cells2(i,0).getValue())!=""){
					if (VC!="") VC=VC+"\n"
					for (j=0;j<cols;j++){
						cell=mygrid.cells2(i,j).getValue()
						if (j!=13) VC=VC+cell+'|'
					}
				}
			}
			return VC

		}


		function Enviar(){

			document.forma1.txt.value=Capturar_Grid()
			document.forma1.submit()
		}

		function Test(){
			if (Trim(document.fst.Mfn.value)==""){
				alert("<?php echo $msgstr["mismfn"]?>")
				return
			}
			msgwin=window.open("","FST_Test")
			msgwin.document.close()
			msgwin.focus()
			document.test.Mfn.value=document.fst.Mfn.value
			document.test.ValorCapturado.value=Capturar_Grid()
			document.test.submit()

		}
	</script>
<body>
<?php
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
	$encabezado="&encabezado=s";
}else{	$encabezado="";}
?>
<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["advsearch"].": ".$arrHttp["base"]?>
	</div>
	<div class="actions">
<?php echo "<a href=\"menu_modificardb.php?base=".$arrHttp["base"]."$encabezado\" class=\"defaultButton cancelButton\">";
?>
<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
<span><strong><?php echo $msgstr["cancel"]?></strong></span>
</a>
</div>
<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/asearch_schema.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp; &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/asearch_schema.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: advancedsearch.php";
?>
</font>
	</div>
<div class="middle form">
			<div class="formContent">


<form name=advancedsearch>
<table width=100% border=0>
   	<td width=480 valign=top border=0>
   		<table width=100%>
	        <tr>
			<td>
				<a href="javascript:void(0)" onclick="mygrid.addRow((new Date()).valueOf(),['','',''],mygrid.getRowIndex(mygrid.getSelectedId()))"><?php echo $msgstr["addrowbef"]?></a>
				&nbsp; &nbsp; &nbsp;<a href="javascript:void(0)" onclick="mygrid.deleteSelectedItem()"><?php echo $msgstr["remselrow"]?></a>
			<!--	&nbsp; &nbsp; &nbsp;<a href="javascript:void(0)" onclick=Organize()>Organize FST</a><br> -->
			</td>
			<td></td>
			<tr>
				<td valign=top>
					<div id="gridbox" width="100%" height="250px" style="left:0;top:0;background-color:white;overflow:hidden"></div>
				</td>

			</tr>
			<tr>
				<td>
					&nbsp; &nbsp; <a href=javascript:Enviar()><?php echo $msgstr["update"]?></a>  &nbsp; &nbsp;
					<?php if (!isset($arrHttp["encabezado"]))
						echo "<a href=menu_modificardb.php?base=".$arrHttp["base"].$msgstr["cancel"]."</a>\n";
						?>
	 			</td>
			</tr>
		</table>
	</td>
	<td valign=top>
<iframe id="cframe" src="fst_leer.php?base=<?php echo $arrHttp["base"]?>" width=100% height=450 scrolling=yes name=fdt></iframe>
	</td>
</table>
<script>
	mygrid = new dhtmlXGridObject('gridbox');
    mygrid.setSkin("xp");
   // mygrid.enableMultiline(true);

	mygrid.setImagePath("../imgs/");

	mygrid.setHeader("<?php echo $msgstr["fn"]?>, Fst Id, <?php echo $msgstr["prefix"]?>");
	mygrid.setInitWidths("300,50,50")
	mygrid.setColAlign("left,left,left")
	mygrid.setColTypes("ed,ed,ed");
    mygrid.enableAutoHeigth(true,400);

    mygrid.enableDragAndDrop(true);
	//mygrid.enableLightMouseNavigation(true);
	//mygrid.enableMultiselect(false);

	mygrid.init();
	if (Opcion=="new")  {		for (i=0;i<30;i++){
			id=(new Date()).valueOf()
			mygrid.addRow(id,['','',''],i)
        }

	}else{
<?php
	if ($arrHttp["Opcion"]=="update"){
		$fp=file($archivo);
		$i=-1;
		$t=array();
		foreach ($fp as $value){
			if (!empty($value)) {
				$t=explode('|',$value);
				$i++;
				echo "i=$i\n
				id=(new Date()).valueOf()
				mygrid.addRow(id,['".trim($t[0])."','".trim($t[1])."','".trim($t[2])."'],i)\n
				mygrid.setRowTextStyle( id,\"font-family:courier new;\")\n ";
			}		}
   }
?> }
	i++
	for (j=i;j<i+10;j++){
		mygrid.addRow((new Date()).valueOf(),['','',''],j)
	}


	mygrid.clearSelection()
	mygrid.setSizes();
</script>
<br><br>
</form>
<form name=forma1 action=advancedsearch_update.php method=post>
<input type=hidden name=txt>
<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
<input type=hidden name=archivo value='camposbusqueda.tab'>
<?php if (isset($arrHttp["encabezado"]))
	echo "<input type=hidden name=encabezado value=S>\n ";

?>
</form>

</div></div>
<?php include("../common/footer.php")?>
</body>
</html>
