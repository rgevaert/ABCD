<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      upload_pft.php
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
$lang=$_SESSION["lang"];
include("../config.php");
include("../lang/dbadmin.php");
include("../lang/soporte.php");

$files = $_FILES['userfile'];
foreach ($files['name'] as $key=>$name) {
	if ($files['size'][$key]) {
      // clean up file name
   		$name = preg_replace("/[^a-z0-9._]/", "",
       	str_replace(" ", "_",
       	str_replace("%20", "_", strtolower($name)
   			)
      		)
        );
      	$fp=file($files['tmp_name'][$key]);
       	$Pft="";
        foreach($fp as $linea) $Pft.=$linea;
	}
}
echo "<script>window.resizeBy(700,400);
window.moveTo(0,0)
function Enviar(){
	window.opener.document.forma1.pft.value=document.forma1.formato.value
	window.opener.toggleLayer('pftedit')
	self.close()
}
</script>\n";

echo "<form name=forma1>". $msgstr["edit"]." ".$msgstr["pft"]."
<br><font color=red>".$msgstr["pftwd1"]." <a href=javascript:Enviar()><img src=../dataentry/img/copy_to_folder.gif border=0></a> ".$msgstr["pftwd2"]."</span>";
echo "<textarea cols=150 rows=40 name=formato>".$Pft."</textarea>";
echo "
<br><font color=red>".$msgstr["pftwd1"]." <a href=javascript:Enviar()><img src=../dataentry/img/copy_to_folder.gif border=0></a> ".$msgstr["pftwd2"]."</span>
</form>
</body>\n";
echo "</html>\n";
?>
