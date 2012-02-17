<?php
include("verificar_eliminacion.php");

function CodificaSubCampos($campo,$numsubc,$subc,$delimsc){
$valores=explode("\n",$campo);
$salida="";
 	foreach ($valores as $lin){
		$lin=trim($lin);
		if($lin!=""){
 			for ($isc=0;$isc<strlen($subc);$isc++){
   				$delim=substr($delimsc,$isc,1);
   				if ($isc==0){
					if (substr($subc,$isc,1)!=" " and substr($subc,$isc,1)!="-") {
				    	$lin='^'.substr($subc,$isc,1).$lin;
					}
   				}else{
       				$pos=strpos($lin, $delim);
   					if (is_integer($pos)) {
    					$lin=substr($lin,0,$pos).'^'.substr($subc,$isc,1).trim(substr($lin,$pos+1));
   					}
   				}

  			}
	  		$salida=$salida."\n".$lin;
		}
 	}
 	return $salida;
}

function ActualizarRegistro(){
$tabla = Array();

global $vars,$cipar,$from,$base,$ValorCapturado,$arrHttp,$ver,$valortag,$fdt,$tagisis,$cn,$msgstr,$tm,$lang_db,$MD5;
global $xtl,$xnr,$Mfn,$FdtHtml,$xWxis,$variables,$db_path,$Wxis,$default_values,$rec_validation,$wxisUrl,$validar,$tm;
	$ValorCapturado="";
	$VC="";
	if ($arrHttp["Opcion"]=="eliminar"){		$archivo=$db_path.$arrHttp["base"]."/pfts/recdel_val";
		$verify="";
		if (file_exists($archivo.".pft")){			$verify="Y";
		}else{            $archivo=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/recdel_val";
            if (file_exists($archivo.".pft")){
				$verify="Y";
			}else{				$archivo=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/recdel_val";
            	if (file_exists($archivo.".pft")){
					$verify="Y";
				}			}		}		if ($verify=="Y") {			$salida=VerificarEliminacion($archivo);
			if ($salida!=""){
				echo "<div class=\"middle form\">
						<div class=\"formContent\">
					";

				echo "<p><font color=red><strong>".$salida."</strong></font>";
				$url= "fmt.php?base=".$arrHttp["base"]."&cipar=".$arrHttp["base"].".par&Opcion=ver&Mfn=".$arrHttp["Mfn"]."&error=".urlencode($salida)."&Formato=".$arrHttp["Formato"];
				if (isset($arrHttp["wks_a"])) $url.="&wks=".$arrHttp["wks_a"];
				$url.="&ver=S";
				if (isset($arrHttp["db_copies"]))  $url.="&db_copies=".$arrHttp["db_copies"];
				echo "<p><a href=$url><h3>".$msgstr["editar"]."</h3></a>";
				echo "</div></div>";
				die;
			}
		}
	}
	if (count($variables)==0 and !isset($arrHttp["check_select"]) and $arrHttp["Opcion"]!="eliminar") {
	    echo $msgstr["specvalue"];
		return;
	}
	PlantillaDeIngreso($xtl,$xnr);
	foreach ($vars as $fdt) {
		$t=explode('|',$fdt);
  		$tag=$t[1];
  		if (isset($variables["tag".$tag])){
			$rep=$t[4];
			$tipoc=$t[0];
			$dataentry=$t[7];

			if ($dataentry=="A") {
				$variables["tag".$tag]=str_replace("\n","", $variables["tag".$tag]);
				$variables["tag".$tag]=str_replace("\r","", $variables["tag".$tag]);
				$variables["tag".$tag]=substr($variables["tag".$tag],0,16360);
			}
			$subc=rtrim($t[5]);
			$numsubc=strlen($subc);
 			$delimsc=$t[6];
 			$ispassword=$t[7];
 			if ($ispassword=="P" and isset($MD5) and $MD5==1 ){ 				$variables["tag".$tag]= md5($variables["tag".$tag]);
 			}
			if ($subc!="" && $tipoc!="T"){
				$lin=trim($variables["tag".$tag]);
                if (!isset($default_values) and !isset($rec_validation) and !isset($end_code)){
					if ($lin!="") {
	                    if (trim($t[6])!=""){
					   		$variables["tag".$tag]=CodificaSubCampos($lin,$numsubc,$subc,$delimsc);
						}
					}
				}
			}
// si $rep=T el campo se edita en forma de tabla por lo que hay que convertirlo en un campo
// repetible con subcampos
			if ($rep=="T") {
				$dummy=explode("\n",$variables["tag".$tag]);
				$salida="";
				foreach ($dummy as $linea) {
 					$linea=trim($linea);
					$xlin="";
					for ($i=0; $i<strlen($subc);$i++){
						$resc='^'.substr($subc,$i,1);
						$ix1=strpos($linea,$resc);
						if ($ix1===false) {
						}else{
							$ix2=strpos($linea,'^',$ix1+1);
							if ($ix2===false) {
						    	$ix2=strlen($linea);
							}
							$ix1=$ix1+2;
							$valorsc=substr($linea,$ix1,$ix2-$ix1);
							if (!empty($valorsc)) {
							    $xlin.=$resc.$valorsc;
							}
							$linea=substr($linea,$ix2);
						}
					}
					if ($xlin!="") $salida.=$xlin."\n";

				}
				$variables["tag".$tag]= $salida;
			}
		}
	}
    if (isset($arrHttp["check_select"])){
    	$dummy=array();
    	$dummy=explode("\n",$arrHttp["check_select"]);    	foreach ($dummy as $value){    		$ixD=strpos($value,"_");
    		if ($ixD>0){
	    		$parte1=substr($value,0,$ixD);
	    		$parte2=substr($value,$ixD+1);
	    		$key=trim(substr($parte1,3));
				if (strlen($key)==1) $key="000".$key;
				if (strlen($key)==2) $key="00".$key;
				if (strlen($key)==3) $key="0".$key;
				$parte2=stripslashes($parte2);
			//	$parte2=str_replace("'","&acute;",$parte2);
				unset($p2);
				$p2=explode("_",$parte2);
				if (isset($p2[2])){
				}else{
			    	$ValorCapturado.=$key.$parte2."\n";
				}
			}
    	}

	}
	if ($arrHttp["Opcion"]!="eliminar" and isset($variables)){		foreach ($variables as $key => $lin){
			$key=trim(substr($key,3));
			$k=$key;
			$ixPos=strpos($key,"_");
			if (!$ixPos===false) {
		    	$key=substr($key,0,$ixPos-1);
			}
			if (!empty($key)) {
				if (strlen($key)==1) $key="000".$key;
				if (strlen($key)==2) $key="00".$key;
				if (strlen($key)==3) $key="0".$key;
				$lin=stripslashes($lin);
	//			$lin=str_replace("'","&acute;",$lin);
				$campo=array();

				if ($dataentry!="xA")
					$campo=explode("\n",$lin);
				else
					$campo[]=str_replace("\n","",$lin);
				foreach($campo as $lin){
					if (!empty($lin))  $VC.=$k." ".$lin."\n";
			   		$ValorCapturado.=$key.$lin."\n";
				}
			}
		}
	}
	$x=isset($default_values);
	$fatal_cn="";
	$fatal="";
    if ($arrHttp["Opcion"]!="eliminar"){
	   	if (isset($default_values) or isset($rec_validation) or isset($end_code)){
	   		return;
	   	}else{

	   		// si en la FDT existe un campo del tipo autoincrement, entonces se determina el número de identificación
	   		// si el valor del campo ya viene fijado entonces no se genera un nuevo valor
	   		if(isset($arrHttp["autoincrement"]) and $arrHttp["Mfn"]=="New"){	 			if (isset($arrHttp["tag".$arrHttp["autoincrement"]]) and $arrHttp["tag".$arrHttp["autoincrement"]]=="" or !isset($arrHttp["tag".$arrHttp["autoincrement"]])){	 				$nc="";	 			 	include("autoincrement.php");
	 			 	if ($cn=="" or $cn==false){                        $fatal_cn=$msgstr["notgenerate"];
	 			 	}else{
		   				$key=$arrHttp["autoincrement"];
		   				if (strlen($key)==1) $key="000".$key;
						if (strlen($key)==2) $key="00".$key;
						if (strlen($key)==3) $key="0".$key;
						$ValorCapturado.=$key.$cn."\n";
						$VC.=$arrHttp["autoincrement"]." ".$cn."\n";
					}
				}else{					$cn=$arrHttp["tag".$arrHttp["autoincrement"]];				}	   		}
//echo "<xmp>$ValorCapturado</xmp>";
//die;

			$ValorCapturado=urlencode($ValorCapturado);
			$VC=urlencode($VC);
	        unset($validar);
			$pftval="";
			if (isset($arrHttp["wks"])){				if (isset($tm)){					foreach ($tm as $value){						$t=explode('|',$value);
						if ($t[0]==$arrHttp["wks"]){							$tl=strtolower($t[1]);
							$nr=strtolower($t[2]);
							$pftval=$tl;
							if (isset($nr) and $nr!="")
								$pftval.="_".$nr;
							$pftval.="_".$arrHttp["base"].".val";
							break;						}					}				}			}
			$file_val="";
			if ($pftval!=""){
				$file_val=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$pftval;				if (!file_exists($file_val))  $file_val=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$pftval;	        }
			if ($file_val=="" or !file_exists($file_val)){				$file_val=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".val";
				if (!file_exists($file_val))  $file_val=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$arrHttp["base"].".val";			}
		//CALL THE VALIDATION FORMAT
			$output="";
			if (file_exists($file_val)){

				include("recval_check.php");

			}
		}
	}
    if ($fatal=="Y" or $fatal_cn!="") {
		echo "<div class=\"middle form\">
				<div class=\"formContent\">
			";

		echo "<p><font color=red><strong>".$msgstr["recnotupdated"]."</strong></font>";
		$error= $output;
  		echo $output."<p><font color=red><strong>".$msgstr["cwritefile"]. "control_number.cn"."</strong></font>";
		$url= "fmt.php?base=".$arrHttp["base"]."&cipar=".$arrHttp["base"].".par&ValorCapturado=$VC&Opcion=reintentar&Mfn=".$arrHttp["Mfn"]."&error=".urlencode($error)."&Formato=".$arrHttp["Formato"];
		if (isset($arrHttp["wks_a"])) $url.="&wks=".$arrHttp["wks_a"];
		$url.="&ver=N";
		if (isset($arrHttp["db_copies"]))  $url.="&db_copies=".$arrHttp["db_copies"];
		echo "<p><a href=$url><h3>".$msgstr["editar"]."</h3></a>";
		echo "</div></div>";
		die;
	}else{		echo $output;	}
	$IsisScript=$xWxis."actualizar.xis";
  	$query = "&base=".$base ."&cipar=$db_path"."par/".$cipar."&login=".$_SESSION["login"]."&Mfn=" . $arrHttp["Mfn"]."&Opcion=".$arrHttp["Opcion"]."&ValorCapturado=".$ValorCapturado;
	include("../common/wxis_llamar.php");
    $salida="";
	foreach ($contenido as $linea){
		if (substr($linea,0,4)=="MFN:") {
	    	$arrHttp["Mfn"]=trim(substr($linea,4));
		}else{
			if (!empty($linea)) $salida.= $linea."\n";
		}
	}
	return $salida;
}


if(isset($arrHttp["Opcion"]) and $arrHttp["Opcion"]=="crear"){
	$maxmfn=$arrHttp["Mfn"];
	$arrHttp["Maxmfn"]=$maxmfn;
}


?>