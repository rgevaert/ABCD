<?php
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include ("../config.php");


include("../lang/dbadmin.php");
include("../lang/soporte.php");
$lang=$_SESSION["lang"];
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";

function CrearArchivo($filename,$contenido){
global $db_path,$msgstr;
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

	$ix=strlen($db_path);
   echo "<p><b>".$msgstr["filemod"].": </b> ".substr($filename,$ix)."<br>";

   fclose($handle);
   return 0;

}

function  delete_directory($dirname)  {
global $db_path,$msgstr;
	$ix=strlen($db_path);
	$dir_handle=false;
    if  (is_dir($dirname)) {
    	echo "<p>".$msgstr["dirname"].": ".substr($dirname,$ix)."<br>";
        $dir_handle  =  opendir($dirname);
    }

    if  (!$dir_handle)
        return  false;
    while($file  =  readdir($dir_handle))  {

        if  ($file  !=  "."  &&  $file  !=  "..")  {

        	echo $msgstr["file"].": ".$file."<br>";
            if  (!is_dir($dirname."/".$file))
                unlink($dirname ."/".$file);
            else
            	delete_directory("$dirname".'/'.$file);
        }
    }
    closedir($dir_handle);
    rmdir($dirname);
    return  true;
}

//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
$encabezado="";
if (isset($arrHttp["encabezado"])) $encabezado="&encabezado=s";
include("../common/header.php");
?>
<script>
function EliminarListaBases(bd){
	ix=top.encabezado.document.OpcionesMenu.baseSel.options.length
	for (i=0;i<ix;i++){
		xbase=top.encabezado.document.OpcionesMenu.baseSel.options[i].value
		ixsel=xbase.indexOf('^b')
		if (ixsel==-1)
			basecomp=xbase.substr(2)
		else
			basecomp=xbase.substr(2,ixsel-2)
		if (basecomp==bd){
			top.encabezado.document.OpcionesMenu.baseSel.options[i]=null
			top.base=""
			return
		}
	}
}
</script>
<?php
echo "<body>\n";
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
?>
<div class="sectionInfo">

			<div class="breadcrumb">
				<?php echo "<h5>".$msgstr["mnt_ebd"]." " .$msgstr["database"].": ".$arrHttp["base"]."</h5>"?>
			</div>

			<div class="actions">
<?php
	if (!isset($arrHttp["eliminar"]))
    	echo "<a href=\"menu_mantenimiento.php?base=".$arrHttp["base"]."$encabezado\" class=\"defaultButton backButton\">";
	else
		echo "<a href=\"../common/inicio.php?reinicio=s\" class=\"defaultButton backButton\">";

?>
					<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
					<span><strong><?php echo $msgstr["back"]?></strong></span>
				</a>
			</div>
			<div class="spacer">&#160;</div>
</div>
<?php }
echo "
<div class=\"middle form\">
			<div class=\"formContent\">

";
echo "<font size=1>Script: eliminarbd.php</font>";
if  (!is_dir($db_path.$arrHttp["base"])) {
    	echo "<p><h5>".$arrHttp["base"]."<br>".$msgstr["dbnex"]."</h5>";
    	if (!isset($arrHttp["encabezado"])){
    		if (!isset($arrHttp["eliminar"]))
    			echo " <p><center><a href=menu_mantenimiento.php?base=".$arrHttp["base"]."$encabezado>". $msgstr["back"]."</a>";
    		else
    			echo " <p><center><a href=.php?base=".$arrHttp["base"]."$encabezado>". $msgstr["back"]."</a>";

    	}
        echo "
</div>
</div>
";
include("../common/footer.php");
echo "</body></html>\n";
        die;
    }
echo "<center><h4><font color=red>".$msgstr["borrartodo"]."!</font></h4></center>";

//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";


if (isset($arrHttp["eliminar"])){
	$BD=$arrHttp["base"];
	$el=$db_path.$arrHttp["base"];
	$res=delete_directory($el);
	if ($res==true){
		if (file_exists($db_path."par/".$BD.".par")) unlink($db_path."par/".$BD.".par");
		$filename=$db_path. "bases.dat";
		$fp=file($filename);
		$contenido="";
		foreach ($fp as $value){
			$value=trim($value);
			if ($value!=""){
				$b=array();
				$b=explode('|',$value);
				$comp=$b[0];
				if ($comp!=$BD) $contenido.=$value."\n";
			}
		}
		$ce=CrearArchivo($filename,$contenido);
		if (!isset($arrHttp["encabezado"])) echo "<script>EliminarListaBases('".$arrHttp["base"]. "')</script>";

	}
}else{
	if (isset($arrHttp["encabezado"])) {
		$url="../common/inicio.php?reiniciar=s";
	}else{
		$url="index.php";
	}
	echo "<script>
			if (confirm(\"".$msgstr["mnt_ebd"]." ".$arrHttp["base"]." ??\")==true){
				if (confirm(\"".$msgstr["seguro"]."\")==true){";
					 echo "self.location=\"eliminarbd.php?base=".$arrHttp["base"]."&eliminar=s$encabezado\"
				}else{
					self.location=\"$url\";
				}
			}else{
				self.location=\"$url\";
			}
		  </script>";
}
if (!isset($arrHttp["encabezado"]))
 	echo "<p><center><a href=menu_mantenimiento.php?base=".$arrHttp["base"].">Menu</a>";
echo "
</div>
</div>
";
include("../common/footer.php");
echo "</body></html>\n";
?>
