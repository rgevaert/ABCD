<?php

function LeerRegistro($base,$cipar,$from,&$maxmfn,$Opcion,$login,$password,$Formato) {global $OS,$valortag,$tl,$nr,$xWxis,$arrHttp,$session_id,$msgstr,$db_path,$Wxis,$wxisUrl,$deleted_record;
	$query="";
	if (isset($arrHttp["lock"])){    	$IsisScript=$xWxis."lock.xis";
    	$query = "&base=" . $base . "&cipar=$db_path"."par/".$cipar. "&Mfn=" . $arrHttp["Mfn"]."&login=".$login;
    	include("../common/wxis_llamar.php");
    	$res=implode("|",$contenido);
    	$res=explode("|",$res);
    	$res=trim($res[0]);
    	if ($res!="LOCKGRANTED") {    		return $res;    	}
    }
    $query="";
    if (isset($arrHttp["unlock"])){
    	$IsisScript=$xWxis."unlock.xis".
    	$query = "&base=" . $base . "&cipar=$db_path"."par/".$cipar. "&Mfn=" . $arrHttp["Mfn"]."&login=".$login;
    	include("../common/wxis_llamar.php");
    	$res=implode("",$contenido);
    	$res=trim($res);
    	return $res;
    }

	$IsisScript=$xWxis."leer.xis";
	$query = "&base=" . $base . "&cipar=$db_path"."par/".$cipar. "&Mfn=" . $arrHttp["Mfn"]."&Opcion=".$Opcion."&login=".$login."&password=".$password;

	if ($Formato!="") $query.="&Formato=".$arrHttp["Formato"];
	include("../common/wxis_llamar.php");

 	$valortag=array();
  	$ic=-1;
 	foreach($contenido as $linea){
	 	if ($ic==-1){
			$linea=trim(substr($linea,0,strlen($linea)-4));
			$arr=explode('##',$linea);
	   		$mfn=substr(trim($arr[1]),4);
	   		$ic=2;
			$arrHttp["Mfn"]=$mfn;
			$arrHttp["Maxmfn"]=substr(trim($arr[0]),7);
			$maxmfn=$arrHttp["Maxmfn"];
	  	}else{
	   		$linea=trim($linea);
	   		if ($linea!=""){
	    		$pos=strpos($linea, " ");
	    		if (is_integer($pos)) {
	     			$tag_x=trim(substr($linea,0,$pos));
	////El formato ALL envía un <br> al final de cada línea y hay que quitarselo
					if (is_numeric($tag_x) and $tag_x!=""){
			    		$linea=rtrim(substr($linea, $pos+2,strlen($linea)-($pos+2)-5));
			    		$tag=$tag_x;
						if ($tag==1002){
				 			$maxmfn=$linea;
						}else{
			     			if (!isset($valortag[$tag])){
			      				$valortag[$tag]=$linea;
			     			}else {
			     	 			$valortag[$tag]=$valortag[$tag]."\n".$linea;
			     			}
			    		}
					}else{
						$valortag[$tag].="\n".$linea;
					}
	   			}else{

	   			}
	  		}
	 	}
	}
 	if (isset($valortag[1102])){	 	if ($valortag[1102]==1) {
		 	echo "<h1>".$msgstr["recdel"]."</h1>";
		 	$record_deleted="Y";
	 		return;
	 	}
    }
    $record_deleted="N";
 	if (isset($valortag["1002"])) $maxmfn=$valortag["1002"];

}

function LeerRegistroFormateado($Formato) {

global $valortag,$xWxis,$arrHttp,$tagisis,$msgstr,$db_path,$Wxis,$wxisUrl,$lang_db,$MaxMfn,$record_deleted;
 	if ($Formato=="" or $arrHttp["Formato"]=="ALL") { 		$Formato="ALL";
 		$arrHttp["Formato"]="ALL";
 	}else{
 		if (file_exists($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/" .$Formato.".pft")){ 			$Formato=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/" .$Formato;
 		}else{ 			$Formato=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/" .$Formato;
        }
 	}

 	$IsisScript=$xWxis."buscar.xis";
 	$query = "&cipar=$db_path"."par/".$arrHttp["cipar"]. "&Mfn=" . $arrHttp["Mfn"]."&Opcion=".$arrHttp["Opcion"]."&base=" .$arrHttp["base"]."&Formato=$Formato";
	include("../common/wxis_llamar.php");
	$salida="";
	$record_deleted="N";
 	if ($arrHttp["Formato"]=="ALL") {
 		$salida="<xmp>";
		foreach ($contenido as $linea) {
			$linea=str_ireplace('<BR>',"\n",$linea);
			$linea=str_ireplace('<BR \>',"\n",$linea);
		 	if ($linea=='$$DELETED'){
		 		$record_deleted="Y";

		 		$salida.= $arrHttp["Mfn"]." ".$msgstr["recdel"];
		 	}else{
			 	$salida.=$linea;
		 	}
		}
		$salida.= "</xmp>";
	 }else{        $cont=$contenido;
	  	foreach ($contenido as $linea) {
	  		$lines=trim($linea);	  		if (substr($linea,0,6)=='$$REF:'){	 			$ref=substr($linea,6);
	 			$f=explode(",",$ref);
	 			$bd_ref=$f[0];
	 			$pft_ref=$f[1];
	 			$expr_ref=$f[2];
	 			$IsisScript=$xWxis."buscar.xis";
 				$query = "&cipar=$db_path"."par/".$arrHttp["cipar"]. "&Expresion=".$expr_ref."&Opcion=buscar&base=" .$bd_ref."&Formato=$pft_ref";
				include("../common/wxis_llamar.php");
				foreach($contenido as $linea) $salida.= "$linea\n";
	  		}else{
	  			if (strpos($linea,'$$DELETED')===false){
			 		$salida.= $linea."\n";
		 		}else{
		  			$salida.= "<h1> ".$arrHttp["Mfn"]." ".$msgstr["recdel"]."</h1>";
		  			$record_deleted="Y";
				}
		 	}
	  	}
	 }
	 return $salida;
}
?>