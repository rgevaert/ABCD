<?php

function LeerFdt($base){
global $lang_db;
	include ("../config.php");
// se lee el archivo mm.fdt
	$archivo="$db_path$base/def/".$_SESSION["lang"]."/$base.fdt";
	if (file_exists($archivo))
		$fpTm = file($archivo);
	else
		$fpTm=file("$db_path$base/def/".$lang_db."/$base.fdt");

	foreach ($fpTm as $linea){
		if (trim($linea)!="") {
			$t=explode('|',$linea);
  			if ($t[0]!="S" and $t[0]!="H" and $t[0]!="L"){
  	  			$tag=$t[1];
  	  			if ($tag!="" and $tag!="*"){
  	  				if (!isset($Fdt[$tag])){
		    			$Fdt[$tag]=$linea;
					}
				}
			}
		}
	}
   return $Fdt;
}

?>