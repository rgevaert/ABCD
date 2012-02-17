<?php
/**
 * @program:   ABCD - ABCD-Central - http://reddes.bvsaude.org/projects/abcd
 * @copyright:  Copyright (C) 2009 BIREME/PAHO/WHO - VLIR/UOS
 * @file:      changeseq.php
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
require_once ("../lang/admin.php");
include("../common/header.php");
?>
<body>
<div class="middle form">
			<div class="formContent">



<?php


include("../common/get_post.php") ;
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";
// Se ubica el número de control en la lista invertida para ver si no está asignado
$Formato=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/".$arrHttp["base"].".pft" ;
if (!file_exists($Formato)) $Formato=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/".$arrHttp["base"].".pft" ;
$Expresion=$arrHttp["prefix"].$arrHttp["cn"];
$query = "&base=".$arrHttp["base"]."&cipar=$db_path"."par/".$arrHttp["base"].".par"."&Expresion=$Expresion&Formato=@$Formato&Opcion=buscar";
$IsisScript=$xWxis."imprime.xis";
include("../common/wxis_llamar.php");
$cont_database=implode('',$contenido);
if (trim($cont_database)!="") {
	$exist="Y";
}else{
	$exist="N";
}
if ($exist=="N"){
	echo "
	<script>
	window.opener.document.forma1.tag".$arrHttp["tag"].".value=\"".$arrHttp["cn"]."\"
	self.close()
	</script>
	";
}else{
	echo "<h4>".$msgstr["cnexists"]."</h4>";
	echo "<a href=javascript:self.close()>".$msgstr["cerrar"]."</a>&nbsp; &nbsp; &nbsp;";
	echo "<a href=javascript:history.back()>".$msgstr["regresar"]."</a><p>";
	echo "$cont_database";
}

?>
</div><div>
</body>

</html>
