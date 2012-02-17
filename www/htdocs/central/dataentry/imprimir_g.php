<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      imprimir_g.php
 * @desc:      Print G
 * @author:    Guilda Ascencio
 * @since:     20091203
 * @version:   1.0
 *
 * == BEGIN LICENSE ==
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU Lesser General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Lesser General Public License for more details.
 *   
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *   
 * == END LICENSE ==
*/
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../config.php");
include ("../lang/admin.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";

if (isset($arrHttp["Expresion"])){	$arrHttp["Expresion"]=stripslashes($arrHttp["Expresion"]);
	if (strpos('"',$arrHttp["Expresion"])==0) {
    	$arrHttp["Expresion"]=str_replace('"','',$arrHttp["Expresion"]);
	}
	$Expresion=urlencode($arrHttp["Expresion"]);}
if (isset($arrHttp["pft"])) $arrHttp["pft"]=stripslashes($arrHttp["pft"]);


if (isset($arrHttp["pft"]) and trim($arrHttp["pft"])!=""){
	$Formato=urlencode($arrHttp["pft"]);
}else{
	if (isset($arrHttp["fgen"]) and $arrHttp["fgen"]!="") {
		$pft_name=explode('|',trim($arrHttp["fgen"]));
		$arrHttp["fgen"]=$pft_name[0];
		if (isset($pft_name[1]))
			$arrHttp["tipof"]=trim($pft_name[1]);
		else
			$arrHttp["tipof"]="";
		if (strpos($arrHttp["fgen"],'.pft')===false) $arrHttp["fgen"].=".pft";
	    $Formato=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/".$arrHttp["fgen"];
	    if (!file_exists($Formato)){	    	$Formato=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/".$arrHttp["fgen"];	    }
	    if (file_exists($Formato)) $Formato="@".$Formato;
// READ THE HEADINGS, IF ANY
	    if ($arrHttp["tipof"]!=""){	    	$head=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/".$pft_name[0]."_h.txt";
	    	if (!file_exists($head)){
	    		$head=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/".$pft_name[0]."_h.txt";
	    	}
	    	if (file_exists($head)){	    		$fp=file($head);
	    		$arrHttp["headings"]="";
	    		foreach ($fp as $value) {	    			$arrHttp["headings"].=trim($value)."\r";	    		}
	    	}
	    }
	}
}

if (isset($arrHttp["Expresion"])) {
    $Opcion="buscar";
}else{
	$Opcion="rango";
}

if (isset($arrHttp["guardarformato"])){
   	$fp=fopen($db_path.$arrHttp["base"]."/".$arrHttp["Dir"]."/".$arrHttp["nombre"],"w",0);
   	fputs($fp, $arrHttp["pft"]); #write all of $data to our opened file
 		fclose($fp); #close the file
 		$fp=fopen($db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/listados.gen","a",0);

   	fputs($fp, "\n".$arrHttp["nombre"]." ".$arrHttp["descripcion"]); #write all of $data to our opened file
 		fclose($fp); #close the file

}

$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["cipar"]."&Expresion=".$Expresion."&Opcion=$Opcion&Word=S&Formato=".$Formato;
$query.="&from=".$arrHttp["Mfn"]."&to=".$arrHttp["to"];
if (!isset($arrHttp["sortkey"])){
	$IsisScript=$xWxis."imprime.xis";
}else{
	$query.='&sortkey='.urlencode($arrHttp["sortkey"]);
	$IsisScript=$xWxis."sort.xis";
}
include("../common/wxis_llamar.php");
switch ($arrHttp["tipof"]){              //TYPE OF FORMAT
	case "T":  //TABLE
		break;
	case "P":  //PARRAGRAPH
		break;
	case "CT": //COLUMNS (TABLE)
		$data="<table border=1>";
		if (isset($arrHttp["headings"])){			$h=explode("\r",$arrHttp["headings"]);
			foreach ($h as $value){				$data.="<th>$value</th>";			}		}
		break;
	case "CD":
		if (isset($arrHttp["headings"])){
			$h=explode("\r",$arrHttp["headings"]);
			foreach ($h as $value){
				if (trim($value)!=""){
					if ($data==""){						$data=$value;					}else{						$data.="|$value";					}
               }
			}
			$data.="<br>";
		}
		break;}
foreach ($contenido as $linea){
	if (substr($linea,0,6)=='$$REF:'){
	 			$ref=substr($linea,6);
	 			$f=explode(",",$ref);
	 			$bd_ref=$f[0];
	 			$pft_ref=$f[1];
	 			$expr_ref=$f[2];
	 			$IsisScript=$xWxis."buscar.xis";
 				$query = "&cipar=$db_path"."par/".$arrHttp["cipar"]. "&Expresion=".$expr_ref."&Opcion=buscar&base=" .$bd_ref."&Formato=$pft_ref";
				include("../common/wxis_llamar.php");
				foreach($contenido as $linea) $data.= "$linea\n";
	}else{		$data.= $linea."\n" ;	}

}
switch ($arrHttp["vp"]){	case "WP":
    	$filename=$arrHttp["base"].".doc";
		header('Content-Type: application/msword; charset=windows-1252');
		header("Content-Disposition: attachment; filename=\"$filename\"");
   		header("Expires: 0");
   		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
   		header("Pragma: public");
		echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
		echo '   xmlns:w="urn:schemas-microsoft-com:office:word"' . "\n";
		echo '   xmlns="http://www.w3.org/TR/REC-html40">' . "\n";
		break;
	case "TB":
		$filename=$arrHttp["base"].".xls";
		header('Content-Type: application/excel; charset=windows-1252');
		header("Content-Disposition: attachment; filename=\"$filename\"");
   		header("Expires: 0");
   		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
   		header("Pragma: public");
		echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
		echo '   xmlns:w="urn:schemas-microsoft-com:office:word"' . "\n";
		echo '   xmlns="http://www.w3.org/TR/REC-html40">' . "\n";
		break;
	case "TXT":
		$filename=$arrHttp["base"].".txt";
		header('Content-Type: application/excel; charset=windows-1252');
		header("Content-Disposition: attachment; filename=\"$filename\"");
   		header("Expires: 0");
   		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
   		header("Pragma: public");
		break;
	default:
}
   echo $data;
switch ($arrHttp["tipof"]){              //TYPE OF FORMAT
	case "T":  //TABLE
		echo "</body></html>";
		break;
	case "P":  //PARRAGRAPH
		echo "</body></html>";
		break;
	case "CT": //COLUMNS (TABLE)
		echo "</table></body></html>";
		break;
	case "CD":
		break;
}

die;

?>
