<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      administrar_ex.php
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
include ("../config.php");
include("../lang/dbadmin.php");

function InicializarBd(){
global $arrHttp,$OS,$xWxis,$db_path,$Wxis,$msgstr;
 	$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["cipar"]."&Opcion=".$arrHttp["Opcion"];
 	$IsisScript=$xWxis.$arrHttp["IsisScript"];
 	include("../common/wxis_llamar.php");
	foreach ($contenido as $linea){
	 	if ($linea=="OK"){	 		echo "<h4>".$arrHttp["base"]." ".$msgstr["init"]."</h4>";	 	}
 	}
}

function VerStatus(){
	global $arrHttp,$xWxis,$OS,$db_path,$Wxis;
	$query = "&base=".$arrHttp["base"] . "&cipar=$db_path"."par/".$arrHttp["base"].".par&Opcion=status";
 	$IsisScript=$xWxis."administrar.xis";
 	include("../common/wxis_llamar.php");
 	$ix=-1;
	foreach($contenido as $linea) {
		if (!empty($linea)) {
			$ix++;
			if ($ix>0) {
	  			$a=explode(":",$linea);
	  			$tag[$a[0]]=$a[1];
			}
		}
	}
	return $tag;
}

function Footer(){	echo "</div></div>";
	include("../common/footer.php");
	echo "</body></html>";
	die;}

include("../common/get_post.php");

$encabezado="";
if (isset($arrHttp["encabezado"])) $encabezado="&encabezado=s";
include("../common/header.php");
echo "<body>\n";
if (isset($arrHttp["encabezado"])){
	include("../common/institutional_info.php");
?>
<div class="sectionInfo">

			<div class="breadcrumb">
				<?php echo "<h5>".$msgstr["maintenance"]." " .$msgstr["database"].": ".$arrHttp["base"]."</h5>"?>
			</div>

			<div class="actions">
<?php echo "<a href=\"../common/inicio.php?reinicio=s&base=".$arrHttp["base"]."&encabezado=s\" class=\"defaultButton backButton\">";
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
echo "<font size=1> &nbsp; &nbsp; Script: administrar_ex.php</font><br>";
switch ($arrHttp["Opcion"]) {
    case "inicializar":
    	if (!file_exists($db_path.$arrHttp["base"])){    		echo "<h3>".$db_path.$arrHttp["base"].": ".$msgstr["folderne"]."</h3>";
    		Footer();    	}
    	if (!file_exists($db_path."par/".$arrHttp["base"].".par")){
    		echo "<h3>".$db_path."par/".$arrHttp["base"].".par: ".$msgstr["ne"]."</h3>";
    		Footer();
    	}
    	$arrHttp["IsisScript"]="administrar.xis";
    	$tag=VerStatus();
		if (!isset($arrHttp["borrar"])){
			if ($tag["BD"]!="N"){
				echo "<center><br><span class=td><h4>".$arrHttp["base"]."<br><font color=red>".$msgstr["bdexiste"]."</font><br>".$tag["MAXMFN"]." ".$msgstr["registros"]."<BR>";
				echo "<script>
					if (confirm(\"".$msgstr["elregistros"]." ??\")==true){
						borrarBd=true
					}else{
						borrarBd=false
					}
					if (borrarBd==true){
						if (confirm(\"".$msgstr["seguro"]." ??\")==true){
							borrarBd=true
						}else{
							borrarBd=false
						}
					}
					if (borrarBd==true)
						self.location=\"administrar_ex.php?base=".$arrHttp["base"]."&cipar=".$arrHttp["cipar"]."&Opcion=inicializar&borrar=true$encabezado\"
					</script>";
			}else{

				InicializarBd();
				$arrHttp["Opcion"]="unlockbd";
			}
		}else{
			$arrHttp["IsisScript"]="administrar.xis";
			InicializarBd();
			$fp=fopen($db_path."par/".$arrHttp["base"].".par","r");
			if (!$fp){
				echo $arrHttp["base"].".par"." ".$msgstr["falta"];
				die;
			}
			$fp=file($db_path."par/".$arrHttp["base"].".par");
			foreach($fp as $value){
				$ixpos=strpos($value,'=');
				if ($ixpos===false){
				}else{
					if (substr($value,0,$ixpos)==$arrHttp["base"].".*"){
						$path=trim(substr($value,$ixpos+1));
						$ixpos=strrpos($path, '/');
						$path=substr($path,0,$ixpos)."/";
//						echo "<p>$path<p>";
						break;
					}
				}
			}
			$arrHttp["Opcion"]="unlockbd";
		}
		break;
	case "fullinv":

		$contenido=VerStatus();
		$arrHttp["IsisScript"]="fullinv.xis";
		MostrarPft();
		break;
	case "unlockbd":
		$contenido=VerStatus();
		echo "<p><span class=td>";
		foreach ($contenido as $value) echo "<dd>$value<br>";
		$arrHttp["IsisScript"]="administrar.xis";
		MostrarPft();
		break;
	case "listar":

	case "unlock":
		$contenido=VerStatus();
		$arrHttp["IsisScript"]="administrar.xis";
		echo "<p><span class=td>";
		MostrarPft();
		break;


	case "listadecampos":
		include("adm_listadecampos.php");
		break;
		echo "<html>\n";
		echo "<body>\n";
		echo "<script languaje=javascript>\n";
		echo "function Buscar(Clave){\n";
		echo 'Clave=Clave.substr(0,30)'."\n";
		echo 'Clave="\""+escape(Clave)+"\""'."\n";
		echo '	url="/wxis/wxis.exe?IsisScript=auditoria.xis&Opcion=buscar&Expresion="+Clave+"&Path='.$arrHttp["Path"].'&base='.$arrHttp["base"]."&cipar=".$arrHttp["base"].".par\"\n";
		echo '  msgwin=window.open(url,"Window2","status=yes,resizable=yes,toolbar=no,menu=yes,scrollbars=yes,width=600,height=400,top=50,left=50")'."\n";
		echo '  msgwin.focus()'."\n";
		echo "}\n";
		echo "</script>\n";
		$arrHttp["IsisScript"]="auditoria.xis";
		LeerRegistro();
		break;


}
if (!isset($arrHttp["encabezado"])){
	if ($arrHttp["Opcion"]!="fullinv")
 		echo "<p><center><a href=index.php?base=".$arrHttp["base"]." class=boton> &nbsp; &nbsp; Menu &nbsp; &nbsp; </a>";
}
?>
