<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      crearbd_new_create.php
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
global $arrHttp;
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
if (!isset($_SESSION['lang'])) $_SESSION["lang"]="es";
include ("../config.php");

include("../lang/dbadmin.php");

function MostrarPft(){
global $arrHttp,$xWxis,$db_path,$Wxis;
 	$IsisScript=$xWxis."inicializar_bd.xis";
 	$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["cipar"];
 	include("../common/wxis_llamar.php");
	foreach ($contenido as $linea){
	   	echo "$linea\n";
 	}
}

function CopiarDirectorio($srcdir, $dstdir,$basesrc,$basedst, $verbose = true) {
global $msgstr;  $num = 0;
//  if(!is_dir($dstdir)) mkdir($dstdir);
  if($curdir = opendir($srcdir)) {
   while($file = readdir($curdir)) {
     if($file != '.' && $file != '..') {
       $srcfile = $srcdir . '/' . $file;
       $dstfile = $dstdir . '/' . str_replace($basesrc,$basedst,$file);

       if(is_file($srcfile)) {
         if(is_file($dstfile)) $ow = filemtime($srcfile) - filemtime($dstfile); else $ow = 1;
         if($ow > 0) {
           if($verbose) echo " ($srcfile) --> ($dstfile) ...<br>";
           if(copy($srcfile, $dstfile)) {
             touch($dstfile, filemtime($srcfile)); $num++;
             if($verbose) echo "OK<br>\n";
           }
           else echo $msgstr["warning"]."! ".$msgstr["nocopyfile"]." ".$srcfile."\n";
         }
       }
       else if(is_dir($srcfile)) {
 //        $num += dircopy($srcfile, $dstfile, $verbose);
       }
     }
   }
   closedir($curdir);
  }
  return $num;
}


function CrearArchivo($filename,$contenido){
global $msgstr;
echo "<xmp>$contenido</xmp>";
	if (!$handle = fopen($filename, 'w')) {
         echo $msgstr["copenfile"]." ($filename)";
         return -1;
		 exit;
   	}

   // Write $somecontent to our opened file.
	if (fwrite($handle, $contenido) === FALSE) {
       echo $msgstr["cwritefile"]." ($filename)";
	   return -1;
       exit;
   }

   echo "<p><b>".$msgstr["file"]." ".$msgstr["created"].": </b>".$filename;         //<xmp>$contenido</xmp>

   fclose($handle);
//   chmod($filename,0777);
   return 0;

}

//----------------------------------------------------------
$requestedURL= $_SERVER['PHP_SELF'];
$ix=strrpos($requestedURL,"/");
$requestedURL=substr($requestedURL,0,$ix+1);

include("../common/get_post.php");

//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
if (isset($arrHttp["pft"])) $_SESSION["PFT"]=$arrHttp["pft"];
include("../common/header.php");

echo "
<script>
function Continuar(){
    if (confirm(\"".$msgstr["borrartodo"]."\")==true ) {
    	document.continuar.accion.value=\"cont\"
    	document.continuar.submit()
    }
}
";
if (!isset($arrHttp["encabezado"])){
	echo "

function ActualizarListaBases(bd,desc){
	ix=top.encabezado.document.OpcionesMenu.baseSel.options.length
	for (i=0;i<ix;i++){
		xbase=top.encabezado.document.OpcionesMenu.baseSel.options[i].value
		ixsel=xbase.indexOf('^b')
		if (ixsel==-1)
			basecomp=xbase.substr(2)
		else
			basecomp=xbase.substr(2,ixsel-2)
		if (basecomp==bd){
			top.encabezado.document.OpcionesMenu.baseSel.options[i].text=desc
			return
		}
	}
	top.encabezado.document.OpcionesMenu.baseSel.options[ix]=new Option(desc,'^a'+bd+'^badm',1)
}";
}
echo "
</script>
<body>
";
if (isset($arrHttp["encabezado"]))
	include("../common/institutional_info.php");
echo "
	<div class=\"sectionInfo\">

			<div class=\"breadcrumb\"><h5>".
				$msgstr["fdt"]." " .$msgstr["database"]. ": " . $arrHttp["base"]."</h5>
			</div>

			<div class=\"actions\">
	";
$arrHttp["Opcion"]="new";
if ($arrHttp["Opcion"]=="new"){
	if (isset($arrHttp["encabezado"]) ){
		echo "<a href=\"../common/inicio.php?reinicio=s\" class=\"defaultButton backButton\">";
	}else{
		 echo "<a href=menu_creardb.php class=\"defaultButton backButton\">";
	}
}else{
	if (isset($arrHttp["encabezado"]))
		$encabezado="&encabezado=s";
	else
		$encabezado="";
	echo "<a href=menu_modificardb.php?base=". $arrHttp["base"].$encabezado." class=\"defaultButton backButton\">";
}
echo "
					<img src=\"../images/defaultButton_iconBorder.gif\" alt=\"\" title=\"\" />
					<span><strong>". $msgstr["back"]."</strong></span>
				</a>
			</div>
			<div class=\"spacer\">&#160;</div>
	</div>";

echo "
	<div class=\"helper\">
	<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/crearbd_new_create.html target=_blank>".$msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/crearbd_new_create.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: crearbd_new_create.php</font></div>";
echo "
<div class=\"middle form\">
			<div class=\"formContent\">
<form name=continuar method=post action=crearbd_new_create.php>
<input type=hidden name=nombre value=\"".$arrHttp["base"]."\">
<input type=hidden name=pft value='".urlencode($_SESSION["PFT"])."'>
<input type=hidden name=desc value=\"".$_SESSION["DESC"]."\">
<input type=hidden name=base value=\"".$arrHttp["base"]."\">
<input type=hidden name=accion value=''> ";
if (isset($arrHttp["Opcion"])) echo "<input type=hidden name=Opcion value=\"".$arrHttp["base"]."\">\n";
if (isset($arrHttp["encabezado"])) echo "<input type=hidden name=encabezado value=s>\n";
echo "</form>";


$Dir = $db_path;
$bd = $arrHttp['base'];
$descripcion=$_SESSION["DESC"];
$base=$arrHttp['base'];
$_SESSION["NEWBD"]=$bd;
$_SESSION["NEWBASE"]=$descripcion;

$the_array = Array();
$handle = opendir($Dir);

if (!isset($arrHttp["accion"])) $arrHttp["accion"]="";
if ($arrHttp["accion"]!="cont"){
	while (false !== ($file = readdir($handle))) {
   		if ($file != "." && $file != "..") {
   			if(!is_file($Dir."/".$file))
            	if ($file==$bd) {
                	echo "<center><br><br><h4>".$bd." ".$msgstr["bdexiste"]."</h4>";
              //  	echo "<p><center><a href=javascript:history.back()>".$msgstr["back"]."</a>";
                	echo "&nbsp;&nbsp;<a href=javascript:Continuar()>".$msgstr["continuar"]."</a>";
					die;
				}
            }
   }
}
closedir($handle);
//$bd=strtolower($bd);
//$Dir=strtolower($Dir);
if (!file_exists($Dir."$bd")){
	if (mkdir($Dir."$bd")==false and $arrHttp["accion"]!="cont") {
    	echo $msgstr["warning"]."! ".$msgstr["nocreatedb"];
		die;
	}
}
if (!file_exists($Dir."$bd"."/data")) mkdir($Dir."$bd"."/data");

if (!file_exists($Dir."$bd"."/pfts")) mkdir($Dir."$bd"."/pfts");
if (!file_exists($Dir."$bd"."/def")) mkdir($Dir."$bd"."/def");
if (!file_exists($Dir."$bd"."/ayudas")) mkdir($Dir."$bd"."/ayudas");
$fp=file($db_path."lang.tab");
foreach ($fp as $l){
	$l=trim($l);
	if (!file_exists($Dir."$bd"."/pfts/$l")) mkdir($Dir."$bd"."/pfts/$l");
	if (!file_exists($Dir."$bd"."/def/$l")) mkdir($Dir."$bd"."/def/$l");
}
if (!file_exists($Dir."$bd"."/cnv")) mkdir($Dir."$bd"."/cnv");
chmod($Dir."$bd"."/data",0777);
chmod($Dir."$bd"."/pfts",0777);
chmod($Dir."$bd"."/cnv",0777);
chmod($Dir."$bd"."/def",0777);

//Bases.dat
$filename= $db_path."bases.dat";
$fp=file($db_path."bases.dat");
$contenido="";
foreach ($fp as $value){
	 $contenido.=$value;
}
$contenido=trim($contenido);
$contenido.="\n".$bd."|".$descripcion;
$ce=CrearArchivo($filename,$contenido);

//Archivo .par
$filename=$db_path."par/$bd.par";
$contenido="$bd.*=%path_database%"."$bd/data/$bd.*\n";
$contenido.="$bd.pft=%path_database%"."$bd/pfts/".$_SESSION["lang"]."/$bd.pft\n";
$contenido.="prologoact.pft=%path_database%"."www/prologoact.pft\nepilogoact.pft=%path_database%"."www/epilogoact.pft\n";
$contenido.="isisac.tab=%path_database%"."$bd/data/isisac.tab\nisisuc.tab=%path_database%"."$bd/data/isisuc.tab\n";
$contenido.="STW=%path_database%"."$bd/data/$bd.stw\n";
$ce=CrearArchivo($filename,$contenido);

$file = $Dir."$base"."/";
$newfile = $Dir."$bd"."/";

echo "<P><b>";
$arrHttp["IsisScript"]="";
$arrHttp["Opcion"]="inicializar";
$arrHttp["cipar"]=$bd.".par";
$arrHttp["base"]=$bd;
MostrarPft();
echo $msgstr["database"]." ".$arrHttp["base"]." ".$msgstr["created"];
$fp=fopen($db_path.$arrHttp["base"]."/data/control_number.cn","w");
fwrite($fp,"0");
fclose($fp);
$t=explode("\n",$_SESSION["FDT"]);
$fp=fopen($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".fdt","w");
foreach ($t as $value){
	$val=trim(str_replace('|','',$value));
	if ($val=="00") $val="";
	if ($val!="") fwrite($fp,stripslashes($value)."\n");
	//echo "$value<br>";
}
fclose($fp);
echo "<p>".$msgstr["fdt"]." ".$msgstr["saved"];


$t=explode("\n",$_SESSION["FST"]);
$fp=fopen($db_path.$arrHttp["base"]."/data/".$arrHttp["base"].".fst","w");
foreach ($t as $value){
	$val=trim(str_replace('|','',$value));
	if ($val=="00") $val="";
	if ($val!="") fwrite($fp,stripslashes($value)."\n");
	//echo "$value<br>";
}
fclose($fp);
echo "<p>".$msgstr["fst"]." ".$msgstr["saved"];

$value=$_SESSION["PFT"];


$fp=fopen($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/".$arrHttp["base"].".pft","w");
fwrite($fp,stripslashes($value)."\n");
fclose($fp);
echo "<p>".$msgstr["pft"]." ".$msgstr["saved"];

$fp=fopen($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/formatos.dat","w");
fwrite($fp,$arrHttp["base"]."|".$_SESSION["DESC"]);
fclose($fp);

if (!isset($arrHttp["encabezado"])){
	$descripcion.="\n";
	echo  "<script>
	ActualizarListaBases('$bd','$descripcion')
	</script>
	";
}
echo "<p>";
if (!isset($arrHttp["encabezado"]))echo "<a href=../dataentry/inicio_base.php?base=".$arrHttp["base"]."&cipar=".$arrHttp["base"].".par&nueva=s>".$msgstr["continuar"]."</a><p>";
echo "<h4>".$msgstr["assusdb"]."</h4>";
echo "

	<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/assign_operators.html target=_blank>".$msgstr["assop"]."</a>&nbsp &nbsp;
    <a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/assign_operators.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<p><a href=iah_edit_db.php?encabezado=s&base=".$arrHttp["base"].">".$msgstr["iah-conf"]."</a>";

echo "</div></div>";
include("../common/footer.php");
echo "</body></html>";
die;
?>
