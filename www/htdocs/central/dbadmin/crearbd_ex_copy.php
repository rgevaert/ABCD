<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      crearbd_ex_copy.php
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
include ("../config.php");
if (!isset($_SESSION['lang'])) $_SESSION["lang"]="es";
include("../lang/dbadmin.php");
include("../lang/soporte.php");

function CrearBd(){
global $arrHttp,$xWxis,$db_path,$Wxis;
 	$IsisScript=$xWxis."inicializar_bd.xis";
 	$query = "&base=".$arrHttp["nombre"]."&cipar=$db_path"."par/".$arrHttp["nombre"].".par";
 	include("../common/wxis_llamar.php");
	foreach ($contenido as $linea){
	   	echo "$linea\n";
 	}
}


function CopiarDirectorio($srcdir, $dstdir,$basesrc,$basedst, $verbose = true) {
global $msgstr;  $num = 0;
  echo $srcdir;
  if (!file_exists($srcdir)) return $num;
  if($curdir = opendir($srcdir)) {
   while($file = readdir($curdir)) {
     if($file != '.' && $file != '..') {
       $srcfile = $srcdir .  $file;
       $dstfile = $dstdir . str_replace($basesrc,$basedst,$file);

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
        $num += DirCopy($srcfile, $dstfile, $verbose);
       }
     }
   }
   closedir($curdir);
  }
  return $num;
}


function DirCopy($srcdir, $dstdir, $verbose ) {
global $arrHttp,$msgstr;
  $num = 0;
  if(!is_dir($dstdir)) mkdir($dstdir);
  if($curdir = opendir($srcdir)) {
    while($file = readdir($curdir)) {
      if($file != '.' && $file != '..') {
        $srcfile = $srcdir . "/" . $file;
        $dstfile = $dstdir . "/" . str_replace($arrHttp["base_sel"],$arrHttp["nombre"],$file);
        $ext= substr($file,strlen($file)-4,4);
        if(is_file($srcfile)) {
          if(is_file($dstfile)) $ow = filemtime($srcfile) - filemtime($dstfile); else $ow = 1;
          if($ow > 0) {
            if($verbose) echo " ($srcfile) --> ($dstfile) ...<br>";
            if(copy($srcfile, $dstfile)) {
              touch($dstfile, filemtime($srcfile)); $num++;
              if($verbose) echo "OK<br>\n";
            }
            else echo $msgstr["warning"]."! ".$msgstr["nocopyfile"]." ".$srcfile."<br>\n";
          }
        }
        else if(is_dir($srcfile)) {
          $num += dircopy($srcfile, $dstfile, $verbose);
        }
      }
    }
    closedir($curdir);
  }
  return $num;
}


function CrearArchivo($filename,$contenido){
global $msgstr;
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
include("../common/header.php");
echo "
<script>
function Continuar(){
    if (confirm(\"".$msgstr["borrartodo"]."\")==true ) {
    	document.continuar.accion.value=\"cont\"
    	document.continuar.submit()
    }
}

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
	top.encabezado.document.OpcionesMenu.baseSel.options[ix]=new Option(desc,'^a'+bd+'^badm')
	top.encabezado.document.OpcionesMenu.selectedIndex=ix
	top.encabezado.document.OpcionesMenu.baseSel.options[ix].selected=true
	top.encabezado.CambiarBase()
}
</script>
<body>";
if (isset($arrHttp["encabezado"]))
	include("../common/institutional_info.php");
echo "
	<div class=\"sectionInfo\">

			<div class=\"breadcrumb\"><h5>".
				$msgstr["copydb"].": " . $arrHttp["nombre"]. ". ".$msgstr["createfrom"].": ".$arrHttp["base_sel"]."</h5>
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
	<a href=../documentacion/ayuda.php?help=".$_SESSION["lang"]."/crearbd_ex_copy.html target=_blank>".$msgstr["help"]."</a>&nbsp &nbsp;";
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
	echo "<a href=../documentacion/edit.php?archivo=". $_SESSION["lang"]."/crearbd_ex_copy.html target=_blank>".$msgstr["edhlp"]."</a>";
echo "<font color=white>&nbsp; &nbsp; Script: crearbd_ex_copy.php</font></div>";

echo "
<div class=\"middle form\">
			<div class=\"formContent\">
<form name=continuar method=post action=crearbd_ex.php>
<input type=hidden name=nombre value=\"".$arrHttp["nombre"]."\">
<input type=hidden name=desc value=\"".$arrHttp["desc"]."\">
<input type=hidden name=base_sel value=\"".$arrHttp["base_sel"]."\">
<input type=hidden name=accion value=''>
</form>
";

//foreach ($arrHttp as $var => $value) echo "$var = $value<br>";
$Dir = $db_path;
$bd = $arrHttp['nombre'];
$descripcion=$arrHttp['desc'];
$base=$arrHttp['base_sel'];
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
                	echo "<center><br><br><font color=red face=verdana><h4>".$bd." ".$msgstr["bdexiste"]."</h4>";
                	echo "<p><center><a href=javascript:history.back()>".$msgstr["back"]."</a>";
                	echo "&nbsp;&nbsp;<a href=javascript:Continuar()>".$msgstr["continuar"]."</a>";
					die;
				}
            }
   }
}
closedir($handle);
//$bd=strtolower($bd);
//$Dir=strtolower($Dir);
if (!file_exists($Dir."$bd")) if (mkdir($Dir."$bd")==false and $arrHttp["accion"]!="cont") {
    echo $msgstr["warning"]."! ".$msgstr["nocreatedb"];
	die;
}
if ($arrHttp["base_sel"]!="~~NewDb"){
	if (!file_exists($Dir."$bd"."/data")) mkdir($Dir."$bd"."/data");
	if (!file_exists($Dir."$bd"."/pfts")) mkdir($Dir."$bd"."/pfts");
	if (!file_exists($Dir."$bd"."/cnv")) mkdir($Dir."$bd"."/cnv");
	if (!file_exists($Dir."$bd"."/def")) mkdir($Dir."$bd"."/def");
	chmod($Dir."$bd"."/data",0777);
	chmod($Dir."$bd"."/pfts",0777);
	chmod($Dir."$bd"."/cnv",0777);
	chmod($Dir."$bd"."/def",0777);

//se crea el fullinv.sh en la carpeta data
 	$OS=strtoupper($_ENV["OS"]);
 	if (strpos($OS,"WIN")==false){
		$contenido="mx $bd fst=@$bd.fst uctab=isisuc.tab actab=isisac.tab fullinv=$bd -all now tell=100";
		$filename=$Dir."$bd"."/data/fullinv.sh";
 	} else{
 		$contenido="mx $bd fst=@$bd.fst uctab=isisuc.tab actab=isisac.tab fullinv=$bd -all now tell=100";
		$filename=$Dir."$bd"."/data/fullinv.bat";
 	}
	$ce=CrearArchivo($filename,$contenido);

//Bases.dat
	$filename= $db_path."bases.dat";
	$fp=file($db_path."bases.dat");
	$contenido="";
	foreach ($fp as $value){
		$ixpos=strpos($value,"|");
		$comp=trim(substr($value,0,$ixpos));
		if ($comp!=$bd) $contenido.=$value;
	}
	$contenido=trim($contenido);
	$contenido.="\n".$bd."|".$descripcion;
//echo $contenido;
	$ce=CrearArchivo($filename,$contenido);

//Archivo .par
	$filename=$db_path."par/$bd.par";
	$contenido="$bd.*=$db_path"."$bd/data/$bd.*\n";
	$contenido.="prologoact.pft=$db_path"."www/prologoact.pft\nepilogoact.pft=$db_path"."www/epilogoact.pft\n";
	$contenido.="isisac.tab=$db_path"."$bd/data/isisac.tab\nisisuc.tab=$db_path"."$bd/data/isisuc.tab\n";
	$contenido.="STW=$db_path"."$bd/data/$bd.stw\n";
	$ce=CrearArchivo($filename,$contenido);

	$file = $Dir."$base"."/";
	$newfile = $Dir."$bd"."/";
}
echo "<p>".$msgstr["copiar"]." ".$msgstr["file"]." ... </p>";
//CopiarDirectorio($file,$newfile,$base,$bd);

$file = $Dir."$base"."/pfts/";
$newfile = $Dir."$bd"."/pfts/";
CopiarDirectorio($file,$newfile,$base,$bd);

$file = $Dir."$base"."/def/";
$newfile = $Dir."$bd"."/def/";
CopiarDirectorio($file,$newfile,"","");


$file = $Dir."$base"."/cnv/";
$newfile = $Dir."$bd"."/cnv/";
CopiarDirectorio($file,$newfile,"","");

//se crea el fullinv.sh en la carpeta data
 if (stristr($OS,"win")==false){
	$contenido="mx $bd fst=@$bd.fst uctab=isisuc.tab actab=isisac.tab fullinv=$bd -all now tell=100";
	$filename=$Dir."$bd"."/data/fullinv.sh";
 } else{
 	$contenido="mx $bd fst=@$bd.fst uctab=isisuc.tab actab=isisac.tab fullinv=$bd -all now tell=100";
	$filename=$Dir."$bd"."/data/fullinv.bat";
 }
$ce=CrearArchivo($filename,$contenido);

// se copia la fst
$file = $Dir."$base"."/data/$base.fst";
$newfile=$Dir."$bd"."/data/$bd.fst";
if(copy($file, $newfile)) {
    touch($newfile, filemtime($file));
}else{
	echo $msgstr["warning"]."! ".$msgstr["nocopyfile"]." ".$file."\n";
}

// copy UC and AC conversion tables and stopword file
if (file_exists($Dir."$base"."/data/isisac.tab")) {
	$file = $Dir."$base"."/data/isisac.tab";
	$newfile=$Dir."$bd"."/data/isisac.tab";
		if(copy($file, $newfile)) {
    		touch($newfile, filemtime($file));
		}else{
		echo $msgstr["warning"]."! ".$msgstr["nocopyfile"]." ".$file."\n";
		}
}
if (file_exists($Dir."$base"."/data/isisuc.tab")) {
	$file = $Dir."$base"."/data/isisuc.tab";
	$newfile=$Dir."$bd"."/data/isisuc.tab";
		if(copy($file, $newfile)) {
    		touch($newfile, filemtime($file));
		}else{
		echo $msgstr["warning"]."! ".$msgstr["nocopyfile"]." ".$file."\n";
		}
}
if (file_exists($Dir."$base"."/data/$base.stw")) {
	$file=$Dir."$base"."/data/$base.stw";
	$newfile=$Dir."$bd"."/data/$bd.stw";
		if(copy($file, $newfile)) {
    		touch($newfile, filemtime($file));
		}else{
		echo $msgstr["warning"]."! ".$msgstr["nocopyfile"]." ".$file."\n";
		}
}


//Archivo.par
$fp=file($db_path."par/$base.par");
$contenido="";
foreach ($fp as $value){
	$contenido.=str_replace($base,"$bd",$value);
}
$newfile=$db_path."par/$bd.par";
$ce=CrearArchivo($newfile,$contenido);

//formatos.dat
$contenido="";
if (file_exists($db_path."$bd/pfts/".$_SESSION["lang"]."/formatos.dat")){
	$fp=file($db_path."$bd/pfts/".$_SESSION["lang"]."/formatos.dat");
	foreach ($fp as $value){
		$contenido.=str_replace($base,"$bd",$value);
	}
}
$newfile=$db_path."$bd/pfts/".$_SESSION["lang"]."/formatos.dat";
$ce=CrearArchivo($newfile,$contenido);
CrearBd();
if (!isset($arrHttp["encabezado"]))
	echo "

<script>
	ActualizarListaBases('$bd','$descripcion')
	self.location=\"../dataentry/inicio_base.php?base=".$arrHttp["nombre"]."&cipar=".$arrHttp["nombre"].".par\"
</script>";

include("../common/footer.php");
?>