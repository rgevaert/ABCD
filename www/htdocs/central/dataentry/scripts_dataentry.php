<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      scripts_dataentry.php
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
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>
 *  
 * == END LICENSE ==
*/
?>
<!-- save search expression style -->
<style>
	#headerDiv, #contentDiv {
	float: right;
	width: 510px;
	}
	#titleText {
	float: right;
	font-size: 1.2em;
	font-weight: bold;
	margin: 5px 10px;
	}
	headerDiv {
	background-color: #ffffff;
	color: #000000;
	}
	contentDiv {
	background-color: #FFE694;
	}
	myContent {
	margin: 5px 10px;
	}
	headerDiv a {
	float: left;
	margin: 10px 10px 5px 5px;
	}
	headerDiv a:hover {
	color: #;
	}
</style>

<style type="text/css">
#wrapper {
	text-align:left;
	margin:0 auto;
	width:100%;
	xmin-height:10px;
	xborder:1px solid #ccc;
	padding:0px;
}


a {
	color:blue;
	cursor:pointer;
}


#myvar {
	border:1px solid #ccc;
	background:#ffffff;
	padding:2px;
}
</style>

<!-- calendar stylesheet -->
  <link rel="stylesheet" type="text/css" media="all" href="../dataentry/calendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />
  <!-- main calendar program -->
  <script type="text/javascript" src="../dataentry/calendar/calendar.js"></script>
  <!-- language for the calendar -->
  <script type="text/javascript" src="../dataentry/calendar/lang/calendar-<?php echo $_SESSION["lang"]?>.js"></script>
  <!-- the following script defines the Calendar.setup helper function, which makes
       adding a calendar a matter of 1 or 2 lines of code. -->
  <script type="text/javascript" src="../dataentry/calendar/calendar-setup.js"></script>

<script language=javascript src=../dataentry/js/campos.js></script>
<script language=Javascript src=../dataentry/js/windowdhtml.js></script>
<script language=Javascript src=../dataentry/js/lr_trim.js></script>
<script language=javascript src=../dataentry/fckeditor.js></script>

<?php if (file_exists("../dataentry/js/".$arrHttp["base"].".js"))
	echo "<script language=javascript src=".$arrHttp["base"].".js></script>\n";
?>

<script>
function switchMenu(obj) {
	var el = document.getElementById(obj);
	if ( el.style.display != "none" ) {
		el.style.display = 'none';
	}
	else {
		el.style.display = '';
	}
}

	function toggle(showHideDiv, switchTextDiv) {
		var ele = document.getElementById(showHideDiv);
		var text = document.getElementById(switchTextDiv);
		if(ele.style.display == "block") {
	    	ele.style.display = "none";
	  	}
		else {
			ele.style.display = "block";
		}
	}
	function GuardarBusqueda(){
		Descripcion=document.forma1.Descripcion.value
		if (Trim(Descripcion)==""){
			alert("<?php echo $msgstr["errsave"]?>")
			return
		}
		document.guardar.Descripcion.value=Descripcion
		var winl = (screen.width-300)/2;
		var wint = (screen.height-200)/2;
		document.guardar.Expresion.value='<?php echo $arrHttp["Expresion"]?>'
		msgwin=window.open("","guardar","menu=no,status=yes,width=300, height=200,left="+winl+",top="+wint)
		msgwin.focus()
		document.guardar.submit()
	}
</script>

<script language=javascript>

Tab_val=Array()
xEliminar=""
url_indice=""
ira=""
Ctrl_activo=""
ValorCapturado=""
Val_text=""

	function DateToIso(From,To){
		d=From.split('/')
		<?php echo "dateformat=\"$config_date_format\"\n" ?>
		if (dateformat="DD/MM/YY"){
			iso=d[2]+d[1]+d[0]
		}else{
			iso=d[2]+d[0]+d[1]
		}
		To.value=iso
	}

	function EliminarRegistro(){
		Mfn=document.forma1.Mfn.value
		if (Mfn=="New"){
			alert("<?php echo $msgstr["confirmnew"]?>")
			return
		}
		if (xEliminar==""){
			alert("<?php echo $msgstr["confirmdel"]?>")
			xEliminar="1"
		}else{
			xEliminar=""
			if (top.window.frames.length>1) top.xeditar="";
			document.forma1.Opcion.value='eliminar'
			document.forma1.submit()
		}
	}


	function EnviarDocumento(){
		msgwin=window.open("","Resultado")
		a=editor_getHTML('Body')
		msgwin.focus()
		document.Enviar.documento.value=a
		document.Enviar.submit()
	}

<?php
echo "
function CancelarActualizacion(){
";
if (!isset($arrHttp["encabezado"])){
	if (!isset($arrHttp["ventana"])){
		if (isset($arrHttp["capturar"])){
			echo "self.document.close()
                  self.document.writeln('')
				top.xeditar=\"\"\n";
		}else{
		 echo "
			top.xeditar=\"\"
			top.mfn=top.mfn-1
			top.Menu(\"proximo\")
			top.ApagarEdicion()\n";
		}
	}else{
		echo "self.close()\n";
	}


}else{
	echo 'self.location.href="browse.php"';
}
	echo  "}";
?>
function AlmacenarTerminoEnCampo(){
	alert(Ctrl_activo.name)
}





function DesplegarArchivo(Tag){

	Ctrl=eval("document.forma1."+Tag+".value")
	img="../../../bases/<?php echo $arrHttp["base"]?>/"+Ctrl
	msgimg=window.open(img,"")
}

function ChangeSeq(ix,prefix){
	msgwin=window.open("","CHANGE","width=400, height=200, scrollbars=yes")
	msgwin.document.writeln("<b><?php echo $msgstr["bd"]."</b>: ". $arrHttp["base"]?>")
	msgwin.document.writeln("<form name=cn method=post action=../dataentry/changeseq.php>")
	msgwin.document.writeln("<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>")
	msgwin.document.writeln("<input type=hidden name=prefix value="+prefix+">")
	msgwin.document.writeln("<input type=hidden name=tag value="+ix+">")
	msgwin.document.writeln("new control number <input type=text size=20 name=cn value=''>")
	msgwin.document.writeln("<input type=submit value=send>")
	msgwin.document.writeln("</form>")
	msgwin.focus()
}

function FormarValorCapturado(){
	j=document.forma1.elements.length-1
	ValorCapturado=""
	VC=new Array()
	Val_text=""
	for (i=0;i<=j;i++){
		campo=document.forma1.elements[i]
		nombre=campo.name
		id=campo.id
		if (campo.value!=""){
			if (nombre.substr(0,3)=="tag"){
				if (campo.type=="text" || campo.type=="textarea" ){
					Val_text+=campo.value
				}else{
					switch (campo.type){
						case "password":
							pass=Trim(document.forma1.confirm.value)
							campo.value=Trim(campo.value)
							if (campo.value==""){
								alert("<?php echo $msgstr["falta"].": ".$msgstr["password"]?>")
								return false
							}
							if (pass!=campo.value){
								alert("<?php echo $msgstr["passconfirm"]?>")
								return false
							}
							Val_text+=Trim(campo.value)
							break
						case "radio":
						case "checkbox":
							if (campo.checked){
								if (VC[nombre]=="undefined"){
									VC[nombre]="S";
									ValorCapturado= nombre+"_"+campo.value+"\n";
								}else{
									ValorCapturado=ValorCapturado+"\n"+nombre+"_"+campo.value
								}
								campo.checked=false
							}
							break
						case "select":
						case "select-one":
							break;
						case "select-multiple":
							Ctrl=eval("document.forma1."+nombre)
							for (ixsel=0;ixsel<Ctrl.length;ixsel++)
								if (Ctrl.options[ixsel].selected){
									if (VC[nombre]=="undefined"){
										VC[nombre]="S";
										ValorCapturado=nombre+"_" +Ctrl.options[ixsel].value;
										Ctrl.options[ixsel].selected=false
									}else{
										ValorCapturado=ValorCapturado+"\n"+nombre+"_"+Ctrl.options[ixsel].value;
										Ctrl.options[ixsel].selected=false
								}
							}
							break
					}
				}
			}
			if (campo.id=="O" && campo.value==""){
				if(Msg=="") {
					Separador="<?php echo $msgstr["specvalue"]?>: "
				}else{
					Separador=", "
				}
				Msg=Msg+Separador+NomList[i]
				enviar=false
			}
		}else{
			if (nombre.substr(0,3)=="eti") 	campo.value=""
		}
	}

	document.forma1.check_select.value=ValorCapturado
	return true
}

function EnviarForma(){
	enviar=true
	Msg=""
	Separador=""
	var Repetibles=new Array();
    enviar=FormarValorCapturado()
    if (enviar==false)return
	if (ValorCapturado+Val_text==""){
		alert("<?php echo $msgstr["specvalue"]?>")
		return
	}
	document.forma1.Opcion.value='actualizar'
	<?php if (!isset($arrHttp["ventana"]) and !isset($arrHttp["encabezado"])) {
		echo "if (top.window.frames.length>1){
			         top.ApagarEdicion()
			         document.forma1.db_copies.value=top.db_copies  //DETECT IF THE DATABASE MANAGE COPIES
			         top.xeditar=\"\"
		}
		";
	}
	?>
	document.forma1.action="../dataentry/fmt.php"
	document.forma1.submit()
}

function EnviarValoresPorDefecto(){
	enviar=true
	Msg=""
	Separador=""
	var Repetibles=new Array();
    FormarValorCapturado()
	document.forma1.action="default_update.php"
	document.forma1.submit()
}

function CapturarRegistro(){
	top.basecap="<?php if (isset($arrHttp["basecap"])) echo $arrHttp["basecap"]?>"
	top.ciparcap="<?php if (isset($arrHttp["ciparcap"])) echo $arrHttp["ciparcap"]?>"
	top.base="<?php echo $arrHttp["base"]?>"
	top.cipar="<?php echo $arrHttp["cipar"]?>"
	top.Mfn="<?php echo $arrHttp["Mfn"]?>"

	top.CapturarRegistro()

}

	function LlenarTabla(Tabla,Llenar){
		ix=Tabla.selectedIndex-1
		TT=eval('document.forma1.tag'+Llenar)
		valores=TT.length
		for (i=0;i<valores;i++) TT.remove(0)
		variable=eval("tab"+Llenar+"["+ix+"]")
		for (i=0;i<variable.length;i++){
			TT.options[i+1]=new Option(variable[i],variable[i])
		}
	}

	function Ayuda (tag) {
		tagx=String(tag)

		url="<?php echo "../documentacion/ayuda_db.php?base=".$arrHttp["base"]?>&campo=tag_"+tagx+".html"
		msgwin=window.open(url,"Ayuda","status=yes,resizable=yes,toolbar=no,menu=no,scrollbars=yes,width=600,height=400,top=100,left=100")
		msgwin.focus()
	}


	function EditarArchivo(Tag,Bd,Cipar){
		msgwin=window.open("fckeditor_edit.php?base=traduc&Tag="+Tag+"&archivo="+eval("document.forma1."+Tag+".value"),"Upload","status=yes,resizable=yes,toolbar=no,menu=no,scrollbars=yes,width=750,height=500,top=10,left=5")
		msgwin.focus()
	}

	function AbrirIndiceAlfabetico(xI,Prefijo,Subc,Separa,db,cipar,tag,postings,Repetible,Formato){
		Ctrl_activo=xI
		lang="<?php echo $_SESSION["lang"]?>"
	    document.forma1.Indice.value=xI
	    Separa="&delimitador="+Separa
	    Prefijo=Separa+"&tagfst="+tag+"&prefijo="+Prefijo
	    myleft=screen.width-600
		url_indice="capturaclaves.php?opcion=autoridades&base="+db+"&cipar="+cipar+"&Tag="+tag+Prefijo+"&postings="+postings+"&lang="+lang+"&repetible="+Repetible+"&Formato="+Formato+"&subcampos="+Subc
		msgwin=window.open(url_indice,"Indice","width=600, height=550,  scrollbars, status, resizable location=no, left="+myleft)
		msgwin.focus()
		return
	}

	function AbrirIndice(ira){
		url_indice=url_indice+ira
	    ancho=screen.width-500-20

		msgwin=window.open(url_indice,"Indice","status=yes,resizable=yes,toolbar=no,menu=yes,scrollbars=yes,width=500,height=600,top=20,left="+ancho)
		msgwin.focus()
	}

	function EnviarArchivo(Tag,subc){

		<?php
		$docRoot = getenv("DOCUMENT_ROOT");
		$img_path="$docRoot/bases/".$arrHttp["base"]."/";
		echo "img_path='$img_path'\n";
		if (file_exists($db_path.$arrHttp["base"]."/img_path.def")){
			$def = parse_ini_file($db_path.$arrHttp["base"]."/img_path.def");
			$img_path=trim($def["IMGDIR"]);
			echo "img_path='$img_path'\n";		}
		?>
		msgwin=window.open("","Upload","status=yes,resizable=yes,toolbar=no,menu=no,scrollbars=yes,width=750,height=180,top=100,left=5");
		msgwin.document.close();
		msgwin.document.writeln("<html><title><?php echo $msgstr["uploadfile"]?></title><body link=black vlink=black bgcolor=white>\n");
		msgwin.document.writeln("<form name=upload action=upload_img.php method=POST enctype=multipart/form-data>\n");
		msgwin.document.writeln("<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>\n");
		msgwin.document.writeln("<input type=hidden name=Tag value="+Tag+">")
		msgwin.document.writeln("<input type=hidden name=subc value=\""+subc+"\">")
		msgwin.document.writeln("  <?php echo $msgstr["storein"]?>: <input type=text name=storein size=40 value=\"\" onfocus=blur()>\n");
		msgwin.document.writeln(" <a href=dirs_explorer.php?Opcion=explorar&base=<?php echo $arrHttp["base"]?> target=_blank>explorar</a>")
		msgwin.document.writeln("<br>");
		msgwin.document.writeln("<table width=100%>");
		msgwin.document.writeln("<tr><td class=menusec1><?php echo $msgstr["archivo"]?></td><td class=menusec1><?php echo $msgstr["r_desc"]?></td>\n");
		msgwin.document.writeln("<tr><td><input name=userfile[] type=file size=50></td><td><input name=descripcion size=50></td>\n");
		msgwin.document.writeln("</table>\n");
		msgwin.document.writeln("  <input type=submit value='<?php echo $msgstr["uploadfile"]?>'>\n");
		msgwin.document.writeln("</form>\n");
		msgwin.document.writeln("</body>\n");
		msgwin.document.writeln("</html>\n");
		msgwin.focus()  ;
	}

function Undelete(Mfn){	top.Menu("editar")}

</script>

<BODY>
<?
if (isset($arrHttp["encabezado"])){
// Si se está creando un registro desde el script browse.php
	if ($arrHttp["Opcion"]=="ver"){
		include("../common/institutional_info.php");
		echo "<div class=\"sectionInfo\">
				<div class=\"breadcrumb\">
				</div>";
		echo "<div class=\"actions\">
			<a href=\"$retorno$return\" class=\"defaultButton backButton\">
				<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
				<span><strong>".$msgstr["back"]."</strong></span>
			</a>
		</div>
		<div class=\"spacer\">&#160;</div>
		</div>";
	}else{
		if (!isset($fmt_test)){

			include("../common/institutional_info.php");
			echo "<div class=\"sectionInfo\">
				<div class=\"breadcrumb\">
					";

				if ($arrHttp["Mfn"]=="New") echo "<h3>". $msgstr["newoper"]."</h3>\n";
				echo "</div>
				<div class=\"actions\">
					<a href=javascript:EnviarForma() class=\"defaultButton saveButton\">
						<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
						<span><strong>".$msgstr["m_guardar"]."</strong></span>
					</a>
					<a href=\"$retorno$return\" class=\"defaultButton cancelButton\">
						<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
						<span><strong>".$msgstr["cancelar"]."</strong></span>
					</a>
				</div>
				<div class=\"spacer\">&#160;</div>
			</div>
			";
		}
	}
}

?>
<a name=INICIO></a>

<form name=guardar action=busqueda_guardar.php method=post target=guardar>
	<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
	<input type=hidden name=Expresion value="<?php echo urlencode($arrHttp["Expresion"])?>">
	<input type=hidden name=Descripcion value="">
</form>

<form method=post name=forma1 action=fmt.php onSubmit="javascript:return false">


<input type=hidden name=db_copies>
<input type=hidden name=IsisScript value=ingreso.xis>
<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>
<input type=hidden name=cipar value=<?php echo $arrHttp["cipar"]?>>
<input type=hidden name=wks value=<?php if (isset($arrHttp["wks"]))  echo $arrHttp["wks"]?>>
<?php
if ($arrHttp["Opcion"]=="capturar" || $arrHttp["Opcion"]=="nuevo"){
	$a="crear";
}else{
	$a=$arrHttp["Opcion"];
}


echo "<input type=hidden name=Opcion value=\"$a\">\n";
$pft_a="";
if (isset($arrHttp["encabezado"])) {
	 echo "<input type=hidden name=encabezado value=s>\n";
	 $arrHttp["Formato"]=$arrHttp["base"];
}
if (isset($arrHttp["from"]))
	echo "<input type=hidden name=from value=".$arrHttp["from"].">\n";
?>

<input type=hidden name=ValorCapturado value="">
<input type=hidden name=check_select value="">
<input type=hidden name=Indice value="">
<input type=hidden name=Mfn value="<?php echo $arrHttp["Mfn"]?>">
<input type=hidden name=ver value=S>
<input type=hidden name=ventana value="<?php if (isset($arrHttp["ventana"]))echo $arrHttp["ventana"]?>">
<?php
if (isset($arrHttp["retorno"])){
	echo "<input type=hidden name=retorno value=".$arrHttp["retorno"].">\n";
}
if (isset($arrHttp["return"])){
	echo "<input type=hidden name=return value=".$arrHttp["return"].">\n";
}
if (isset($arrHttp["status"])){
	echo "<input type=hidden name=return value=".$arrHttp["status"].">\n";
}
?>
<input type=hidden name=valor value="">
<input type=hidden name=occur value="">
<input type=hidden name=conte value="">
<input type=hidden name=NoVar value="">
<input type=hidden name=SubC value="">
<input type=hidden name=ep value="">
<input type=hidden name=Occ value="">
<input type=hidden name=NombreC value="">
<input type=hidden name=TagActivo value="">
<input type=hidden name=Repetible value="">
<input type=hidden name=Formato_ex value="">