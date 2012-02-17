<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      fmt_update.php
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
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../config.php");
$lang=$_SESSION["lang"];

include("../lang/dbadmin.php");
if (!isset($_SESSION["permiso"])) die;
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
//die;
$fp=fopen($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["nombre"].".fmt","w",0);
if (!$fp){
	echo $arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["nombre"].".fmt"." ";
	echo $msgstr["nopudoseractualizado"];
	die;
}
if (isset($arrHttp["wks"])){
	$fmt=ConstruyeWorksheetFMT();
	foreach ($fmt as $value){
	    $value=trim($value);
	    if ($value!="")
			$res=fwrite($fp,$value."\n");

	}
	fclose($fp); #close the file
}

if (file_exists(($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/formatos.wks"))){
	$fp=file($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/formatos.wks");
}else{
	if (file_exists(($db_path.$arrHttp["base"]."/def/".$lang_db."/formatos.wks")))
		$fp=file($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/formatos.wks");
}
$fex="N";
if (isset($arrHttp["fmt"])){
	$name=explode('|',$arrHttp["fmt"]);
	$arrHttp["nombre"]=trim($name[0]);
}
if ($fp){
	foreach ($fp as $linea){
		$linea=trim($linea);
		if ($linea!=""){
			$l=explode('|',$linea);
			if (isset($arrHttp["fmt"])) $arrHttp["descripcion"]=$l[1];
			if (trim($l[0])==trim($arrHttp["nombre"])){
				$fex="S";
				$lfmt=$l[0].'|'.trim($arrHttp["descripcion"]);
				if (isset($arrHttp["sel_oper"])) $lfmt.='|'.$arrHttp["sel_oper"];
				$salida[]=$lfmt;
			}else{
				$salida[]=$linea;
			}
		}
	}
}

//IF IS A NEW FORMAT
if ($fex=="N"){
	$wks=$arrHttp["nombre"].'|'.$arrHttp["descripcion"]."|";
	if (isset($arrHttp["sel_oper"])) $wks.=$arrHttp["sel_oper"];
	$salida[]=$wks;
}
$fp=fopen($db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/formatos.wks","w");
foreach ($salida as $arch) $res=fwrite($fp,$arch."\n");
fclose($fp);
include("../common/header.php");

echo "<body>\n";
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
	$encabezado="&encabezado=s";
}else{
	$encabezado="";
}
?>
<div class="sectionInfo">
	<div class="breadcrumb">
<?php echo $msgstr["fmt"].": ". $arrHttp["nombre"]."-".$arrHttp["descripcion"]." (".$arrHttp["base"].")"?>
	</div>

	<div class="actions">
<?php if ($arrHttp["Opcion"]=="new"){
	echo "<a href=\"../common/inicio.php?reinicio=s\" class=\"defaultButton cancelButton\">";
}else{
	echo "<a href=\"fmt.php?base=".$arrHttp["base"]."$encabezado\" class=\"defaultButton backButton\">";
}
?>
<img src="../images/defaultButton_iconBorder.gif" alt="" title="" />
<span><strong><?php echo $msgstr["back"]?></strong></span>
</a>
			</div>
			<div class="spacer">&#160;</div>
</div>
<div class="helper">
<?php echo "<font color=white>&nbsp; &nbsp; Script: fmt_update.php" ?></font>
	</div>
<div class="middle form">
			<div class="formContent">
<center><h4>
<?php echo $msgstr["fmtupdated"]?></h4>

		</TD>
</table>
</center>
</div></div>
<?php include("../common/footer.php");?>
</body>
</html>

<?php
function ConstruyeWorksheetFMT(){
global $arrHttp,$vars,$db_path,$lang_db;
	$base=$arrHttp["base"];
	$fpDb_fdt = $db_path.$base."/def/".$_SESSION["lang"]."/"."$base.fdt";
	if (!file_exists($fpDb_fdt)) {
		$fpDb_fdt = $db_path.$base."/def/".$lang_db."/"."$base.fdt";
		if (!file_exists($fpDb_fdt)){
   			echo $db_path.$base."/def/".$_SESSION["lang"]."/$base.fdt".": ".$msgstr["ne"];
			die;
		}
	}
	$fpDb=file($fpDb_fdt);
	// se lee la estructura de la base de datos (dbn.fdt)
	foreach ($fpDb as $var=>$value){
		$base_fdt[]=$value;
	}
	$fp=explode("\n",$arrHttp["wks"]);
	$ix=-1;
	foreach ($fp as $value){
		$value=trim($value);
		if ($value!=""){
			unset($tx);
			$tx=explode('|',$value) ;
			if (count($tx)>1) {
				$ix++;
				$vars[$ix]=$value;
				reset($base_fdt);
				$entro="";
				$primeravez="S";
				foreach ($base_fdt as $lin){ //COPY THE SUBFIELD OF THE SELECTED FIELD, IF ANY
					$vx=explode('|',$lin);
					if ($vx[1]==$tx[1] or $entro=="S"){
						$entro="S";
						if (trim($vx[0])!="S" ){       //S IN THE FIRST COLUMN INDICATES SUBFIELD
							if ($primeravez=="S"){
								$primeravez="";
							}else{
								break;
							}
						}else{
							$ix++;
							$vars[$ix]=$lin;
						}
					}
				}
			}
		}
	}
	return $vars;
}
?>

