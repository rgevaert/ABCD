<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      usuario_prestamos_presentar.php
 * @desc:      Analyzes the user and item for establishing the loan policy
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

//date_default_timezone_set('UTC');
$debug="";
if (!isset($_SESSION["login"])) die;
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
include("../config_loans.php");
$lang=$_SESSION["lang"];
include("../lang/admin.php");
include("../lang/prestamo.php");

include("fecha_de_devolucion.php");
$ec_output="" ;

function ImprimirRecibo($Recibo){?>
<script>
	msgwin=window.open("","recibo","width=400, height=300, scrollbars, resizable")
	msgwin.document.write("<?php echo $Recibo?>")
	msgwin.focus()
	msgwin.print()
	msgwin.close()
</script>
<?php}

//SE AVERIGUA SI EL TITULO TIENE RESERVAS, SI HAY EJEMPLARES DISPONIBLES Y SI EL USUARIO
//QUE RETIRA HA RESERVADO

function ProcesarReserva($usuario,$copias,$control_number){global $db_path,$Wxis,$xWxis,$arrHttp,$reservas_pendientes,$lang_db;
//SE DETERMINA SI SE HAN IMPLEMENTADO LAS RESERVAS//DETERMINAR SI EL TITULO TIENE RESERVAS BUSCANDO EN RESERVE EL NÚMERO DE CONTROL
	$control_number=3;
	$formato_obj=$db_path."reserve/pfts/".$_SESSION["lang"]."/opac_reserve.pft";
	if (!file_exists($formato_obj)) $formato_obj=$db_path."reserve/pfts/".$lang_db."/opac_reserve.pft";
	$query = "&Expresion=CN_".$control_number."&base=reserve&cipar=$db_path"."par/reserve.par&Formato=$formato_obj";
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$total_reservas=0;
	$mismo_usuario="";
	$reservas_pendientes=array();
	foreach ($contenido as $linea){
		//SE DETERMINA SI LA RESERVA ESTA VENCIDA
		$r=explode('|',$linea);
		$fecha=$r[1];
		$vence=$r[2];
	 	$difference=compareDate ($r[2]);
	 	$datediff = floor($difference / 86400);
	 	if ($datediff>=0){
	 		$total_reservas=$total_reservas+1;
			$reservas_pendientes[]=$linea;
		}
		//SE DETERMINA SI EL USUARIO QUE SOLICITA LO TIENE RESERVADO
		//EN CASO AFIRMATIVO SE CANCELA LA RESERVA
		if ($usuario==trim($r[4])) {
			$query = "&base=reserve&cipar=$db_path"."par/reserve.par&login=abcd&Mfn=" . $r[3]."&Opcion=eliminar";
			$IsisScript=$xWxis."eliminarregistro.xis";
			//include("../common/wxis_llamar.php");
			//SI LA RESERVA EXPIRÓ SE SUSPENDE AL USUARIO
			if ($datediff<0)
				echo "expiro";
			return 0;
		}
	}
	//SI NO HAY RESERVAS SE PROCEDE AL PRESTAMO
	if ($total_reservas==0)
		return 0;
	if ($total_reservas>count($copias))
		return $total_reservas;	//SE DETERMINA SI QUEDAN EJEMPLARES DISPONIBLES NO RESERVADOS
	//PARA ELLO SE DETERMINA EL TOTAL DE EJEMPLARES PRESTADOS PARA RESTARLE LOS RESERVADOS
	$formato_obj=$db_path."trans/pfts/".$_SESSION["lang"]."/loans_display.pft";
	if (!file_exists($formato_obj)) $formato_obj=$db_path."trans/pfts/".$lang_db."/loans_display.pft";
	$query = "&Expresion=ON_P_".$control_number."&base=trans&cipar=$db_path"."par/trans.par&Formato=".$formato_obj;
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$total_prestados=0;
	foreach ($contenido as $linea){
		$total_prestados=$total_prestados+1;
	}
	if (count($copias)>$total_prestados-$total_reservas)
		return 0;
	else
		return $total_reservas;}

// Se localiza el número de control en la base de datos bibliográfica
function ReadCatalographicRecord($control_number,$db){
global $db_path,$Wxis,$xWxis,$arrHttp,$pft_totalitems,$pft_in,$pft_nc,$pft_typeofr,$titulo,$prefix_in,$multa,$pft_storobj,$lang_db;
global $prefix_in;
	//Read the FDT of the database for extracting the prefix used for indexing the control number
//	echo $control_number;
	if (isset($arrHttp["db"]))
		$Expresion=trim($prefix_in).trim($control_number);
	else
	    $Expresion="CN_".$control_number;
	// Se extraen las variables necesarias para extraer la información del título al cual pertenece el ejemplar
	// se toman de databases_configure_read.php
	// pft_totalitems= pft para extraer el número total de ejemplares del título
	// pft_in= pft para extraer el número de inventario
	// pft_nc= pft para extraer el número de clasificación
	// pft_typeofr= pft para extraer el tipo de registro

	$formato_ex="'||'".$pft_nc."'||'".$pft_typeofr."'###'";
	//se ubica el título en la base de datos de objetos de préstamo
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";
	$Expresion=urlencode($Expresion);
	$formato_obj=$db_path."$db/loans/".$_SESSION["lang"]."/loans_display.pft";
	if (!file_exists($formato_obj)) $formato_obj=$db_path."$db/loans/".$lang_db."/loans_display.pft";
	$formato_obj.="\n /".urlencode($formato_ex).urlencode($pft_storobj);
	$query = "&Opcion=disponibilidad&base=$db&cipar=$db_path"."par/$db.par&Expresion=".$Expresion."&Pft=@$formato_obj";
	include("../common/wxis_llamar.php");
	$total=0;
	$titulo="";
	foreach ($contenido as $linea){
		$linea=trim($linea);
		if (!empty($linea)) {
			if (substr($linea,0,8)=='$$TOTAL:')
				$total=trim(substr($linea,8));
			else
				$titulo.=$linea."\n";
		}
	}
	return $total;
}

// Se localiza el número de inventario en la base de datos de objetos  de préstamo
function LocalizarInventario(){
global $db_path,$Wxis,$xWxis,$arrHttp,$pft_totalitems,$pft_in,$pft_nc,$pft_typeofr,$copies_title,$prefix_in,$multa;

    $Expresion=$prefix_in.$arrHttp["inventory"];
	// Se extraen las variables necesarias para extraer la información del título al cual pertenece el ejemplar
	// se toman de databases_configure_read.php
	// pft_totalitems= pft para extraer el número total de ejemplares del título
	// pft_in= pft para extraer el número de inventario
	// pft_nc= pft para extraer el número de clasificación
	// pft_typeofr= pft para extraer el tipo de registro



	//READ LOANOBJECT DATABASE TO GET THE RECORD WITH THE ITEMS OF THE TITLE
	$IsisScript=$xWxis."loans/prestamo_disponibilidad.xis";

	$Expresion=urlencode($Expresion);
	if (isset($arrHttp["db"])){
	//IF NO LOANOBJECTS READ THE PFT FOR EXTRACTING THEN INVENTORY NUMBER AND THE TYPE OF RECORD		$d=explode('|',$arrHttp["db"]);
		$arrHttp["base"]=$d[0];
		$arrHttp["db"]=$d[0];
		$pft_typeofrec=LeerPft("loans_typeofobject.pft");
		$pft_ni=LeerPft("loans_inventorynumber.pft",$d[0]);
		$formato_ex="($pft_ni'||".$d[0]."||',$pft_ni,'||||||',".$pft_typeofrec."'||||||'/)";	}else{		$arrHttp["base"]="loanobjects";
		$formato_ex="(v1[1]'||'v10[1],'||',v959^i,'||',v959^l,'||',v959^b,'||',v959^o,'||',v959^v,'||',v959^t,'||'/)";
    // control number||database||inventory||main||branch||type||volume||tome	}
	$formato_obj=urlencode($formato_ex);

	$query = "&Opcion=disponibilidad&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par&Expresion=".$Expresion."&Pft=$formato_obj";
	include("../common/wxis_llamar.php");
	$total=0;
	$copies_title=array();
	$item="";

	foreach ($contenido as $linea){
		$linea=trim($linea);
		if (substr($linea,0,8)=='$$TOTAL:'){
			$total=trim(substr($linea,8));
		}else{
			$t=explode('||',$linea);
			if ($arrHttp["inventory"]==$t[2]) $item=$linea;
			$copies_title[]=$linea;
		}
	}
	return $item;
}

//se busca la el número de inventario en el archivo de transacciones
function LocalizarTransacciones(){
global $db_path,$Wxis,$xWxis,$arrHttp,$msgstr,$tr_prestamos;
	$formato_obj=$db_path."trans/pfts/".$_SESSION["lang"]."/loans_display.pft";
	if (!file_exists($formato_obj)) $formato_obj=$db_path."trans/pfts/".$lang_db."/loans_display.pft";
	$query = "&Expresion=TR_P_".$arrHttp["inventory"]."&base=trans&cipar=$db_path"."par/trans.par&Formato=".$formato_obj;
	$IsisScript=$xWxis."cipres_usuario.xis";
	include("../common/wxis_llamar.php");
	$prestamos=array();
	foreach ($contenido as $linea){
		$tr_prestamos[]=$linea;
	}
}



///////////////////////////////////////////////////////////////////////////////////////////

include("leer_pft.php");

// se lee la configuración local
include("calendario_read.php");
include("locales_read.php");
// se leen las politicas de préstamo
include("loanobjects_read.php");
// se lee la configuración de la base de datos de usuarios
include("borrowers_configure_read.php");


$valortag = Array();
include("../common/get_post.php");
//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";

// ARE THE COPIES IN THE COPIES DATABASE OR IN THE BIBLIOGRAPHIC DATABASE?
$prefix_in="IN_";
if (isset($arrHttp["db"])){
	$from_copies="N";
	$x=explode('|',$arrHttp["db"]);
	$prefix_in=trim($x[2]);
}else{
	$from_copies="Y";
}
if (isset($arrHttp["Opcion"])){
	if ( $arrHttp["Opcion"]=="reservar")
		$msg_1=$msgstr["reserve"];
	else
		if ($arrHttp["Opcion"]=="prestar") $msg_1=$msgstr["loan"];
}

// ------------------------------------------------------
//--------------------------------------------------------------

$link_u="";
if (isset($arrHttp["usuario"])) $link_u="&usuario=".$arrHttp["usuario"];
if (isset($arrHttp["inventory"])) $presentar_reservas="N";
$nmulta=0;
$cont="";
include("ec_include.php");  //se incluye el procedimiento para leer el usuario y los préstamos pendientes
if ($sanctions_output!="") {	$cont="N";
	unset($arrHttp["inventory"]);}
if (count($prestamos)>0) $ec_output.= "<strong><a href=javascript:DevolverRenovar('D')>".$msgstr["return"]."</a> | <a href=javascript:DevolverRenovar('R')>".$msgstr["renew"]."</a></strong>";
$ec_output.= "</form>";
$ec_output.= "<form name=devolver action=devolver_ex.php>
<input type=hidden name=searchExpr>
<input type=hidden name=usuario value=".$arrHttp["usuario"].">
<input type=hidden name=vienede value=ecta>
</form>
<form name=multas action=multas_eliminar_ex.php method=post>
<input type=hidden name=usuario value=".$arrHttp["usuario"].">
<input type=hidden name=Mfn value=\"\">
</form>

";
//Se obtiene el código, tipo y vigencia del usuario
$formato=$pft_uskey.'\'$$\''.$pft_ustype.'\'$$\''.$pft_usvig;
$formato=urlencode($formato);
$query = "&Expresion=".trim($uskey).$arrHttp["usuario"]."&base=users&cipar=$db_path"."par/users.par&Pft=$formato";
$contenido="";
$IsisScript=$xWxis."cipres_usuario.xis";
include("../common/wxis_llamar.php");
$user="";
$nmulta=0;
$msgsusp="";
$vig="";
foreach ($contenido as $linea){
	$linea=trim($linea);
	if ($linea!="")  $user.=$linea;
}
if (empty($user)) {
	ProduceOutput("<h4>".$msgstr["userne"]."</h4>","");
	die;
}else{
	$userdata=explode('$$',$user);

	if ($nsusp>0) $userdata[2]="2";

	//Se analiza la vigencia del usuario

	switch($userdata[2]){
		case "1":
			$msgsusp=$msgstr["userinval"];
			$vig="N";
			break;
		case "2":
		    $msgsusp=$msgstr["usersusp"];
		    $vig="N";
			break;
	}
}
$ec_output.= "\n
<script>
  Vigencia='$vig'

</script>\n";

// Si viene desde la opción de prestar, se localiza el número de inventario solicitado
if (isset($arrHttp["inventory"]) and $vig==""){

	//Se determina si el usuario tiene préstamos vencidos. La variable $nv se calcula en ec_include.php

	if (!empty($userdata[2])) {
		echo $msgstr[$userdata[2]];
		die;	}

	//Se ubica el ejemplar en la base de datos de objeto
	$item=LocalizarInventario();
	if ($item==""){		$ec_output.= "<h3><font color=red>".$arrHttp["inventory"]." ".$msgstr["copynoexists"]."</font></h3>";
		$cont="N";
 		ProduceOutput($ec_output,"");
		die;	}
	//se extrae la información del número de control del título y la base de datos catalográfica a la cual pertenece
	$tt=explode('||',trim($item));
	$control_number=$tt[0];
	$catalog_db=$tt[1];
    $tipo_obj=trim($tt[5]);      //Tipo de objeto

// se lee la configuración de la base de datos de objetos de préstamos
$arrHttp["db"]="$catalog_db";
include("databases_configure_read.php");


	$ppres=$politica[$tipo_obj][trim($userdata[1])];   //read the policy
	if (trim($ppres)==""){		$ppres=$politica[strtoupper($tipo_obj)][trim(strtoupper($userdata[1]))];	}
	if (trim($ppres)==""){		$ppres=$politica["0"]["0"];
		if ($ppres!="")
			$ec_output.= "<font color=red><h3>".$msgstr["nopolicy"].". ".$msgstr["policyusing"]." 0-0</h3></font>";
	}
	if (trim($ppres)==""){		$debug="Y";
		$ec_output.= "<font color=red><h3>".$msgstr["nopolicy"]."</h3></font>";	}

	$obj=explode('|',$ppres);
	$fechal_usuario=$obj[15];
	$fechal_objeto=$obj[16];
	$fecha_d=date("Ymd");
	if (trim($fechal_usuario)!=""){
		if ($fecha_d>$fechal_usuario){			echo $msgstr["tit_limusuario_a"];
			$norenovar="S";
			die;		}
	}
	if (trim($fechal_objeto)!=""){
		if ($fecha_d>$fechal_objeto){
			echo $msgstr["tit_limobjeto_a"];
			die;
		}
	}
	$tprestamos_p=$obj[2];   // número de préstamos que puede recibir de acuerdo al tipo de objeto
	if ($debug=="Y")$ec_output.= "<p>".$msgstr["requested_policy"].": ".$userdata[1]."</a> - ".$tipo_obj.": ".$msgstr["loans_perm"].": $tprestamos_p;  ".$msgstr["items_loan"].": $np; ".$msgstr["objects_arrears"].": $nv<p>";
    $cont="S";
	if ($nv>0 and isset($arrHttp["inventory"]) and $obj[12]!="Y"){
		$ec_output.= "<font color=red><h3>".$msgstr["useroverdued"]."</h3></font>";
		$cont="N";
	}else{
//	echo "$np /$tprestamos_p/";
		if (trim($tprestamos_p)!=""){
			if ($np>=$tprestamos_p){
				$ec_output.= " <font color=red><h3>".$msgstr["nomoreloans"]."</h3></font>";
				$cont="N";

			}
		}
	}


	if ($cont=="S"){
		// Se localiza el registro catalográfico utilizando los datos anteriores
		$ref_cat=ReadCatalographicRecord($control_number,$catalog_db);
	 	if ($ref_cat==""){      //The catalographic record is not found
	 		$ec_output.= "<h3><font color=red>".$arrHttp["inventory"]." ".$msgstr["catalognotfound"]."</font></h3>";
			$cont="N";
	 		ProduceOutput($ec_output,"");
			die;
	 	}
	    $tt=explode('###',trim($titulo));
	    $obj_store=$tt[1];
		$tt=explode('||',$tt[0]);
		$titulo=$tt[0];
		$signatura=$tt[1];     //signatura topográfica
	    $ec_output.= "<blockquote>$titulo</blockquote>";

//		$ec_output.= "</ul>";
	// se verifica si el ejemplar está prestado
		LocalizarTransacciones();
		$Opcion="";
		if (count($tr_prestamos)>0){   // Si ya existe una transacción de préstamo para ese número de inventario, el ejemplar está prestado
			$msg= $msgstr["itemloaned"];
			$Opcion="reservar";
		//	$action="usuario_prestamos_reservar.php";
			$msg_1=$msgstr["reserve"];
			$ec_output.= "<h3><font color=red>".$msgstr["inventory"].": ".$arrHttp["inventory"]. " $msg</font></h3>";
			$cont="N";
			ProduceOutput($ec_output,"") ;
			die;
		}else{
			// VERYFY IF THE USER HAS ANOTHER ITEM OF THE SAME OBJECT
			foreach($prestamos as $value){
				if (trim($value)!=""){					$nc_us=explode('^',$value);
	                $pi=$nc_us[0];                                   //GET INVENTORY NUMBER OF THE LOANED OBJECT
	                $pv=$nc_us[14];                                  //GET THE VOLUME OF THE LOANED OBJECT
	                $pt=$nc_us[15];                                  //GET THE TOME OF THE LOANED OBJECT
					$comp=$pi." ".$pv." ".$pt;
					foreach ($copies_title as $cop){						$c=explode('||',$cop);
						$comp_01=$c[2]." ".$c[6]." ".$c[7];
						if ($comp_01==$comp and $obj[14]!="Y"){							$ec_output.= "<h3><font color=red>".$msgstr["inventory"].": ".$arrHttp["inventory"].": ".$msgstr["duploan"]."</font></h3>";
							$cont="N";
							ProduceOutput($ec_output,"") ;
							die;
						}					}				}
        	}
			$Opcion="prestar";
			//	$action="usuario_prestamos_prestar.php";
			$msg_1=$msgstr["loan"];
			if ($debug=="Y" or isset($arrHttp["show"])){
				ProduceOutput($ec_output,"");
			}else{
				//LA POLITICA CONTEMPLA EL MANEJO DE RESERVAS?
				$pp=explode('|',$ppres);
				$reserva=0;
				$pp[11]="Y";
				if ($pp[11]=="Y")
					if (file_exists("$db_path/reserve/data/reserve.mst"))						//SE DETERMINA SI HAY RESERVAS Y SI HAY EJEMPLARES DISPONIBLES
						$reserva=ProcesarReserva($arrHttp["usuario"],$copies_title,$control_number);
				if ($reserva==0){
					header("Location:usuario_prestamos_prestar.php?usuario=".$arrHttp["usuario"]."&inventario=".$arrHttp["inventory"]."&signatura=".$signatura."&item=".urlencode($item)."&usrtype=".$userdata[1]."&copies=".$from_copies."&policy=".urlencode($ppres));
				}else{
					$arrHttp["vienede"]="prestamos";
					include("opac_reservas.php");
					ProduceOutput($ec_output,$reserva_output);				}
            }

		}
	}else{		ProduceOutput($ec_output,"");	}
}else{	ProduceOutput($ec_output,"");}

function ProduceOutput($ec_output,$reservas){global $msgstr,$arrHttp,$signatura,$msg_1,$cont,$institution_name,$db_path,$lang_db,$copies_title;	include("../common/header.php");    echo "<body>";
 	include("../common/institutional_info.php");
?>
<script  src="../dataentry/js/lr_trim.js"></script>
<script>
document.onkeypress =
  function (evt) {
    var c = document.layers ? evt.which
            : document.all ? event.keyCode
            : evt.keyCode;
	if (c==13)
		self.location.href='prestar.php?encabezado=s<?php echo $link_u?>'
    return true;
  };

function DevolverRenovar(Proceso) {	if (Proceso=="D"){
		document.devolver.action="devolver_ex.php"
	}else{
		if (Vigencia=="N"){
			alert("<?php echo $msgstr["norenew"]?>");
			return
		}
		document.devolver.action="renovar_ex.php"
	}
	marca="N"
	switch (np){     // número de préstamos del usuario
		case 1:
			if (document.ecta.chkPr.checked){
				document.devolver.searchExpr.value=document.ecta.chkPr.id
				atraso=document.ecta.chkPr.value
				politica=document.ecta.politica.value
				marca="S"
			}
			break
		default:
			for (i=0;i<document.ecta.chkPr.length;i++){
				if (document.ecta.chkPr[i].checked){
					marca="S"
					document.devolver.searchExpr.value=document.ecta.chkPr[i].id
					atraso=document.ecta.chkPr[i].value
					politica=document.ecta.politica[i].value
					break
				}
			}
	}
	fecha_d="<?php echo date("Ymd")?>"
	if (marca=="S"){
		p=politica.split('|')
		if (Proceso=="R"){    // si es una renovación
			if (p[6]==0){     // the object does not accept renovations\
				alert("<?php echo $msgstr["noitrenew"] ?>")
				return
			}
			if (atraso!=0){
				if (p[13]!="Y"){
					alert("<?php echo $msgstr["loanoverdued"]?>")
					return
				}
			}
			if (Trim(p[15])!=""){
				if (fecha_d>p[15]){
					alert("<?php echo $msgstr["limituserdate"]?>"+": "+p[15])
					return
				}
			}
			if (Trim(p[16])!=""){
				if (fecha_d>p[16]){
					alert("<?php echo $msgstr["limitobjectdate"]?>"+": "+p[16])
					return
				}
			}

			if (nMultas!=0){
				alert("<?php echo $msgstr["norenew"]?>")
				return
			}
		}
		document.devolver.submit()
	}else{
		alert("<?php echo $msgstr["markloan"]?>")
	}
}

function PagarMultas(){
	Mfn=""
	switch (nMultas){
		case 1:
			if (document.ecta.pay.checked){
            	Mfn=document.ecta.pay.value
			}
			break
		default:
			for (i=0;i<nMultas;i++){
				if (document.ecta.pay[i].checked){
					Mfn+=document.ecta.pay[i].value+"|"
				}
			}
			break
	}
	if (Mfn==""){
		alert("<?php echo $msgstr["selfine"]?>")
		return
	}
	document.multas.Mfn.value=Mfn
	document.multas.submit()
}

function DeleteSuspentions(){
	Mfn=""
	switch (nSusp){
		case 1:
			if (document.ecta.susp.checked){
            	Mfn=document.ecta.susp.value
			}
			break
		default:
			for (i=0;i<nSusp;i++){
				if (document.ecta.susp[i].checked){
					Mfn+=document.ecta.susp[i].value+"|"
				}
			}
			break
	}
	if (Mfn==""){
		alert("<?php echo $msgstr["selsusp"]?>")
		return
	}
	document.multas.Mfn.value=Mfn
	document.multas.submit()
}
</script>
<body>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["statment"]?>
	</div>
	<div class="actions">
		<?php include("submenu_prestamo.php");?>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<?php
echo "<a href=../documentacion/ayuda.php?help=". $_SESSION["lang"]."/circulation/loan.html target=_blank>". $msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/circulation/loan.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: usuarios_prestamos_presentar.php </font>
	</div>";
// prestar, reservar o renovar
?>
<div class="middle form">
	<div class="formContent">
<form name=ecta>
<?php
echo $ec_output;
if ($reservas !=""){	echo "<P><font color=red><strong>".$msgstr["total_copies"].": ".count($copies_title).". ".$msgstr["item_reserved"]."</strong></font><br>";
	echo $reservas ;}

?>
</div></div>
<?php include("../common/footer.php");?>
</body>
</html>

<?php

if (isset($arrHttp["recibo"])) {
	ImprimirRecibo($arrHttp["recibo"]);
}
	if (isset($arrHttp["error"])){
		echo "<script>
				alert('".$arrHttp["error"]."')
				</script>
		";
	}
}  //END FUNCTION PRODUCEOUTPUT

?>