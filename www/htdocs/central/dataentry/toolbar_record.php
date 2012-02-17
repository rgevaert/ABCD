<?php
echo "<div class=\"helper\" style=\"height:23px\">\n" ;
if (!isset($fmt_test)){
	if (isset($default_values)){
		echo "<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/valdef.html target=_blank>".$msgstr["m_ayuda"]."</a>&nbsp &nbsp;";
	    if (isset($_SESSION["permiso"]["EDHLPSYS"])) echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/valdef.html target=_blank>". $msgstr["edhlp"]."</a>";
	}else{
		if (isset($arrHttp["capturar"]) and $arrHttp["capturar"]=="S"){
			echo "<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/copy_record.html target=_blank>".$msgstr["m_ayuda"]."</a>&nbsp &nbsp;";
	    	if (isset($_SESSION["permiso"]["EDHLPSYS"])) echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/copy_record.html target=_blank>". $msgstr["edhlp"]."</a>";
		}else{
			echo "<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/dataentry.html target=_blank>".$msgstr["m_ayuda"]."</a>&nbsp &nbsp;";
	    	if (isset($_SESSION["permiso"]["EDHLPSYS"])) echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/dataentry.html target=_blank>". $msgstr["edhlp"]."</a>";
	  	}
	}
	echo "<font color=white>&nbsp; &nbsp; Script: fmt.php</font>";
}

?>
</div>
	<div class="middle form">
		<div class="formContent">
<script language ="javascript" type="text/javascript">



function scrollingDetector(){

if (navigator.appName == "Microsoft Internet Explorer")

{

//alert(document.documentElement.scrollTop);

document.getElementById("myDiv").style.top = document.documentElement.scrollTop;

}



 // For FireFox

else{ document.getElementById("myDiv").style.top = window.pageYOffset + "px";      }



}



function startScrollingDetector()

{

setInterval("scrollingDetector()",1000);

}
startScrollingDetector()


</script>
<div id="myDiv" style="position:absolute; top:0px; left:600px;" >
<table bgcolor=#cccccc>
<td>
<?php


//CHECK IF THERE IS A VALIDATION FORMAT
	$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".val";
	if (!file_exists($archivo)) $archivo=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$arrHttp["base"].".val";
	if (file_exists($archivo)){
		$rec_validation="S";
	}
    if (!isset($arrHttp["encabezado"])){
	 	switch ($arrHttp["Opcion"]){
			case "ver":
			case "cancelar":
			case "actualizar":
			case "buscar":
			case "presentar_captura":
			case "dup_record":
				if (isset($_SESSION["permiso"]["CENTRAL_EDREC"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"])) {					echo " &nbsp;<a href=\"javascript:top.Menu('editar')\" title=\"".$msgstr["m_editar"]."\"><img src=img/toolbarEdit.png alt=\"".$msgstr["m_editar"]."\" style=\"border:0;\"></a>  &nbsp;\n";
				}
				if (isset($_SESSION["permiso"]["CENTRAL_CREC"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"])) {					echo " &nbsp;<a href=\"javascript:top.Menu('dup_record')\" title=\"".$msgstr["m_copyrec"]."\"><img src=img/toolbarCopy.png alt=\"".$msgstr["m_copyrec"]."\" style=\"border:0;\"></a>  &nbsp;\n";
				}
				if (isset($_SESSION["permiso"]["CENTRAL_DELREC"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"])) echo "<a href=\"javascript:top.Menu('eliminar')\" title=\"".$msgstr["m_eliminar"]."\"><img src=img/toolbarDelete.png alt=\"".$msgstr["m_eliminar"]."\" style=\"border:0;\"></a> &nbsp;\n";
				if (isset($_SESSION["permiso"]["CENTRAL_Z3950CAT"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"])) echo "<a href=\"javascript:top.Menu('edit_Z3950')\" title=\"Z39.50\"><img src=img/z3950.png alt=\"Z39.50\" style=\"border:0;\"></a>\n";
				if (isset($_SESSION["permiso"]["CENTRAL_EDREC"]) or isset($_SESSION["permiso"]["CENTRAL_CREC"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"]))
					if (isset($rec_validation)) echo "<a href='javascript:top.Menu(\"recvalidation\")' title=\"".$msgstr["rval"]."\"><img src=img/recordvalidation_p.gif alt=\"".$msgstr["rval"]."\" style=\"border:0;\"></a> &nbsp;\n";
				if (isset($arrHttp["db_copies"])){					if (isset($_SESSION["permiso"]["CENTRAL_ADDCOP"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"])){  //THE DATABASES HAS COPIES DATABASE
						echo "<a href='javascript:top.toolbarEnabled=\"\";top.Menu(\"addcopies\")' title='".$msgstr["m_addcopies"]."'><img src=img/db_add.png alt='".$msgstr["m_addcopies"]."' border=0></a> &nbsp;\n";
						echo "<a href='javascript:top.toolbarEnabled=\"\";top.Menu(\"editdelcopies\")' title='".$msgstr["m_editdelcopies"]."'><img src=img/database_edit.png alt='".$msgstr["m_editdelcopies"]."' border=0></a> &nbsp;\n";				    }
					if (isset($_SESSION["permiso"]["CENTRAL_ADDLO"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"]))
						echo "<a href='javascript:top.toolbarEnabled=\"\";top.Menu(\"addloanobjects\")' title='".$msgstr["addloansdb"]."'><img src=img/add.gif alt='".$msgstr["addloansdb"]."' border=0></a> \n";
				}
				echo " &nbsp;";
				break;
			case "editar":
			case "capturar":
			case "crear":
			case "reintentar":
				if ($OpcionDeEntrada!="captura_bd"){
				   	echo " &nbsp; <a href='javascript:top.Menu(\"cancelar\")' title=\"".$msgstr["m_cancelar"]."\"><img src=img/toolbarCancelEdit.png alt='".$msgstr["m_cancelar"]."' border=1><a> &nbsp; \n";
					echo "<a href='javascript:EnviarForma()' title=\"".$msgstr["m_guardar"]."\"><img src=img/toolbarSave.png alt=\"".$msgstr["m_guardar"]."\"><a> &nbsp; \n";
				}
	//          echo "<input type=button name=capturar value=\"".$msgstr["m_capturar"]."\">\n";
	//			echo "<input type=button name=capturar value=\"".$msgstr["m_z3950"]."\">\n";
				break;
		}
	}
	echo "</td></table></div></div>\n";

    if (isset($arrHttp["Expresion"])){    	echo "<font face=arial style=font-size:10px>".$msgstr["expresion"].": ".stripslashes($arrHttp["Expresion"])."";
    	if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_SAVEXPR"])){
    ?>
    		<div id=headerDiv_1 style="headerDiv">
	    	<div id=titleText_1 class=titleText> <img src=../dataentry/img/barSearch.png border=0 align=middle><a id=myHeader_1 style="myHeader" href="javascript:toggle('contentDiv_1','myHeader_1');" ><?php echo $msgstr["savesearch"]?></a></div>
			<div id=contentDiv_1 style="display:none; hide:block">
     		<?php echo $msgstr["r_desc"]?>: <input type=text name=Descripcion size=40>
     			&nbsp; &nbsp; <input type=button value="<?php echo $msgstr["savesearch"]?>" onclick=GuardarBusqueda()>
			</div>
			</div>
 <?php
 		}else{ 			echo "<p>"; 		}
 		echo "</font>";
    }

//    echo $arrHttp["Opcion"];
?>

