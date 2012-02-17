<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      index.php
 * @desc:      Create the menus and initial screen
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


include("../lang/dbadmin.php");
include("../lang/admin.php");
include("../common/get_post.php");
?>
	<html>
	<head>
<xMETA HTTP-EQUIV="Expires" CONTENT="Tue, 04 Dec 1993 21:29:02 GMT">

<script src=../dataentry/js/lr_trim.js></script>

<script languaje=javascript>

function CrearDb(){
	top.main.location=""
	top.main.location="menu_creardb.php"
}

function Users(){
	top.main.location="../usersadm/browse.php"
}

function Traducir(){
	top.main.location="menu_traducir.php"
}

function ModificarDb(){

	if (top.base==""){
		alert ("<?php echo $msgstr["seldb"]?>")
		return
	}
	top.main.location="menu_modificardb.php?base="+top.base
}

function Mantenimiento(){
	if (top.base==""){
		alert ("<?php echo $msgstr["seldb"]?>")
		return
	}
	top.main.location="menu_mantenimiento.php?base="+top.base
}

function AbrirAyuda(){
	msgwin=window.open("../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/toolbar_admin.html","Ayuda","status=yes,resizable=yes,toolbar=no,menu=no,scrollbars=yes,width=750,height=500,top=10,left=5")
	msgwin.focus()

}




</script>


	</head>
	<body bgcolor=#cccccc text=#000000 LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0 >
<form name=forma1>
	<link rel="STYLESHEET" type="text/css" href="../dataentry/js/dhtmlXToolbar.css">
	<script language="JavaScript" src="../dataentry/js/dhtmlXProtobar.js"></script>
	<script language="JavaScript" src="../dataentry/js/dhtmlXToolbar.js"></script>
	<script language="JavaScript" src="../dataentry/js/dhtmlXCommon.js"></script>
	<table width=100%>


		<td><div id="toolbarBox" style="width:100%;height:50;position:relative"></div></td>



      </table>
<script>
	//horizontal toolbar


	toolbar=new dhtmlXToolbarObject("toolbarBox","100%","30","ABCD");
	toolbar.setOnClickHandler(onButtonClick);

	toolbar.addItem(new dhtmlXToolbarDividerXObject('xedit'))
	<?php if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_MODIFYDB"]))
		echo 'toolbar.addItem(new dhtmlXImageButtonObject("../dataentry/img/database.png","32","32",10,"creardb","'.$msgstr["newdb"].'"))'."\n"?>
	toolbar.addItem(new dhtmlXToolbarDividerXObject('div_1'))
	toolbar.addItem(new dhtmlXImageButtonObject("../dataentry/img/barEdit.png","32","32",9,"modificardb","<?php echo $msgstr["updbdef"]?>"))
	toolbar.addItem(new dhtmlXToolbarDividerXObject('div_2'))
	<?php if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_MODIFYDB"]))
		echo 'toolbar.addItem(new dhtmlXImageButtonObject("../dataentry/img/barTool.png","32","32",9,"mantenimiento","'.$msgstr["maintenance"].'"))'."\n"?>
	toolbar.addItem(new dhtmlXToolbarDividerXObject('div_3'))
	<?php if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_MODIFYDB"]))
		echo 'toolbar.addItem(new dhtmlXImageButtonObject("../dataentry/img/translate.gif","50","32",9,"traducir","'.$msgstr["translate"].'"))'."\n"
	?>
	toolbar.addItem(new dhtmlXToolbarDividerXObject('div_5'))
    <?php if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_MODIFYDB"]))
		echo 'toolbar.addItem(new dhtmlXImageButtonObject("../dataentry/img/explore.gif","40","32",12,"explorar","'.$msgstr["expbases"].'>"))'."\n"?>
	<?php if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_MODIFYDB"]))
		echo 'toolbar.addItem(new dhtmlXImageButtonObject("../dataentry/img/barTool.png","32","32",9,"iah","'.$msgstr["iah-conf"].'"))'."\n"?>
	toolbar.addItem(new dhtmlXToolbarDividerXObject('div_6'))
	toolbar.addItem(new dhtmlXImageButtonObject("../dataentry/img/barHelp.png","32","32",14,"ayuda","<?php echo $msgstr["m_ayuda"]?>"))
	toolbar.addItem(new dhtmlXToolbarDividerXObject('div_7'))
	toolbar.addItem(new dhtmlXImageButtonObject("../dataentry/img/barHome.png","32","32",14,"home","<?php echo $msgstr["inicio"]?>"))
	toolbar.showBar();

	function onButtonClick(itemId,itemValue){
         top.main.location="blank.html"
		switch (itemId){
			case "creardb":
				CrearDb()
				break;
			case "b_lang":
				top.location.href="inicio_main.php?Opcion=admin&lang="+itemValue+"&cipar=bases.par&cambiolang=S"
				break
			case "modificardb":
				ModificarDb()
				break
			case "mantenimiento":
				Mantenimiento()
				break
			case "traducir":
				Traducir()
				break
			case "usuarios":
				Users()
				break;
			case "explorar":
				top.main.location.href="dirtree.php"
				break
			case "iah":
				if (top.base==""){
					alert ("<?php echo $msgstr["seldb"]?>")
					return
				}
				top.main.location.href="iah_edit_db.php?base="+top.base
				break
			case "ayuda":
				AbrirAyuda()
				break
			case "home":
				top.location.href="../common/inicio.php?reinicio=s";
				break
		}
	};


</script>

	</form>
	<script>
		top.ModuloActivo="dbadmin"
        top.main.location.href="../dataentry/inicio_base.php?base="+top.base+"&cipar="+top.cipar
	</script>
</body>
</html>

