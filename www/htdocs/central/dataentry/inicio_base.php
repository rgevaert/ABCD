<?php
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
$page="";
if (isset($_REQUEST['GET']))
	$page = $_REQUEST['GET'];
else
	if (isset($_REQUEST['POST'])) $page = $_REQUEST['POST'];

if (!(eregi("^[a-z_./]*$", $page) && !eregi("\\.\\.", $page))) {
	// Abort the script
	die("Invalid request");

}
//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include ('../config.php');
include("../lang/admin.php");
include("../lang/dbadmin.php");

unset($_SESSION["tagsel"]);
unset($_SESSION["count"]);

include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
if (!isset($arrHttp["base"])) die;

include("../common/header.php");


$_SESSION["base"]=$arrHttp["base"];
$_SESSION["cipar"]=$arrHttp["cipar"];

$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".fdt";
$cont="";
$warning="";

if (!file_exists($archivo)){
	$archivo=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$arrHttp["base"].".fdt";
	if (!file_exists($archivo)){
		echo "<h4><font face=Verdana>".$msgstr["fatal"]."... ".$msgstr["misfile"]." ".$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".fdt";
		$cont="N";
	}
}
$archivo=$db_path.$arrHttp["base"]."/data/".$arrHttp["base"].".fst";
if (!file_exists($archivo)){
	echo  "<h4><font face=Verdana>".$msgstr["fatal"].".. ".$msgstr["misfile"]." $db_path".$arrHttp["base"]."/data/".$arrHttp["base"].".fst";
	$cont="N";
}

$archivo=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/".$arrHttp["base"].".pft";
if (!file_exists($archivo)){
    $archivo=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/".$arrHttp["base"].".pft";
    if (!file_exists($archivo))
		$warning="<br><h6><font face=Verdana>".$msgstr["warning"]."... ".$msgstr["misfile"]." $db_path".$arrHttp["base"]."/def/".$_SESSION['lang']."/".$arrHttp["base"].".pft";
}
if ($cont=="N") die;

$IsisScript=$xWxis."administrar.xis";
$query = "&base=".$arrHttp["base"] . "&cipar=$db_path"."par/".$arrHttp["base"].".par&Opcion=status";
include("../common/wxis_llamar.php");
$ix=-1;
foreach($contenido as $linea) {	$ix=$ix+1;
	if ($ix>0) {		if (trim($linea)!=""){
	   		$a=explode(":",$linea);
	   		$tag[$a[0]]=$a[1];
	  	}
	}
}
echo "<script>top.maxmfn=".$tag["MAXMFN"]."
	top.mfn=0
	top.wks=''
	top.Formato=''\n";

$i=-1;

//Se leen los formatos de salida disponibles
unset($fp);
if (!isset($arrHttp["inicio"])){   //indica que no se van a colocar los formatos en el toolbar
	if (file_exists($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat")){
		$fp = file($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat");
	}else{
		if (file_exists($db_path.$arrHttp["base"]."/pfts/".$lang_db."/formatos.dat")){
			$fp = file($db_path.$arrHttp["base"]."/pfts/".$lang_db."/formatos.dat");
		}
	}
	echo "if (top.ModuloActivo==\"catalog\") top.menu.document.forma1.formato.options.length=0\n";
	$i=-1;
	if (isset($fp)) {
		foreach($fp as $linea){
			if (trim($linea)!="") {				$linea=trim($linea);
				$ll=explode('|',$linea);
				$cod=$ll[0];
				$nom=$ll[1];
				if (isset($_SESSION["permiso"][$arrHttp["base"]."_pft_ALL"]) or isset($_SESSION["permiso"][$arrHttp["base"]."_pft_".$ll[0]])
						or isset($_SESSION["permiso"]["CENTRAL_ALL"])){					$i=$i+1;
					echo "if (top.ModuloActivo==\"catalog\") top.menu.document.forma1.formato.options[$i]=new Option('$nom','$cod')\n";				}
			}
		}

	}
	$i=$i+1;
	if (isset($_SESSION["permiso"][$arrHttp["base"]."_pft_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"])){
		echo "if (top.ModuloActivo==\"catalog\") top.menu.document.forma1.formato.options[$i]=new Option('".$msgstr["noformat"]."','')\n";
		echo "if (top.ModuloActivo==\"catalog\") top.menu.document.forma1.formato.options[$i+1]=new Option('".$msgstr["all"]."','ALL')\n";
	}
	unset($fp);
	echo "if (top.ModuloActivo==\"catalog\") top.menu.document.forma1.wks.options.length=0\n";
	//Se leen las hojas de entrada disponibles
	if (file_exists($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/formatos.wks")){
		$fp = file($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/formatos.wks");
	}else{		if (file_exists($db_path.$arrHttp["base"]."/def/".$lang_db."/formatos.wks"))
			$fp = file($db_path.$arrHttp["base"]."/def/".$lang_db."/formatos.wks");	}
	if (isset($_SESSION["permiso"][$arrHttp["base"]."_fmt_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"]))
		$i=0;
	else
		$i=-1;
	$wks_p=array();
	if (isset($fp)) {
		foreach($fp as $linea){
			if (trim($linea)!="") {				$linea=trim($linea);
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
	//Se lee la tabla de tipos de registro
	unset($fp);
	if (file_exists($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/typeofrecord.tab")){
		$fp = file($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/typeofrecord.tab");
	}else{		if (file_exists($db_path.$arrHttp["base"]."/def/".$lang_db."/typeofrecord.tab"))
			$fp = file($db_path.$arrHttp["base"]."/def/".$lang_db."/typeofrecord.tab");	}
	$i=0;
	$typeofr="";
	if (isset($fp)) {
		foreach($fp as $linea){	           if ($i==0){
	           	$l=explode(" ",$linea);
	           	echo "top.tl='".trim($l[0])."'\n";
	           	if (isset($l[1]))
	           		echo "top.nr='".trim($l[1])."'\n";
	           	else
	           	    echo "top.nr=''\n";
	           	$i=1;	           }else{
				if (trim($linea)!="") {					$l=explode('|',$linea);
					$cod=$l[0];
					$ix=strpos($cod,".");
					$cod=substr($cod,0,$ix);
					if (isset($wks_p[$cod]))
						$typeofr.=trim($linea)."$$$";
	    		}
			}
		}
		echo "top.typeofrecord=\"$typeofr\"\n";
	}else{		echo "top.typeofrecord=\"\"\n";	}
}

//Se lee la fdt para determinar el prefijo y el formato de extracci�n del campo del �ndice de la entrada principal
unset($fp);
$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".fdt";
if (file_exists($archivo)){
	$fp=file($archivo);
}else{
	$archivo=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$arrHttp["base"].".fdt";
	if (file_exists($archivo)) $fp=file($archivo);
}
if (!$fp){
	echo $msgstr["misfile"]. " ".$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".fdt";
	die;
}
$pi="";
$fe="";
foreach($fp as $linea){
	if (trim($linea)!="") {		$fdt=explode('|',$linea);
		if ($fdt[3]==1){			$pi=$fdt[12];
			$fe=$fdt[13];
			if (trim($fe=="")){				$fe="v".trim($fdt[1]);			}
			break;		}
	}
}

echo "
top.prefijo_indice='$pi'
top.formato_indice='".urlencode($fe)."'
top.lock_db=\"\"

</script>";


?>
<body>
<div class="helper">
<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/inicio_base.html target=_blank><?php echo $msgstr["help"]?></a>&nbsp &nbsp;
<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=".$_SESSION["lang"]."/inicio_base.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: inicio_base.php" ?>
</font>
	</div>
<div class="middle form">
			<div class="formContent">
<br><br><br>
<?
echo "<center><b>".$msgstr["bd"].": ".$arrHttp["base"]."</b>";
echo "<br><b><font color=darkred>". $msgstr["maxmfn"].": ".$tag["MAXMFN"]."</b></font>";
if ($tag["BD"]=="N")
	echo "<p>".$msgstr["database"]." ".$msgstr["ne"];
if ($tag["IF"]=="N")
	echo "<p>".$msgstr["if"]." ".$msgstr["ne"];
if ($tag["EXCLUSIVEWRITELOCK"]!=0) {	echo "<p>".$msgstr["database"]." ".$msgstr["exwritelock"]."=".$tag["EXCLUSIVEWRITELOCK"].". ".$msgstr["contactdbadm"]."
	<script>top.lock_db='Y'</script>
	";
}
echo $warning;

?>
</div>
</div>
</center>
<?include("../common/footer.php");?>
</body>
</html>
