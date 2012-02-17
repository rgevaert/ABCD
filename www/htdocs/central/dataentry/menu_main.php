<?php
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}

include("../config.php");
include ("../lang/admin.php");
include("../common/get_post.php");
echo "<script>permiso='".$_SESSION["permiso"]."'</script>";
include("../common/header.php");
?>

<script>
lang='<?php echo $_SESSION["lang"]?>'
document.onkeypress =
  function (evt) {
    var c = document.layers ? evt.which
            : document.all ? event.keyCode
            : evt.keyCode;
	if (c==13) top.Menu('ira')
    return true;
  };

function AbrirAyuda(){
	msgwin=window.open("../documentacion/ayuda.php?help="+lang+"/dataentry_toolbar.html","Ayuda","status=yes,resizable=yes,toolbar=no,menu=no,scrollbars=yes,width=750,height=500,top=10,left=5")
		msgwin.focus()

}

function GenerarDespliegue(){
	base=top.base
	if (base==""){
		alert("<?php echo $msgstr["seldb"]?>")
		return
	}
	if(top.xeditar=="S"){
		alert("<?php echo $msgstr["aoc"]?>")

		return
	}
	i=document.forma1.formato.selectedIndex
	if (i==-1){
	}else{
	  	pft=document.forma1.formato.options[i].value
		if (pft!='ALL') {
		}else{
		}
	}
	if (top.mfn>0){
		top.mfn=top.mfn-1
		top.Menu('proximo')
	}
}

function GenerarWks(){
	base=top.base
	if (base==""){
		alert("<?php echo $msgstr["seldb"]?>")

	}
	if(top.xeditar=="S"){
		alert("<?php echo $msgstr["aoc"]?>")


	}
	i=document.forma1.wks.selectedIndex
	if (i==-1){
		top.wks=""
	}else{
	  	top.wks=document.forma1.wks.options[i].value
	}
}


</script>
</head>
<body bgcolor=#EDF3F3 text=#000000 LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0 >
<form name=forma1 onsubmit="return false" method=post>
<link rel="STYLESHEET" type="text/css" href="js/dhtmlXToolbar.css">
<script language="JavaScript" src="js/dhtmlXProtobar.js"></script>
<script language="JavaScript" src="js/dhtmlXToolbar.js"></script>
<script language="JavaScript" src="js/dhtmlXCommon.js"></script>
<table width=100% bgcolor=#d4d0c8>
	<td align=center valign=top ><font style="font-size:10px;font-family:arial"> <?php echo $msgstr["m_ir"]?>:<br>
		<input type=text  name=ir_a size=15 value='' onClick="javascript:this.value=''" style="font-size:10px;font-family:arial">
   	</td>
	<td ><div id="toolbarBox" style="height:30;position:relative"></div></td>
    <td align=right>
       	<table cellspacing=0 cellpadding=0><td align=right><font style="font-size:7pt"><?php echo $msgstr["displaypft"]?>:</td>
			<td><select name=formato onChange=Javascript:GenerarDespliegue()  style="width:90;font-size:8pt;font-family:arial narrow">
				<option></option>
				</select>
			</td>
	  	 <tr><td align=right><font style="font-size:7pt"><?php echo $msgstr["fmt"]?>:&nbsp; </td><td>
				<select name=wks onChange=Javascript:GenerarWks() style="width:90;font-size:8pt;font-family:arial narrow">
					<option></option>
				</select>
			</td>
		</table>
	</td>
	<td width=3>&nbsp;</td>
</table>
<script>
	//horisontal toolbar
	toolbar=new dhtmlXToolbarObject("toolbarBox","400","24","ABCD");
	toolbar.setOnClickHandler(onButtonClick);
	toolbar.addItem(new dhtmlXImageButtonObject('img/barArrowLeft2.png',18,24,1,'0_primero','<?php echo $msgstr["m_primero"]?>'))
    toolbar.addItem(new dhtmlXImageButtonObject('img/barArrowLeft.png',18,24,2,'0_anterior','<?php echo $msgstr["m_anterior"]?>'))
    toolbar.addItem(new dhtmlXImageButtonObject('img/barArrowRight.png',18,24,3,'0_siguiente','<?php echo $msgstr["m_siguiente"]?>'))
    toolbar.addItem(new dhtmlXImageButtonObject('img/barArrowRight2.png',18,24,4,'0_ultimo','<?php echo $msgstr["m_ultimo"]?>'))
    toolbar.addItem(new dhtmlXSelectButtonObject('select',',mfn,search','<?php echo $msgstr["browse"]?>,Mfn,<?php echo $msgstr["busqueda"]?>','browse',80,80,''))
    toolbar.addItem(new dhtmlXToolbarDividerXObject('div_1'))
    toolbar.addItem(new dhtmlXImageButtonObject("img/toolbarSearch.png","16","16",5,"1_buscar","<?php echo $msgstr["m_buscar"]?>"))
	toolbar.addItem(new dhtmlXImageButtonObject("img/toolbarOrder.png","16","16",6,"1_alfa","<?php echo $msgstr["m_indice"]?>"))
	toolbar.addItem(new dhtmlXToolbarDividerXObject('div_2'))
	<?php
	if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_CREC"])){	?>		toolbar.addItem(new dhtmlXImageButtonObject("img/toolbarNew.png","16","16",7,"2_nuevo","<?php echo $msgstr["m_crear"]?>"))
	<?php }
	if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_CAPTURE"])){
	?>
		toolbar.addItem(new dhtmlXImageButtonObject("img/toolbarCopy.png","18","24",9,"2_capturar","<?php echo $msgstr["m_capturar"]?>"))
	<?php }
	if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_Z3950CAT"])){
	?>
		toolbar.addItem(new dhtmlXImageButtonObject("img/z3950.png","18","16",19,"2_z3950","<?php echo $msgstr["m_z3950"]?>"))
	<?php }
	if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_VALDEF"])){
	?>
		toolbar.addItem(new dhtmlXToolbarDividerXObject('xedit'))
		toolbar.addItem(new dhtmlXSelectButtonObject('defaultval',',editdv,deletedv','<?php echo $msgstr["valdef"]?>,<?php echo $msgstr["editar"]?>,<?php echo $msgstr["eliminar"]?>','',80,80,''))
		toolbar.addItem(new dhtmlXToolbarDividerXObject('div_5'))
	<?php }
	if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_PREC"])){
	?>
		toolbar.addItem(new dhtmlXImageButtonObject("img/toolbarPrint.png","16","24",12,"3_imprimir","<?php echo $msgstr["m_reportes"]?>"))
	<?php }
	if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_UTILS"])  or isset($_SESSION["permiso"]["CENTRAL_IMPEX"])  or isset($_SESSION["permiso"]["CENTRAL_GLOBC"])){
	?>
		toolbar.addItem(new dhtmlXImageButtonObject("img/toolbarTool.png","16","24",13,"config","<?php echo $msgstr["mantenimiento"]?>"))
	<?php }?>
	toolbar.addItem(new dhtmlXToolbarDividerXObject('div_5'))

<?php $select="";
	if (file_exists($db_path.$arrHttp["base"]."/modulos.dat")){
		$ini=parse_ini_file($db_path.$arrHttp["base"]."/modulos.dat");
    	$select="case \"".$ini["OPCION"]."\":
    	top.main.location.href=\"".$ini["LOCATION"]."?base=".$arrHttp["base"]."\"
    	break
    	";
		echo "toolbar.addItem(new dhtmlXImageButtonObject(\"img/".$ini["IMAGEN"]."\",\"18\",\"24\",13,\"".$ini["OPCION"]."\",\"".$ini["NOMBRE"]."\"))\n";
	}
if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_STATGEN"])  or isset($_SESSION["permiso"]["CENTRAL_STATCONF"])){
?>
	toolbar.addItem(new dhtmlXImageButtonObject("img/grafico.gif","18","24",13,"stats","<?php echo $msgstr["estadisticas"]?>"))
<?PHP }?>
	toolbar.addItem(new dhtmlXImageButtonObject("img/toolbarHelp.png","16","24",14,"5_ayuda","<?php echo $msgstr["m_ayuda"]?>"))
	toolbar.addItem(new dhtmlXToolbarDividerXObject('div_6'))
	toolbar.addItem(new dhtmlXImageButtonObject("img/toolbarHome.png","16","24",14,"home","<?php echo $msgstr["inicio"]?>"))
	toolbar.showBar();
	function onButtonClick(itemId,itemValue){
		switch (itemId){
			<?php echo $select;?>
			case "select":
				switch (itemValue){
					case "mfn":
						top.browseby="mfn"
						top.mfn=top.Mfn_Search -1
						if (top.mfn<=0) top.mfn=1
						top.Menu("proximo")
						break
					case "search":
						if (top.Expresion==""){
							alert("<?php echo $msgstr["faltaexpr"]?>")
							var item=top.menu.toolbar.getItem('select');
    						item.selElement.options[0].selected =true
							return
						}
						top.browseby="search"
						top.mfn=top.Search_pos -1
						if (top.mfn<=0) top.mfn=1
						top.Menu("proximo")
						break
				}
				break
			case "defaultval":
				top.Menu(itemValue)
				var item=top.menu.toolbar.getItem('defaultval');
				item.selElement.options[0].selected =true
				break
			case "database":
				top.Menu('database')
				break;
			case "b_lang":
				top.location.href="inicio_main.php?Opcion=admin&lang="+itemValue+"&cipar=bases.par&cambiolang=S"
				return;
			case "config":
				top.Menu('administrar')
				break;
			case "0_ir":
				top.Menu('ira')
				break
			case "0_primero":
				top.Menu('primero')
				break
			case "0_anterior":
				top.Menu('anterior')
				break
			case "0_siguiente":
				top.Menu('proximo')
				break
			case "0_ultimo":
				top.Menu('ultimo')
				break
			case "1_alfa":
				top.Menu('alfa')
				break
			case "1_buscar":
				top.Menu('buscar')
				break
			case "1_tabla":
				top.Menu('tabla')
				break
			case "2_nuevo":
				top.Menu('nuevo')
				break
			case "2_editar":
				top.Menu('editar')
				break
			case "2_z3950":
				top.Menu('z3950')
				break
			case "2_capturar":
				top.Menu('capturar_bd')
				break
			case "4_eliminar":
				top.Menu('eliminar')
				break
			case "4_guardar":
				if (top.xeditar!="S"){
  					alert("<?php echo $msgstr["menu_edit"]?>")
    				return
  				}
				top.main.EnviarForma()
				break
			case "4_cancelar":
				if (top.xeditar!="S" && top.xeditar!="valdef"){
  					alert("<?php echo $msgstr["menu_canc"]?>")
    				return
  				}
				top.Menu('cancelar')
				break
			case "addcopies":
			    top.Menu('addcopies')
			    break
			case "5_ayuda":
				AbrirAyuda()
				break
			case "3_cglobal":
				top.Menu('global')
				break
			case "3_imprimir":
				top.Menu('imprimir')
				break
			case "stats":
				top.Menu('stats')
				break;
			case "home":
				top.Menu('home')
                break;
		}
	};


</script>

	</form>
	<script>
		top.ModuloActivo="catalog"

	</script>
<script>
<?php
unset($fp);
if (isset($arrHttp["base"])){
	if (file_exists($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat")){
		$fp = file($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat");
	}else{
		if (file_exists($db_path.$arrHttp["base"]."/pfts/".$lang_db."/formatos.dat")){
			$fp = file($db_path.$arrHttp["base"]."/pfts/".$lang_db."/formatos.dat");
		}
	}
	$i=-1;
	if (isset($fp)) {
		foreach($fp as $linea){
			if (trim($linea)!="") {
				$linea=trim($linea);
				$ll=explode('|',$linea);
				$cod=$ll[0];
				$nom=$ll[1];
				if (isset($_SESSION["permiso"][$arrHttp["base"]."_pft_ALL"]) or isset($_SESSION["permiso"][$arrHttp["base"]."_pft_".$ll[0]])
						or isset($_SESSION["permiso"]["CENTRAL_ALL"])){
					$i=$i+1;
					echo "if (top.ModuloActivo==\"catalog\") top.menu.document.forma1.formato.options[$i]=new Option('$nom','$cod')\n";
				}
			}
		}

	}else{		echo "document.forma1.formato.options.length=0\n";	}
	$i=$i+1;
	if (isset($_SESSION["permiso"][$arrHttp["base"]."_pft_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"])){
		echo "document.forma1.formato.options[$i]=new Option('".$msgstr["noformat"]."','')\n";
		echo "document.forma1.formato.options[$i+1]=new Option('".$msgstr["all"]."','ALL')\n";
	}
	unset($fp);
	//Se leen las hojas de entrada disponibles
	if (file_exists($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/formatos.wks")){
		$fp = file($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/formatos.wks");
	}else{
		if (file_exists($db_path.$arrHttp["base"]."/def/".$lang_db."/formatos.wks"))
			$fp = file($db_path.$arrHttp["base"]."/def/".$lang_db."/formatos.wks");
	}
	if (isset($_SESSION["permiso"][$arrHttp["base"]."_fmt_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"]))
		$i=0;
	else
		$i=-1;
	$wks_p=array();
	$wks_p=array();
	if (isset($fp)) {
		foreach($fp as $linea){
			if (trim($linea)!="") {
				$linea=trim($linea);
				$l=explode('|',$linea);
				$cod=trim($l[0]);
				$nom=trim($l[1]);
				if (isset($_SESSION["permiso"][$arrHttp["base"]."_fmt_ALL"]) or isset($_SESSION["permiso"][$arrHttp["base"]."_fmt_".$cod] )
						or isset($_SESSION["permiso"]["CENTRAL_ALL"])){
					$i=$i+1;
					$wks_p[$cod]="Y";
					echo "if (top.ModuloActivo==\"catalog\") top.menu.document.forma1.wks.options[$i]=new Option('$nom','$cod')\n";
				}
			}
		}
	}
	$i=$i+1;
}

//Se lee la tabla de tipos de registro
unset($fp);
if (file_exists($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/typeofrecord.tab")){
	$fp = file($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/typeofrecord.tab");
}else{
	if (file_exists($db_path.$arrHttp["base"]."/def/".$lang_db."/typeofrecord.tab"))
		$fp = file($db_path.$arrHttp["base"]."/def/".$lang_db."/typeofrecord.tab");
}
$i=0;
$typeofr="";
if (isset($fp)) {
	foreach($fp as $linea){
           if ($i==0){
           	$l=explode(" ",$linea);
           	echo "top.tl='".trim($l[0])."'\n";
           	if (isset($l[1]))
           		echo "top.nr='".trim($l[1])."'\n";
           	else
           	    echo "top.nr=''\n";
           	$i=1;
           }else{
			if (trim($linea)!="") {
				$l=explode('|',$linea);
				$cod=$l[0];
				$ix=strpos($cod,".");
				$cod=substr($cod,0,$ix);
				if (isset($wks_p[$cod]))
					$typeofr.=trim($linea)."$$$";
    		}
		}
	}
	echo "top.typeofrecord=\"$typeofr\"\n";
}else{
	echo "top.typeofrecord=\"\"\n";
}
if (isset($arrHttp["inicio"]) and $arrHttp["inicio"]=="s"){
	echo 'top.main.location.href="inicio_base.php?inicio=s&base="+top.base+"&cipar="+top.base+".par&per="+top.db_permiso';
}else{
	echo "url=top.main.location.href
	top.main.location.href=url\n";
}
?>
</script>
</body>
</html>

