<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      show.php
 * @desc:      Receive order. List orders
 * @author:    Guilda Ascencio
 * @since:     20091203
 * @version:   1.0.4
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
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../config.php");
$lang=$_SESSION["lang"];

include("../lang/acquisitions.php");
include("../lang/admin.php");


include("../common/get_post.php");

//foreach ($arrHttp as $var=>$value) echo "$var = $value<br>";
if (!isset($arrHttp["Expresion"])) $arrHttp["Expresion"]="";
if (isset($arrHttp["nodiacritics"])){	$archivo=$db_path."acentos.tab";
	$fp=file($archivo);
	if (!$fp){		echo $msgstr["notfound"].": acentos.tab";
		die;	}
	foreach ($fp as $value){		$value=trim($value);
		$a=explode(' ',$value);
		$accent[]=$a[0];
		$noaccent[]=$a[1];	}}

include("../common/header.php");
echo "<body>
<div class=\"middle form\">
	<div class=\"formContent\">
<form name=forma1 method=post action=show.php>
<input type=hidden name=base value=".$arrHttp["base"].">
<input type=hidden name=Expresion value=".urlencode($arrHttp["Expresion"]).">
<input type=hidden name=Opcion value=".$arrHttp["Opcion"].">

	";
if (isset($arrHttp["Formato"])){	$Pft=$arrHttp["Formato"].".pft";}else{	$Pft=$arrHttp["base"].".pft";}
if (!isset($arrHttp["from"])) $arrHttp["from"]=1;
if (!isset($arrHttp["to"])) $arrHttp["to"]="20";
$Formato=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/$Pft" ;
if (!file_exists($Formato)) $Formato=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/$Pft"  ;
if (isset($arrHttp["Mfn"]))
	$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par"."&from=".$arrHttp["Mfn"]."&to=".$arrHttp["Mfn"]."&Formato=@$Formato&Opcion=rango";
else
	$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par"."&Expresion=".urlencode($arrHttp["Expresion"])."&Formato=@$Formato&Total=s&Opcion=buscar&from=".$arrHttp["from"]."&to=".$arrHttp["to"];
if (isset($arrHttp["sort"])){	$IsisScript=$xWxis."sort.xis";
	$query.="&sort=".$arrHttp["sort"];}else{
	$IsisScript=$xWxis."imprime.xis";
}
include("../common/wxis_llamar.php");
$rec=0;
$total=0;
foreach ($contenido as $value){	$value=trim($value);	if (trim($value)!=""){
		if (isset($arrHttp["nodiacritics"]))			$value=str_replace($accent,$noaccent,$value);
		if (substr($value,0,9)=="[RECORD:]") {			$rec=substr($value,9);
			echo "<font color=darkred>".$rec."</font>";		}else{			if (substr($value,0,8)=="[TOTAL:]") {
				$total=substr($value,9);
			}else{
				echo  "$value";
			}
		}
	}
}
$rec++;
echo "<dd><input type=hidden name=from value=".$rec.">";
if (!isset($arrHttp["Mfn"])){	if ($total >$rec){		 echo "<dd><input type=submit value=".$msgstr["continuar"]." onclick=document.forma1.submit()>";
	}
}else{	echo "<p>";}

?>
&nbsp; &nbsp;<input type=submit value=<?php echo $msgstr["cerrar"]?> onclick=self.close()>
</form>
</div>
</div>

</body>
</html>