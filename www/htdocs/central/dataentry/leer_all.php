<html>

<body>
<font face="courier new">
<?php
global $arrHttp,$xWxis;

// ==================================================================================================
// INICIO DEL PROGRAMA
// ==================================================================================================

//

include("../config.php");

include("../common/get_post.php");

//foreach ($arrHttp as $var => $value) 	echo "$var = $value<br>";

$IsisScript=$xWxis."leer_all.xis";
$query="&base=".$arrHttp["base"]."&cipar=".$arrHttp["cipar"]."&Mfn=".$arrHttp["Mfn"]."&count=1";
include("../common/wxis_llamar.php");
foreach ($contenido as $value) echo "$value";
 ?>
 </body>
 </html>