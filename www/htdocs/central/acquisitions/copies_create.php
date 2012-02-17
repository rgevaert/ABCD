<?php
//
// Crea en la base de datos copias los ítems procedentes de una orden de compra
// INSERT IN COPIES DATABASE THE ITEMS RECEIVED FROM A PURCHASE ORDER
//
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
$lang=$_SESSION["lang"];
include("../lang/admin.php");
include("../lang/acquisitions.php");

include("../common/get_post.php");

include("../common/header.php");
echo "<body>\n";
include("../common/institutional_info.php");
?>
<div class="sectionInfo">
	<div class="breadcrumb">
		<?php echo $msgstr["purchase"].": ".$msgstr["createcopies"]?>
	</div>
	<div class="actions">
	<?php include("order_menu.php")?>
	</div>
	<div class="spacer">&#160;</div>
</div>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/acquisitions/copies_create.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/acquisitions/copies_create.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: copies_create.php</font>\n";
?>
	</div>
<div class="middle form">
			<div class="formContent">
<?php

// se lee la FDT para conseguir la etiqueta del campo donde se coloca la numeración automática
$archivo=$db_path.$arrHttp["database"]."/def/".$_SESSION["lang"]."/".$arrHttp["database"].".fdt";
if (file_exists($archivo)){
	$fp=file($archivo);
}else{
	$archivo=$db_path.$arrHttp["database"]."/def/".$lang_db."/".$arrHttp["database"].".fdt";
	if (file_exists($archivo)){
		$fp=file($archivo);
	}else{
		echo $msgstr["missing"].": ".$archivo;
	    die;
	 }
}
$tag_ctl="";
foreach ($fp as $linea){
	$l=explode('|',$linea);
	if ($l[0]=="AI"){
		$tag_ctl=$l[1];
		$pref_ctl="CN_";
	}
}
//ERROR IF THE CONTROL NUMBER IS NOT DEFINED IN THE FDT
if ($tag_ctl=="" or $pref_ctl==""){
	echo "<h2>".$msgstr["missingctl"]."</h2>";
	die;
}

//GET AND DISPLEY THE OBJECT FROM THE BIBLIOGRAPHIC DATABASE
$Formato=$db_path.$arrHttp["database"]."/pfts/".$_SESSION["lang"]."/".$arrHttp["database"].".pft" ;
if (!file_exists($Formato)) $Formato=$db_path.$arrHttp["database"]."/pfts/".$lang_db."/".$arrHttp["database"].".pft" ;
$Formato="@$Formato,/";
$Expresion="$pref_ctl".$arrHttp["objectid"];
$query = "&base=".$arrHttp["database"]."&cipar=$db_path"."par/".$arrHttp["database"].".par"."&Expresion=$Expresion&Formato=$Formato&Opcion=buscar";
$IsisScript=$xWxis."imprime.xis";
include("../common/wxis_llamar.php");
$cont_database=implode('',$contenido);
if (trim($cont_database)=="") {
	echo "<h4>".$arrHttp["objectid"].": ".$msgstr["objnoex"]."</h4>";
	die;
}
echo $cont_database;
//DISPLAY THE DATA OF THE PURCHASE ORDER AND ITEM TO BE CREATED
echo "<h3>".$msgstr["order"].": ".$arrHttp["order"];
echo "<br>".$msgstr["copies_no"].": ".$arrHttp["copies_req"];
echo "<br>".$msgstr["price"].": ".$arrHttp["price"];
echo "<br>".$msgstr["copiesrec"].": ".$arrHttp["received"];
if (isset($arrHttp["suggestion"])) echo "<br>".$msgstr["suggestions"].": ".$arrHttp["suggestion"];
if (isset($arrHttp["bidding"])) echo "<br>".$msgstr["bidding"].": ".$arrHttp["bidding"];
echo "<br>".$msgstr["database"].": ".$arrHttp["database"];
echo "<br>".$msgstr["date_receival"].": ".$arrHttp["date"];
echo "</h3>";

// Se verifica si esa orden de compra-recomendación-cotización ya ha sido actrualizada
//$res=BuscarCopias($arrHttp["database"],$arrHttp["order"],$arrHttp["suggestion"],$arrHttp["bidding"]);
$res=0;
if ($res==0){   // si no existen las copias, se crean
//READ THE TABLE WITH THE STATUS OF THE COPIES TO ASSIGN THE STATUS 0
	$ix=strpos($arrHttp["provider"],"^");
	if ($ix>0) $arrHttp["provider"]=substr($arrHttp["provider"],0,$ix);
	$status=$db_path."copies/def/".$_SESSION["lang"]."/status_copy.tab";
	if (!file_exists($status)) $status=$db_path."copies/def/".$lang_db."/status_copy.tab";
	$fp=file($status);
	$ix=0;
	$st="^a0";
	foreach ($fp as $stats){		$stats=trim($stats);
		if ($stats!=""){			if ($ix==0) {
				$stats=explode('|',$stats);				$st='^a'.$stats[0].'^b'.$stats[1];
				break;			}		}
    }
	$Mfn="";
	for ($ix=1;$ix<=$arrHttp["received"];$ix++ ){
		echo "<hr>";		$cn=ProximoNumero("copies");
		echo "<p>".$msgstr["createcopies"].": $ix";
		echo "<br>".$msgstr["inventory"].": $cn";
		$ValorCapturado="0001" .$arrHttp["objectid"]; 			//CONTROL NUMBER
		$ValorCapturado.="\n0010" .$arrHttp["database"];		//DATABASE
		$ValorCapturado.="\n0030" .$cn;                			//INVENTORY NUMBER
//		$ValorCapturado.="\n0035"  								//MAIN LIBRARY
//		$ValorCapturado.="\n0040"                           	//BRANCH LIBRARY
		$ValorCapturado.="\n0050" .$arrHttp["tome"];           	//TOME
		$ValorCapturado.="\n0060" .$arrHttp["volume"];			//VOLUME/PART
//		$ValorCapturado.="\n0063"                             	//COPY NUMBER
		$ValorCapturado.="\n0068" .$arrHttp["acqtype"];       	//ACQUISITION TYPE
		if (isset($arrHttp["provider"]))$ValorCapturado.="\n0070" .$arrHttp["provider"];		//PROVIDER
		$ValorCapturado.="\n0080" .$arrHttp["date"];            //DATE OF ARRIVAL
		$ValorCapturado.="\n0085" .$arrHttp["isodate"];         //ISO DATE OF ARRIVAL
		$ValorCapturado.="\n0090" .$arrHttp["price"];           //PRICE
		$ValorCapturado.="\n0100" .$arrHttp["order"];           //ORDEN NUMBER
		if (isset($arrHttp["suggestion"])) $ValorCapturado.="\n0110" .$arrHttp["suggestion"];   //SUGGESTION NUMBER
		if (isset($arrHttp["bidding"]))    $ValorCapturado.="\n0120" .$arrHttp["bidding"];      //BIDDING NUMBER
		$ValorCapturado.="\n0200".$st;                    		//STATUS: PRECATALOGUING
		if (isset($arrHttp["institucion"])) $ValorCapturado.="\n0070" .$arrHttp["institucion"]; //INSTITUTION (EXCHANGE OR DONATION)
		if (isset($arrHttp["condiciones"])) $ValorCapturado.="\n0300" .$arrHttp["condiciones"]; //CONDITIONS (EXCHANGE OR DONATION)
		if (isset($arrHttp["canjeadopor"])) $ValorCapturado.="\n0400" .$arrHttp["canjeadopor"]; //INSTITUTION (EXCHANGE OR DONATION)
		$ValorCapturado=urlencode($ValorCapturado);
		$IsisScript=$xWxis."actualizar.xis";
		$query = "&base=copies&cipar=$db_path"."par/copies.par&login=".$_SESSION["login"]."&Mfn=New&Opcion=crear&ValorCapturado=".$ValorCapturado;
		include("../common/wxis_llamar.php");
		foreach ($contenido as $linea){
			if (substr($linea,0,4)=="MFN:") {
				echo " &nbsp; Mfn: <a href=../dataentry/show.php?base=copies&Mfn=".trim(substr($linea,4))." target=_blank>".trim(substr($linea,4))."</a>";
	    		$Mfn.=trim(substr($linea,4))."|";
			}else{
				if (trim($linea!="")) echo $linea."\n";
			}
		}
	}
	echo "<hr>";
}else{
// ya se cargó esa orden de compra - recomendacion-cotización en la base de datos
	$Expresion='ORDER_'.$arrHttp["order"].'_'.$arrHttp["suggestion"].'_'.$arrHttp["bidding"];
	echo "<h3>".$msgstr["orderloaded"]." ($res) &nbsp; <a href=../dataentry/show.php?base=copies&Expresion=$Expresion target=_blank>".$msgstr["search"]."</a></h3>";}
echo "<p><a href=receive_order_ex.php?searchExpr=".$arrHttp["order"]."&base=purchaseorder&date=".$arrHttp["date"]."&isodate=".$arrHttp["isodate"];
echo ">".$msgstr["purchase"]."</a>";

echo "</div></div>";
include("../common/footer.php");
echo "</body></html>";

//UPDATE PURCHASE ORDER WITH THE COPIES RECEIVED
PurchaseOrderUpdate();


//=================================================================

function ProximoNumero($base){
global $db_path,$msgstr;
	$archivo=$db_path.$base."/data/control_number.cn";
	if (!file_exists($archivo)){
		echo "<h2>".$msgstr["missing"].": ".$base."/data/control_number.cn</h2>";
		return 0;
	}
	$perms=fileperms($archivo);

	if (is_writable($archivo)){
	//se protege el archivo con el número secuencial
		chmod($archivo,0555);
	// se lee el último número asignado y se le agrega 1
		$fp=file($archivo);
		$cn=implode("",$fp);
		$cn=$cn+1;
	// se remueve el archivo .bak y se renombre el archivo .cn a .bak
		if (file_exists($db_path.$base."/data/control_number.bak"))
			unlink($db_path.$base."/data/control_number.bak");
		$res=rename($archivo,$db_path.$base."/data/control_number.bak");
		chmod($db_path.$base."/data/control_number.bak",0666);
		$fp=fopen($archivo,"w");
	    fwrite($fp,$cn);
	    fclose($fp);
	    chmod($archivo,0666);
	    return $cn;
	}
}

function BuscarCopias($database,$order,$suggestion,$bidding){
global $xWxis,$db_path,$Wxis;	$Prefijo='ORDER_'.$order.'_'.$suggestion.'_'.$bidding;
	$IsisScript= $xWxis."ifp.xis";
	$query = "&base=copies&cipar=$db_path"."par/copies.par&Opcion=diccionario&prefijo=$Prefijo&campo=1";
	$contenido=array();
	include("../common/wxis_llamar.php");
	foreach ($contenido as $linea){
		if (trim($linea)!=""){
			$pre=trim(substr($linea,0,strlen($Prefijo)));
			if ($pre==$Prefijo){
				$l=explode('|',$linea);
				return $l[1];
				break;
			}
		}
	}
	return 0;}


function PurchaseOrderUpdate(){
global $arrHttp,$xWxis,$Wxis,$db_path;
	$Db="purchaseorder";
	$Expresion="ON_".$arrHttp["order"] ;
	$formato="(v50/)";
	$IsisScript=$xWxis."buscar_ingreso.xis";
	$query = "&base=$Db&cipar=$db_path"."par/$Db.par&Expresion=".$Expresion."&count=1&from=1&Pft=$formato";
	include("../common/wxis_llamar.php");
	$order=array();
	$ix=0;
    foreach ($contenido as $value){
    	$value=trim($value);
    	if ($value!=""){
    		if (substr($value,0,1)!="[")   $order[]=$value;
    	}
    }
    $ValorCapturado="";
    foreach ($order as $value){    	$ix=$ix+1;
    	if (trim($value)!=""){
	    	if ($ix==$arrHttp["occ"]){
	    		$ix1=strpos($value,"^f");
	    		if ($ix1===false){
	    			$value.="^f".$arrHttp["received"];
	    		}else{    				$p=explode('^',$value);
    				$value="";
    				foreach ($p as $sc){    					$delim=substr($sc,0,1);
    					$subc=substr($sc,1);
    					if ($delim=="f"){    						$subc=(int)$subc + (int)$arrHttp["received"];    					}
    					$value.='^'.$delim.$subc;    				}
	    		}
	    	}
	    	if ($ValorCapturado=="")
	    		$ValorCapturado="0050".$value;
	    	else
	    		$ValorCapturado.="\n"."0050".$value;
	   }
    }
    $arrHttp["Mfn"]=$arrHttp["Mfn_order"];
    $IsisScript=$xWxis."actualizar.xis";
  	$query = "&base=".$Db ."&cipar=$db_path"."par/".$Db.".par&login=".$_SESSION["login"]."&Mfn=" . $arrHttp["Mfn"]."&Opcion=actualizar&ValorCapturado=".$ValorCapturado;
	include("../common/wxis_llamar.php");
}

?>
